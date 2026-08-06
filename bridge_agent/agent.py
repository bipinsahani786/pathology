#!/usr/bin/env python3
"""
Lab Machine Bridge Agent v1.0
================================
Connects lab machines (via Serial COM port or TCP/IP) to the cloud pathology software.

Supported Machines:
  - SRP Healthcare Analyzer    (Serial / ASTM)
  - AURA Chem 120              (TCP / ASTM)
  - Cellomax 5                 (TCP / Key=Value CBC)
  - ST-200 Plus                (Serial / Key=Value Electrolyte)

Usage:
  1. Edit config.json with your machine details and cloud API token
  2. Run: python agent.py
  3. Or as Windows service: python agent.py --install-service

Requirements:
  pip install pyserial requests
"""

import json
import logging
import os
import re
import socket
import sys
import threading
import time
from datetime import datetime
from pathlib import Path

try:
    import serial
except ImportError:
    serial = None

try:
    import requests
except ImportError:
    print("ERROR: 'requests' library not found. Run: pip install requests pyserial")
    sys.exit(1)

# ─────────────────────────────────────────────────────────────────────────────
# LOGGING SETUP
# ─────────────────────────────────────────────────────────────────────────────
LOG_FILE = Path(__file__).parent / "bridge_agent.log"
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s [%(levelname)s] %(message)s",
    handlers=[
        logging.FileHandler(LOG_FILE, encoding="utf-8"),
        logging.StreamHandler(sys.stdout),
    ],
)
log = logging.getLogger("BridgeAgent")


# ─────────────────────────────────────────────────────────────────────────────
# CONFIG LOADER
# ─────────────────────────────────────────────────────────────────────────────
DEFAULT_CONFIG = {
    "cloud_url": "https://YOUR_APP_URL.com",
    "heartbeat_interval_seconds": 60,
    "machines": [
        {
            "id": 1,
            "name": "Cellomax 5",
            "machine_type": "hematology",
            "connection_type": "tcp",
            "host": "192.168.1.50",
            "port": 4001,
            "api_token": "mach_YOUR_TOKEN_HERE",
            "protocol": "custom",
            "enabled": True
        },
        {
            "id": 2,
            "name": "AURA Chem 120",
            "machine_type": "biochemistry",
            "connection_type": "tcp",
            "host": "192.168.1.55",
            "port": 4002,
            "api_token": "mach_YOUR_TOKEN_HERE",
            "protocol": "astm",
            "enabled": True
        },
        {
            "id": 3,
            "name": "SRP Healthcare Analyzer",
            "machine_type": "biochemistry",
            "connection_type": "serial",
            "com_port": "COM1",
            "baud_rate": 9600,
            "api_token": "mach_YOUR_TOKEN_HERE",
            "protocol": "astm",
            "enabled": True
        },
        {
            "id": 4,
            "name": "ST-200 Plus",
            "machine_type": "electrolyte",
            "connection_type": "serial",
            "com_port": "COM3",
            "baud_rate": 9600,
            "api_token": "mach_YOUR_TOKEN_HERE",
            "protocol": "custom",
            "enabled": True
        }
    ]
}


def load_config() -> dict:
    config_file = Path(__file__).parent / "config.json"
    if not config_file.exists():
        log.warning("config.json not found. Creating default config...")
        config_file.write_text(json.dumps(DEFAULT_CONFIG, indent=2))
        log.info(f"Default config created at: {config_file}")
        log.info("PLEASE EDIT config.json with your machine details and API tokens!")
        sys.exit(0)
    return json.loads(config_file.read_text())


# ─────────────────────────────────────────────────────────────────────────────
# PARSERS
# ─────────────────────────────────────────────────────────────────────────────
def parse_astm(raw: str) -> dict:
    """Parse ASTM E1394 message into structured data."""
    result = {"sample_id": None, "results": {}}
    lines = re.split(r"[\r\n]+", raw.strip())

    for line in lines:
        line = line.strip()
        if not line:
            continue

        record_type = line[0] if line else ""
        fields = line.split("|")

        if record_type == "P":
            result["patient_id"] = (fields[2] if len(fields) > 2 else "").strip()

        elif record_type == "O":
            if len(fields) > 2 and fields[2].strip():
                result["sample_id"] = fields[2].strip()

        elif record_type == "R":
            param_raw = fields[2] if len(fields) > 2 else ""
            value = (fields[3] if len(fields) > 3 else "").strip()
            unit = (fields[4] if len(fields) > 4 else "").strip()

            # Extract param name from ^^^PARAM format
            param_parts = param_raw.split("^")
            param_name = ""
            for part in reversed(param_parts):
                part = part.strip()
                if part:
                    param_name = part.upper()
                    break

            if param_name and value:
                result["results"][param_name] = value

    return result


def parse_key_value(raw: str) -> dict:
    """Parse KEY=VALUE,KEY=VALUE format (Cellomax 5, ST-200 Plus)."""
    result = {"sample_id": None, "results": {}}
    pairs = re.split(r"[,;\s]+", raw.strip())

    for pair in pairs:
        if "=" not in pair:
            continue
        key, value = pair.split("=", 1)
        key = key.strip().upper()
        value = value.strip()

        if key in ("SID", "SAMPLEID", "ACCESSION", "BARCODE", "ID"):
            result["sample_id"] = value
        elif value:
            result["results"][key] = value

    return result


def parse_raw(raw: str, protocol: str, machine_type: str) -> dict:
    """Choose the right parser based on protocol / machine type."""
    if protocol == "astm" or machine_type == "biochemistry":
        return parse_astm(raw)
    else:
        return parse_key_value(raw)


# ─────────────────────────────────────────────────────────────────────────────
# CLOUD API SENDER
# ─────────────────────────────────────────────────────────────────────────────
class CloudSender:
    def __init__(self, cloud_url: str):
        self.cloud_url = cloud_url.rstrip("/")
        self.session = requests.Session()
        self.session.timeout = 10

    def push_raw(self, token: str, raw_data: str) -> dict:
        """Send raw ASTM/custom data to cloud (server does the parsing)."""
        try:
            resp = self.session.post(
                f"{self.cloud_url}/api/machine/push-raw",
                headers={"Authorization": f"Bearer {token}", "Content-Type": "application/json"},
                json={"raw_data": raw_data},
            )
            resp.raise_for_status()
            return resp.json()
        except Exception as e:
            log.error(f"push_raw failed: {e}")
            return {"error": str(e)}

    def push_result(self, token: str, sample_id: str, results: dict) -> dict:
        """Send already-parsed key-value results to cloud."""
        try:
            resp = self.session.post(
                f"{self.cloud_url}/api/machine/push-result",
                headers={"Authorization": f"Bearer {token}", "Content-Type": "application/json"},
                json={"sample_id": sample_id, "results": results},
            )
            resp.raise_for_status()
            return resp.json()
        except Exception as e:
            log.error(f"push_result failed: {e}")
            return {"error": str(e)}

    def heartbeat(self, token: str, machine_name: str) -> bool:
        """Send heartbeat to mark machine as online."""
        try:
            resp = self.session.post(
                f"{self.cloud_url}/api/machine/heartbeat",
                headers={"Authorization": f"Bearer {token}"},
                timeout=5,
            )
            return resp.status_code == 200
        except Exception:
            return False


# ─────────────────────────────────────────────────────────────────────────────
# MACHINE LISTENERS
# ─────────────────────────────────────────────────────────────────────────────
class SerialListener(threading.Thread):
    """Listens on a COM port and sends data to cloud."""

    def __init__(self, machine: dict, sender: CloudSender):
        super().__init__(daemon=True, name=f"Serial-{machine['name']}")
        self.machine = machine
        self.sender = sender
        self.running = True

    def run(self):
        if serial is None:
            log.error(f"[{self.machine['name']}] pyserial not installed. Run: pip install pyserial")
            return

        com_port = self.machine.get("com_port", "COM1")
        baud_rate = self.machine.get("baud_rate", 9600)

        log.info(f"[{self.machine['name']}] Starting Serial listener on {com_port} @ {baud_rate} baud")

        while self.running:
            try:
                with serial.Serial(
                    port=com_port,
                    baudrate=baud_rate,
                    bytesize=serial.EIGHTBITS,
                    parity=serial.PARITY_NONE,
                    stopbits=serial.STOPBITS_ONE,
                    timeout=1,
                ) as ser:
                    log.info(f"[{self.machine['name']}] {com_port} opened. Waiting for data...")
                    buffer = ""

                    while self.running:
                        raw = ser.read(4096).decode("ascii", errors="ignore")
                        if not raw:
                            continue

                        buffer += raw
                        log.debug(f"[{self.machine['name']}] Received {len(raw)} bytes")

                        # ASTM: complete message ends with L record
                        # Custom: message ends with newline or timeout
                        if self._is_complete_message(buffer):
                            self._process(buffer.strip())
                            buffer = ""

            except serial.SerialException as e:
                log.warning(f"[{self.machine['name']}] Serial error: {e}. Retrying in 5s...")
                time.sleep(5)
            except Exception as e:
                log.error(f"[{self.machine['name']}] Unexpected error: {e}")
                time.sleep(5)

    def _is_complete_message(self, buffer: str) -> bool:
        protocol = self.machine.get("protocol", "custom")
        if protocol == "astm":
            return "L|" in buffer  # ASTM terminator record
        return "\n" in buffer or len(buffer) > 2048

    def _process(self, raw: str):
        log.info(f"[{self.machine['name']}] Processing: {raw[:80]}...")
        try:
            parsed = parse_raw(raw, self.machine.get("protocol", "custom"), self.machine.get("machine_type", ""))
            sample_id = parsed.get("sample_id")
            results = parsed.get("results", {})

            if results:
                resp = self.sender.push_result(self.machine["api_token"], sample_id, results)
                log.info(f"[{self.machine['name']}] Cloud response: {resp.get('status')} | Sample: {sample_id} | {len(results)} params")
            else:
                log.warning(f"[{self.machine['name']}] No results parsed from message")
        except Exception as e:
            log.error(f"[{self.machine['name']}] Process error: {e}")


class TcpListener(threading.Thread):
    """Connects to machine's TCP socket and reads data."""

    def __init__(self, machine: dict, sender: CloudSender):
        super().__init__(daemon=True, name=f"TCP-{machine['name']}")
        self.machine = machine
        self.sender = sender
        self.running = True

    def run(self):
        host = self.machine.get("host", "")
        port = self.machine.get("port", 4001)

        log.info(f"[{self.machine['name']}] Starting TCP listener → {host}:{port}")

        while self.running:
            try:
                with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as sock:
                    sock.settimeout(30)
                    sock.connect((host, port))
                    log.info(f"[{self.machine['name']}] Connected to {host}:{port}")

                    buffer = ""
                    while self.running:
                        try:
                            chunk = sock.recv(4096).decode("ascii", errors="ignore")
                            if not chunk:
                                log.warning(f"[{self.machine['name']}] Connection closed by machine")
                                break
                            buffer += chunk

                            if self._is_complete(buffer):
                                self._process(buffer.strip())
                                buffer = ""
                        except socket.timeout:
                            # Send heartbeat to keep connection alive
                            continue

            except (socket.error, ConnectionRefusedError) as e:
                log.warning(f"[{self.machine['name']}] TCP error: {e}. Retrying in 10s...")
                time.sleep(10)
            except Exception as e:
                log.error(f"[{self.machine['name']}] Unexpected: {e}")
                time.sleep(10)

    def _is_complete(self, buffer: str) -> bool:
        protocol = self.machine.get("protocol", "custom")
        if protocol == "astm":
            return "L|" in buffer
        return "\n" in buffer or len(buffer) > 4096

    def _process(self, raw: str):
        log.info(f"[{self.machine['name']}] Processing TCP data: {raw[:80]}...")
        try:
            parsed = parse_raw(raw, self.machine.get("protocol", "custom"), self.machine.get("machine_type", ""))
            sample_id = parsed.get("sample_id")
            results = parsed.get("results", {})

            if results:
                resp = self.sender.push_result(self.machine["api_token"], sample_id, results)
                log.info(f"[{self.machine['name']}] Cloud response: {resp.get('status')} | {len(results)} params")
            else:
                log.warning(f"[{self.machine['name']}] No results parsed")
        except Exception as e:
            log.error(f"[{self.machine['name']}] Process error: {e}")


# ─────────────────────────────────────────────────────────────────────────────
# HEARTBEAT WORKER
# ─────────────────────────────────────────────────────────────────────────────
class HeartbeatWorker(threading.Thread):
    """Sends periodic heartbeat for each machine to cloud."""

    def __init__(self, machines: list, sender: CloudSender, interval: int = 60):
        super().__init__(daemon=True, name="HeartbeatWorker")
        self.machines = machines
        self.sender = sender
        self.interval = interval

    def run(self):
        while True:
            for m in self.machines:
                if m.get("enabled"):
                    ok = self.sender.heartbeat(m["api_token"], m["name"])
                    status = "✅" if ok else "❌"
                    log.debug(f"Heartbeat {status} {m['name']}")
            time.sleep(self.interval)


# ─────────────────────────────────────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────────────────────────────────────
def main():
    print("=" * 60)
    print("  🔬 Lab Machine Bridge Agent v1.0")
    print("  Connecting lab machines to cloud pathology software")
    print("=" * 60)

    config = load_config()
    cloud_url = config.get("cloud_url", "")
    if not cloud_url or "YOUR_APP_URL" in cloud_url:
        log.error("Please set 'cloud_url' in config.json to your app URL!")
        sys.exit(1)

    sender = CloudSender(cloud_url)
    machines = [m for m in config.get("machines", []) if m.get("enabled")]

    if not machines:
        log.error("No enabled machines found in config.json")
        sys.exit(1)

    log.info(f"Starting {len(machines)} machine listener(s) → {cloud_url}")

    threads = []

    # Start heartbeat
    hb = HeartbeatWorker(machines, sender, config.get("heartbeat_interval_seconds", 60))
    hb.start()
    threads.append(hb)

    # Start listener for each machine
    for machine in machines:
        conn_type = machine.get("connection_type", "serial")
        try:
            if conn_type == "tcp":
                listener = TcpListener(machine, sender)
            else:
                listener = SerialListener(machine, sender)

            listener.start()
            threads.append(listener)
            log.info(f"✅ Started: {machine['name']} [{conn_type.upper()}]")
        except Exception as e:
            log.error(f"❌ Failed to start {machine['name']}: {e}")

    print("\nBridge Agent running. Press Ctrl+C to stop.\n")

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        log.info("Shutting down Bridge Agent...")
        for t in threads:
            if hasattr(t, "running"):
                t.running = False
        log.info("Stopped.")


if __name__ == "__main__":
    main()

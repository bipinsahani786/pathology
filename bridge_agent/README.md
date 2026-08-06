# 🔬 Lab Machine Bridge Agent

Connects your lab machines to the cloud pathology software.

## Supported Machines
| Machine | Type | Connection |
|---------|------|-----------|
| SRP Healthcare Analyzer | Biochemistry (ASTM) | Serial / COM port |
| AURA Chem 120 | Biochemistry (ASTM) | TCP/IP |
| Cellomax 5 | Hematology CBC 5-Part | TCP/IP |
| ST-200 Plus | Electrolyte (Na/K/Cl) | Serial / COM port |

---

## Option 1 — Python 🐍

**Requirements:** Python 3.8+

```bash
pip install pyserial requests
python agent.py
```

## Option 2 — Node.js 🟩

**Requirements:** Node.js 18+

```bash
npm install
node agent.js
```

---

## Setup

1. **Add machines** in your pathology app → Settings → Machines
2. **Copy the API Token** for each machine
3. **Edit `config.json`:**
   - Set `cloud_url` to your app URL (e.g. `https://yourlabapp.com`)
   - Set `api_token` for each machine (paste from Settings page)
   - Set `com_port` (e.g. `COM3`) for serial machines
   - Set `host` + `port` for TCP machines
4. **Run the agent**
5. Machine data will auto-appear on the Result Entry page! ✅

---

## config.json Reference

```json
{
  "cloud_url": "https://yourlabapp.com",
  "heartbeat_interval_seconds": 60,
  "machines": [
    {
      "name": "Cellomax 5",
      "machine_type": "hematology",
      "connection_type": "tcp",
      "host": "192.168.1.50",
      "port": 4001,
      "api_token": "mach_xxxxxxxxxxxx",
      "protocol": "custom",
      "enabled": true
    },
    {
      "name": "SRP Analyzer",
      "machine_type": "biochemistry",
      "connection_type": "serial",
      "com_port": "COM1",
      "baud_rate": 9600,
      "api_token": "mach_xxxxxxxxxxxx",
      "protocol": "astm",
      "enabled": true
    }
  ]
}
```

---

## Logs

Both agents write logs to `bridge_agent.log` in the same folder.

## Finding COM Port (Windows)
Device Manager → Ports (COM & LPT) → look for your machine

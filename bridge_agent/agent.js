#!/usr/bin/env node
/**
 * Lab Machine Bridge Agent v1.0 — Node.js Version
 * =================================================
 * Connects lab machines (COM port / TCP) to cloud pathology software.
 *
 * Supported Machines:
 *   - SRP Healthcare Analyzer    (Serial / ASTM)
 *   - AURA Chem 120              (TCP / ASTM)
 *   - Cellomax 5                 (TCP / Key=Value CBC)
 *   - ST-200 Plus                (Serial / Key=Value Electrolyte)
 *
 * Requirements:
 *   npm install serialport axios
 *
 * Usage:
 *   node agent.js
 *
 * First Run:
 *   node agent.js   ← creates config.json if missing, then edit it
 */

'use strict';

const fs   = require('fs');
const net  = require('net');
const path = require('path');
const http = require('https'); // for axios fallback

// ─── Dependency check ───────────────────────────────────────────────────────
let axios;
try {
  axios = require('axios');
} catch {
  console.error('\n❌ "axios" not found. Run:\n   npm install axios serialport\n');
  process.exit(1);
}

let SerialPort;
try {
  SerialPort = require('serialport').SerialPort;
} catch {
  // serialport is optional — serial machines won't work without it
  SerialPort = null;
}

// ─── Logging ────────────────────────────────────────────────────────────────
const LOG_FILE = path.join(__dirname, 'bridge_agent.log');

function log(level, machine, msg) {
  const ts   = new Date().toISOString();
  const line = `[${ts}] [${level.toUpperCase()}] [${machine}] ${msg}`;
  console.log(line);
  fs.appendFileSync(LOG_FILE, line + '\n');
}

const info  = (m, msg) => log('INFO',  m, msg);
const warn  = (m, msg) => log('WARN',  m, msg);
const error = (m, msg) => log('ERROR', m, msg);
const debug = (m, msg) => log('DEBUG', m, msg);

// ─── Config ──────────────────────────────────────────────────────────────────
const CONFIG_FILE = path.join(__dirname, 'config.json');

const DEFAULT_CONFIG = {
  cloud_url: 'https://YOUR_APP_DOMAIN.com',
  heartbeat_interval_seconds: 60,
  machines: [
    {
      id: 1, name: 'Cellomax 5', machine_type: 'hematology',
      connection_type: 'tcp', host: '192.168.1.50', port: 4001,
      api_token: 'PASTE_TOKEN_HERE', protocol: 'custom', enabled: true,
    },
    {
      id: 2, name: 'AURA Chem 120', machine_type: 'biochemistry',
      connection_type: 'tcp', host: '192.168.1.55', port: 4002,
      api_token: 'PASTE_TOKEN_HERE', protocol: 'astm', enabled: true,
    },
    {
      id: 3, name: 'SRP Healthcare Analyzer', machine_type: 'biochemistry',
      connection_type: 'serial', com_port: 'COM1', baud_rate: 9600,
      api_token: 'PASTE_TOKEN_HERE', protocol: 'astm', enabled: true,
    },
    {
      id: 4, name: 'ST-200 Plus', machine_type: 'electrolyte',
      connection_type: 'serial', com_port: 'COM3', baud_rate: 9600,
      api_token: 'PASTE_TOKEN_HERE', protocol: 'custom', enabled: true,
    },
  ],
};

function loadConfig() {
  if (!fs.existsSync(CONFIG_FILE)) {
    fs.writeFileSync(CONFIG_FILE, JSON.stringify(DEFAULT_CONFIG, null, 2));
    console.log('\n✅ config.json created. Please edit it with your machine details and API tokens, then run again.\n');
    process.exit(0);
  }
  return JSON.parse(fs.readFileSync(CONFIG_FILE, 'utf8'));
}

// ─── Parsers ─────────────────────────────────────────────────────────────────

/**
 * Parse ASTM E1394 message.
 * Returns { sample_id, results: { PARAM: value } }
 */
function parseAstm(raw) {
  const result = { sample_id: null, results: {} };
  const lines  = raw.split(/[\r\n]+/);

  for (const line of lines) {
    if (!line.trim()) continue;
    const fields     = line.split('|');
    const recordType = line[0];

    if (recordType === 'O') {
      if (fields[2] && fields[2].trim()) {
        result.sample_id = fields[2].trim();
      }
    } else if (recordType === 'R') {
      const paramRaw = fields[2] || '';
      const value    = (fields[3] || '').trim();
      const parts    = paramRaw.split('^');
      let paramName  = '';
      for (let i = parts.length - 1; i >= 0; i--) {
        if (parts[i].trim()) { paramName = parts[i].trim().toUpperCase(); break; }
      }
      if (paramName && value) {
        result.results[paramName] = value;
      }
    }
  }
  return result;
}

/**
 * Parse KEY=VALUE format (Cellomax 5, ST-200 Plus).
 */
function parseKeyValue(raw) {
  const result = { sample_id: null, results: {} };
  const pairs  = raw.trim().split(/[,;\s]+/);

  for (const pair of pairs) {
    if (!pair.includes('=')) continue;
    const [k, v]  = pair.split('=');
    const key     = (k || '').trim().toUpperCase();
    const value   = (v || '').trim();

    if (['SID', 'SAMPLEID', 'ACCESSION', 'BARCODE', 'ID'].includes(key)) {
      result.sample_id = value;
    } else if (value) {
      result.results[key] = value;
    }
  }
  return result;
}

function parseRaw(raw, protocol, machineType) {
  if (protocol === 'astm' || machineType === 'biochemistry') {
    return parseAstm(raw);
  }
  return parseKeyValue(raw);
}

function isCompleteMessage(buffer, protocol) {
  if (protocol === 'astm') return buffer.includes('L|');
  return buffer.includes('\n') || buffer.length > 4096;
}

// ─── Cloud Sender ────────────────────────────────────────────────────────────
class CloudSender {
  constructor(cloudUrl) {
    this.cloudUrl = cloudUrl.replace(/\/$/, '');
    this.client   = axios.create({
      baseURL: this.cloudUrl,
      timeout: 10000,
    });
  }

  async pushResult(token, sampleId, results) {
    try {
      const resp = await this.client.post('/api/machine/push-result',
        { sample_id: sampleId, results },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      return resp.data;
    } catch (e) {
      return { error: e.message };
    }
  }

  async pushRaw(token, rawData) {
    try {
      const resp = await this.client.post('/api/machine/push-raw',
        { raw_data: rawData },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      return resp.data;
    } catch (e) {
      return { error: e.message };
    }
  }

  async heartbeat(token) {
    try {
      await this.client.post('/api/machine/heartbeat', {},
        { headers: { Authorization: `Bearer ${token}` }, timeout: 5000 }
      );
      return true;
    } catch {
      return false;
    }
  }
}

// ─── TCP Listener ────────────────────────────────────────────────────────────
function startTcpListener(machine, sender) {
  const { name, host, port, api_token, protocol, machine_type } = machine;

  function connect() {
    info(name, `Connecting TCP → ${host}:${port}`);
    const sock = new net.Socket();
    let buffer = '';

    sock.connect(port, host, () => {
      info(name, `✅ Connected to ${host}:${port}`);
    });

    sock.on('data', async (chunk) => {
      buffer += chunk.toString('ascii');
      debug(name, `Received ${chunk.length} bytes`);

      if (isCompleteMessage(buffer, protocol)) {
        const raw = buffer.trim();
        buffer    = '';
        await processData(name, raw, protocol, machine_type, api_token, sender);
      }
    });

    sock.on('error', (e) => {
      warn(name, `TCP error: ${e.message}. Reconnecting in 10s...`);
    });

    sock.on('close', () => {
      warn(name, 'Connection closed. Reconnecting in 10s...');
      setTimeout(connect, 10000);
    });
  }

  connect();
}

// ─── Serial Listener ─────────────────────────────────────────────────────────
function startSerialListener(machine, sender) {
  const { name, com_port, baud_rate, api_token, protocol, machine_type } = machine;

  if (!SerialPort) {
    error(name, '"serialport" package not installed. Run: npm install serialport');
    return;
  }

  info(name, `Opening Serial → ${com_port} @ ${baud_rate || 9600} baud`);

  let buffer = '';

  function openPort() {
    try {
      const port = new SerialPort({
        path:     com_port,
        baudRate: baud_rate || 9600,
        dataBits: 8,
        parity:   'none',
        stopBits: 1,
      });

      port.on('open', () => {
        info(name, `✅ ${com_port} opened. Waiting for data...`);
      });

      port.on('data', async (chunk) => {
        buffer += chunk.toString('ascii');

        if (isCompleteMessage(buffer, protocol)) {
          const raw = buffer.trim();
          buffer    = '';
          await processData(name, raw, protocol, machine_type, api_token, sender);
        }
      });

      port.on('error', (e) => {
        warn(name, `Serial error: ${e.message}. Retrying in 5s...`);
        setTimeout(openPort, 5000);
      });

      port.on('close', () => {
        warn(name, 'Port closed. Retrying in 5s...');
        setTimeout(openPort, 5000);
      });
    } catch (e) {
      warn(name, `Cannot open ${com_port}: ${e.message}. Retrying in 5s...`);
      setTimeout(openPort, 5000);
    }
  }

  openPort();
}

// ─── Data Processor ──────────────────────────────────────────────────────────
async function processData(name, raw, protocol, machineType, token, sender) {
  info(name, `Processing: ${raw.substring(0, 80)}...`);
  try {
    const parsed   = parseRaw(raw, protocol, machineType);
    const sampleId = parsed.sample_id;
    const results  = parsed.results;

    if (Object.keys(results).length === 0) {
      warn(name, 'No results parsed from message');
      return;
    }

    const resp = await sender.pushResult(token, sampleId, results);
    info(name, `Cloud: ${resp.status || resp.error} | Sample: ${sampleId} | ${Object.keys(results).length} params`);
  } catch (e) {
    error(name, `Process error: ${e.message}`);
  }
}

// ─── Heartbeat Worker ────────────────────────────────────────────────────────
function startHeartbeat(machines, sender, intervalSecs) {
  setInterval(async () => {
    for (const m of machines) {
      if (!m.enabled) continue;
      const ok = await sender.heartbeat(m.api_token);
      debug(m.name, `Heartbeat ${ok ? '✅' : '❌'}`);
    }
  }, intervalSecs * 1000);
}

// ─── Main ────────────────────────────────────────────────────────────────────
function main() {
  console.log('='.repeat(60));
  console.log('  🔬 Lab Machine Bridge Agent v1.0 — Node.js');
  console.log('  Connecting lab machines to cloud pathology software');
  console.log('='.repeat(60));

  const config   = loadConfig();
  const cloudUrl = config.cloud_url || '';

  if (!cloudUrl || cloudUrl.includes('YOUR_APP_DOMAIN')) {
    console.error('\n❌ Please set "cloud_url" in config.json to your app URL!\n');
    process.exit(1);
  }

  const sender   = new CloudSender(cloudUrl);
  const machines = (config.machines || []).filter(m => m.enabled);

  if (machines.length === 0) {
    console.error('\n❌ No enabled machines found in config.json\n');
    process.exit(1);
  }

  console.log(`\n🚀 Starting ${machines.length} machine listener(s) → ${cloudUrl}\n`);

  // Heartbeat
  startHeartbeat(machines, sender, config.heartbeat_interval_seconds || 60);

  // Machine listeners
  for (const machine of machines) {
    if (machine.connection_type === 'tcp') {
      startTcpListener(machine, sender);
    } else {
      startSerialListener(machine, sender);
    }
    console.log(`  ✅ ${machine.name} [${machine.connection_type.toUpperCase()}]`);
  }

  console.log('\nBridge Agent running. Press Ctrl+C to stop.\n');

  // Keep alive
  process.on('SIGINT', () => {
    console.log('\nShutting down...');
    process.exit(0);
  });
}

main();

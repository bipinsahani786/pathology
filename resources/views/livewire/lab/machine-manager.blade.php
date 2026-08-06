<div>
    <div class="nxl-content">
        {{-- Page Header --}}
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Machine Integration</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item">Machines</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <button wire:click="openCreate" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                    <i class="feather-plus me-2"></i>Add Machine
                </button>
            </div>
        </div>

        <div class="main-content">

            {{-- Info Banner --}}
            <div class="alert alert-soft-info border-0 rounded-3 mb-4 d-flex align-items-start gap-3">
                <i class="feather-info text-info mt-1" style="font-size:1.2rem"></i>
                <div>
                    <strong>How it works:</strong>
                    Add your lab machines here → copy the API Token → install the Bridge Agent on your lab PC → results will auto-fill on the Result Entry page.
                    <span class="text-primary fw-semibold">No machine? Use the 🧪 Simulator to test the full flow.</span>
                </div>
            </div>

            {{-- Machine Cards --}}
            @if(count($machines) === 0)
                <div class="text-center py-5">
                    <i class="feather-cpu text-muted" style="font-size:3rem"></i>
                    <p class="text-muted mt-3">No machines added yet. Click <strong>Add Machine</strong> to get started.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($machines as $m)
                        @php
                            $isOnline = $m['last_seen_at'] && \Carbon\Carbon::parse($m['last_seen_at'])->diffInMinutes(now()) <= 10;
                            $lastSeen = $m['last_seen_at'] ? \Carbon\Carbon::parse($m['last_seen_at'])->diffForHumans() : null;
                            $typeIcon = match($m['machine_type']) {
                                'hematology'  => '🩸',
                                'electrolyte' => '⚗️',
                                default       => '🔬',
                            };
                            $typeLabel = match($m['machine_type']) {
                                'hematology'  => 'Hematology (CBC)',
                                'electrolyte' => 'Electrolyte',
                                default       => 'Biochemistry',
                            };
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid {{ $isOnline ? '#28a745' : ($m['last_seen_at'] ? '#dc3545' : '#6c757d') }} !important; border-left-style: solid !important;">
                                <div class="card-body">
                                    {{-- Header Row --}}
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0">{{ $typeIcon }} {{ $m['name'] }}</h6>
                                            @if($m['brand'])
                                                <small class="text-muted">{{ $m['brand'] }}</small>
                                            @endif
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button wire:click="openEdit({{ $m['id'] }})" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="feather-edit-2"></i>
                                            </button>
                                            <button wire:click="delete({{ $m['id'] }})"
                                                wire:confirm="Delete {{ $m['name'] }}? This will also delete all its result logs."
                                                class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Type & Connection --}}
                                    <div class="d-flex gap-2 flex-wrap mb-3">
                                        <span class="badge bg-soft-primary text-primary">{{ $typeLabel }}</span>
                                        <span class="badge bg-soft-secondary text-secondary">
                                            {{ strtoupper($m['connection_type']) }}
                                            @if($m['port_or_ip']) — {{ $m['port_or_ip'] }}@endif
                                        </span>
                                        <span class="badge bg-soft-secondary text-secondary">
                                            {{ strtoupper($m['protocol']) }}
                                        </span>
                                    </div>

                                    {{-- Status --}}
                                    <div class="mb-3">
                                        @if(!$m['last_seen_at'])
                                            <span class="badge bg-soft-secondary text-secondary">
                                                <i class="feather-wifi-off me-1"></i>Never Connected
                                            </span>
                                        @elseif($isOnline)
                                            <span class="badge bg-soft-success text-success">
                                                <i class="feather-wifi me-1"></i>Online — {{ $lastSeen }}
                                            </span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger">
                                                <i class="feather-wifi-off me-1"></i>Offline — {{ $lastSeen }}
                                            </span>
                                        @endif
                                        @if(!$m['is_active'])
                                            <span class="badge bg-soft-warning text-warning ms-1">Disabled</span>
                                        @endif
                                    </div>

                                    {{-- API Token --}}
                                    <div class="mb-3">
                                        <label class="text-muted small fw-semibold mb-1">API Token (Bridge Agent)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="{{ $revealedTokenId === $m['id'] ? 'text' : 'password' }}"
                                                   class="form-control font-monospace"
                                                   value="{{ $m['api_token'] }}"
                                                   readonly>
                                            <button class="btn btn-outline-secondary" wire:click="revealToken({{ $m['id'] }})" title="{{ $revealedTokenId === $m['id'] ? 'Hide' : 'Show' }}">
                                                <i class="feather-{{ $revealedTokenId === $m['id'] ? 'eye-off' : 'eye' }}"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary"
                                                    onclick="navigator.clipboard.writeText('{{ $m['api_token'] }}').then(()=>alert('Token copied!'))"
                                                    title="Copy">
                                                <i class="feather-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button wire:click="openSimulator({{ $m['id'] }})" class="btn btn-sm btn-soft-purple">
                                            🧪 Simulate
                                        </button>
                                        <button wire:click="openLogs({{ $m['id'] }})" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather-list me-1"></i>Logs
                                        </button>
                                        <button wire:click="regenerateToken({{ $m['id'] }})"
                                                wire:confirm="Regenerate token for {{ $m['name'] }}? The old token will stop working. You must update your Bridge Agent."
                                                class="btn btn-sm btn-outline-warning">
                                            <i class="feather-refresh-cw me-1"></i>New Token
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Bridge Agent Download Card --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="fw-bold mb-0"><i class="feather-download me-2"></i>Bridge Agent — Lab PC Software</h6>
                    <small class="text-muted">Install on your lab PC to connect COM/TCP machines to the cloud</small>
                </div>
                <div class="card-body">
                    <div class="row g-4">

                        {{-- Python Option --}}
                        <div class="col-md-6">
                            <div class="card border border-warning h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div style="font-size:2.5rem">🐍</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">Python Version</h6>
                                            <small class="text-muted">Best if Python is installed</small>
                                        </div>
                                    </div>
                                    <div class="bg-dark rounded p-3 mb-3">
                                        <code class="text-success small">
                                            # Install dependency<br>
                                            pip install pyserial requests<br><br>
                                            # Run the agent<br>
                                            python LabBridgeAgent.py
                                        </code>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('lab.machine.bridge-agent.download') }}"
                                           class="btn btn-warning btn-sm">
                                            <i class="feather-download me-1"></i>Download agent.py
                                        </a>
                                        <a href="{{ route('lab.machine.bridge-agent.config') }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="feather-settings me-1"></i>Config File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Node.js Option --}}
                        <div class="col-md-6">
                            <div class="card border border-success h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div style="font-size:2.5rem">🟩</div>
                                        <div>
                                            <h6 class="fw-bold mb-0">Node.js Version</h6>
                                            <small class="text-muted">Best if Node.js is installed</small>
                                        </div>
                                    </div>
                                    <div class="bg-dark rounded p-3 mb-3">
                                        <code class="text-success small">
                                            # Install dependencies<br>
                                            npm install<br><br>
                                            # Run the agent<br>
                                            node LabBridgeAgent.js
                                        </code>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('lab.machine.bridge-agent.download-node') }}"
                                           class="btn btn-success btn-sm">
                                            <i class="feather-download me-1"></i>Download agent.js
                                        </a>
                                        <a href="{{ route('lab.machine.bridge-agent.package-json') }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="feather-package me-1"></i>package.json
                                        </a>
                                        <a href="{{ route('lab.machine.bridge-agent.config') }}"
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="feather-settings me-1"></i>Config File
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Setup Steps --}}
                    <div class="mt-4 p-3 bg-soft-info rounded">
                        <h6 class="fw-bold mb-2"><i class="feather-list me-2"></i>Setup Steps</h6>
                        <ol class="mb-0 small text-muted">
                            <li>Add your machines above → copy each machine's <strong>API Token</strong></li>
                            <li>Download <strong>Agent</strong> (Python or Node.js) + <strong>Config File</strong></li>
                            <li>Put both files in the same folder on your lab PC</li>
                            <li>Install dependency → run the agent</li>
                            <li>Machine data will automatically appear on Result Entry page! ✅</li>
                        </ol>
                    </div>
                </div>
            </div>

        </div>{{-- /main-content --}}
    </div>{{-- /nxl-content --}}

    {{-- ═══════════════════════════════════════════════════════════
         ADD / EDIT MACHINE MODAL
    ═══════════════════════════════════════════════════════════ --}}
    @if($showForm)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-soft-primary border-0 py-3">
                        <h5 class="modal-title fw-bold">
                            <i class="feather-cpu me-2"></i>
                            {{ $editingId ? 'Edit Machine' : 'Add New Machine' }}
                        </h5>
                        <button wire:click="closeForm" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Machine Name <span class="text-danger">*</span></label>
                                <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Cellomax 5">
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            {{-- Brand --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Brand / Manufacturer</label>
                                <input type="text" wire:model="brand" class="form-control" placeholder="e.g. INDO-MEDX">
                            </div>
                            {{-- Machine Type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Machine Type <span class="text-danger">*</span></label>
                                <select wire:model="machine_type" class="form-select">
                                    <option value="biochemistry">🔬 Biochemistry Analyzer</option>
                                    <option value="hematology">🩸 Hematology (CBC) Analyzer</option>
                                    <option value="electrolyte">⚗️ Electrolyte Analyzer</option>
                                    <option value="other">🔧 Other</option>
                                </select>
                            </div>
                            {{-- Protocol --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Protocol <span class="text-danger">*</span></label>
                                <select wire:model="protocol" class="form-select">
                                    <option value="astm">ASTM E1394 (Most common)</option>
                                    <option value="hl7">HL7 2.x</option>
                                    <option value="custom">Custom / Proprietary</option>
                                </select>
                            </div>
                            {{-- Connection Type --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Connection Type <span class="text-danger">*</span></label>
                                <select wire:model.live="connection_type" class="form-select">
                                    <option value="serial">Serial (RS-232 / USB-to-Serial)</option>
                                    <option value="tcp">TCP/IP Socket</option>
                                    <option value="http_push">HTTP Push (machine sends directly)</option>
                                </select>
                            </div>
                            {{-- Port / IP --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    {{ $connection_type === 'tcp' ? 'IP Address' : 'COM Port' }}
                                </label>
                                <input type="text" wire:model="port_or_ip" class="form-control"
                                       placeholder="{{ $connection_type === 'tcp' ? '192.168.1.50' : 'COM3' }}">
                            </div>
                            {{-- Baud Rate (serial only) --}}
                            @if($connection_type === 'serial')
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Baud Rate</label>
                                    <select wire:model="baud_rate" class="form-select">
                                        <option value="1200">1200</option>
                                        <option value="2400">2400</option>
                                        <option value="4800">4800</option>
                                        <option value="9600">9600 (default)</option>
                                        <option value="19200">19200</option>
                                        <option value="38400">38400</option>
                                        <option value="57600">57600</option>
                                        <option value="115200">115200</option>
                                    </select>
                                </div>
                            @endif
                            {{-- TCP Port (tcp only) --}}
                            @if($connection_type === 'tcp')
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">TCP Port</label>
                                    <input type="number" wire:model="tcp_port" class="form-control" placeholder="4001">
                                </div>
                            @endif
                            {{-- Status --}}
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="machine_active">
                                    <label class="form-check-label fw-semibold" for="machine_active">Active</label>
                                </div>
                            </div>
                            {{-- Parameter Mapping --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Parameter Mapping (JSON)
                                    <span class="text-muted fw-normal">— optional, only if machine uses different names</span>
                                </label>
                                <textarea wire:model="test_mapping_raw" rows="4" class="form-control font-monospace @error('test_mapping_raw') is-invalid @enderror"
                                          placeholder='{"SGOT": "SGOT", "BILI-T": "BILIRUBIN-T", "GLU": "GLUCOSE"}'></textarea>
                                @error('test_mapping_raw')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text text-muted">
                                    Format: <code>{"machine_param_name": "your_test_param_name"}</code>
                                    If machine name matches exactly, leave empty.
                                </div>
                            </div>
                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Notes</label>
                                <textarea wire:model="notes" rows="2" class="form-control" placeholder="Any notes about this machine..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button wire:click="closeForm" class="btn btn-outline-secondary">Cancel</button>
                        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary px-4">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-2"></span>
                            <i wire:loading.remove wire:target="save" class="feather-save me-2"></i>
                            {{ $editingId ? 'Save Changes' : 'Add Machine' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         SIMULATOR MODAL
    ═══════════════════════════════════════════════════════════ --}}
    @if($showSimulator)
        @php $simMachine = collect($machines)->firstWhere('id', $simulatorMachineId); @endphp
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0" style="background:linear-gradient(135deg,#6f42c1,#a855f7)">
                        <h5 class="modal-title text-white fw-bold">
                            🧪 Machine Simulator — {{ $simMachine['name'] ?? '' }}
                        </h5>
                        <button wire:click="closeSimulator" class="btn-close btn-close-white"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-soft-purple border-0 mb-4">
                            <i class="feather-info me-2"></i>
                            This simulator generates realistic fake machine data and sends it through the same pipeline as a real machine.
                            Use it to test the full import flow on the Result Entry page.
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sample / Barcode ID</label>
                                <input type="text" wire:model="simulatorSampleId" class="form-control font-monospace"
                                       placeholder="LAB260801001">
                                <div class="form-text">Must match a barcode in your invoice for auto-matching.</div>
                            </div>
                            @if(($simMachine['machine_type'] ?? '') === 'biochemistry')
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Test Type</label>
                                    <select wire:model="simulatorTestType" class="form-select">
                                        <option value="lft">LFT (Liver Function)</option>
                                        <option value="rft">RFT (Kidney Function)</option>
                                        <option value="lipid">Lipid Profile</option>
                                    </select>
                                </div>
                            @endif
                        </div>

                        <button wire:click="runSimulator" wire:loading.attr="disabled" class="btn btn-purple px-4">
                            <span wire:loading wire:target="runSimulator" class="spinner-border spinner-border-sm me-2"></span>
                            🚀 Run Simulation
                        </button>

                        {{-- Simulation Result --}}
                        @if($simulatorResult)
                            <div class="mt-4">
                                <div class="alert {{ $simulatorResult['status'] === 'matched' ? 'alert-success' : 'alert-warning' }} d-flex gap-3 border-0">
                                    <div style="font-size:1.5rem">{{ $simulatorResult['status'] === 'matched' ? '✅' : '⚠️' }}</div>
                                    <div>
                                        <strong>Status: {{ ucfirst($simulatorResult['status']) }}</strong><br>
                                        @if($simulatorResult['status'] === 'matched')
                                            Sample matched to an invoice! Go to Result Entry page → click <strong>Import from Machine</strong>.
                                        @else
                                            Sample ID <code>{{ $simulatorResult['sample_id'] }}</code> not found in invoices.
                                            Try using a barcode from an actual invoice.
                                        @endif
                                        <br><small class="text-muted">Log ID: #{{ $simulatorResult['log_id'] }} | {{ $simulatorResult['params_count'] }} parameters generated</small>
                                    </div>
                                </div>

                                {{-- Parsed Values Table --}}
                                <h6 class="fw-bold mb-2 mt-3">Generated Parameters ({{ $simulatorResult['params_count'] }})</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr><th>Parameter</th><th>Value</th><th>Unit</th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($simulatorResult['parsed'] as $param => $data)
                                                <tr>
                                                    <td class="fw-semibold">{{ $param }}</td>
                                                    <td class="font-monospace">{{ is_array($data) ? ($data['value'] ?? $data) : $data }}</td>
                                                    <td class="text-muted">{{ is_array($data) ? ($data['unit'] ?? '') : '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Raw Data --}}
                                <details class="mt-2">
                                    <summary class="text-muted small cursor-pointer">Show raw data</summary>
                                    <pre class="mt-2 p-2 bg-dark text-success rounded small" style="font-size:0.75rem;overflow-x:auto">{{ $simulatorResult['raw_data'] }}</pre>
                                </details>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0">
                        <button wire:click="closeSimulator" class="btn btn-outline-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════
         LOGS MODAL
    ═══════════════════════════════════════════════════════════ --}}
    @if($showLogs)
        <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0 bg-soft-secondary">
                        <h5 class="modal-title fw-bold">
                            <i class="feather-list me-2"></i>Result Logs — {{ $logsForMachine?->name }}
                        </h5>
                        <button wire:click="closeLogs" class="btn-close"></button>
                    </div>
                    <div class="modal-body p-0">
                        @if(count($logs) === 0)
                            <div class="text-center py-5 text-muted">No logs yet.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Sample ID</th>
                                            <th>Status</th>
                                            <th>Parameters</th>
                                            <th>Invoice</th>
                                            <th>Received</th>
                                            <th>Imported</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td class="text-muted small">{{ $log['id'] }}</td>
                                                <td class="font-monospace fw-semibold">{{ $log['sample_id'] ?? '—' }}</td>
                                                <td>
                                                    @php
                                                        $sc = match($log['status']) {
                                                            'matched' => 'primary', 'imported' => 'success',
                                                            'unmatched' => 'secondary', 'failed' => 'danger',
                                                            'simulated' => 'purple', default => 'warning'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-soft-{{ $sc }} text-{{ $sc }}">{{ ucfirst($log['status']) }}</span>
                                                </td>
                                                <td>{{ count($log['parsed_data'] ?? []) }} params</td>
                                                <td>{{ $log['invoice_id'] ? '#' . $log['invoice_id'] : '—' }}</td>
                                                <td class="text-muted small">{{ \Carbon\Carbon::parse($log['created_at'])->format('d M H:i') }}</td>
                                                <td class="small">{{ $log['imported_at'] ? \Carbon\Carbon::parse($log['imported_at'])->format('d M H:i') : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0">
                        <button wire:click="closeLogs" class="btn btn-outline-secondary">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

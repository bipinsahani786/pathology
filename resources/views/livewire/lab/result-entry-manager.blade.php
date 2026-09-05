<div>
    {{-- ======================== PAGE HEADER ======================== --}}
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h4 class="fw-bold mb-1 text-dark">Result Entry</h4>
            </div>
            <ul class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" class="text-primary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lab.reports') }}" class="text-primary text-decoration-none">Reports</a></li>
                <li class="breadcrumb-item active fw-medium">Result Entry</li>
            </ul>
        </div>
    </div>

    {{-- ======================== MAIN CONTENT ======================== --}}
    <div class="main-content">

        {{-- Patient Info Banner --}}
        <div class="card mb-4 border-primary border-top border-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 border-end">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg rounded-circle" style="background:rgba(59,113,202,0.1);">
                                <i class="feather-user text-primary fs-3"></i>
                            </div>
                            <div>
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Patient Info</div>
                                <div class="fw-bold fs-13">{{ $invoice->patient->name }} <span class="badge bg-soft-info text-info ms-1">{{ $invoice->patient->formatted_id }}</span></div>
                                <div class="fs-11 text-muted">{{ $invoice->patient->patientProfile ? $invoice->patient->patientProfile->age_text : '--' }} | {{ $invoice->patient->patientProfile->gender ?? '--' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 border-end">
                        <div class="row text-center mt-3 mt-md-0">
                            <div class="col-6 border-end">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Invoice / PID</div>
                                <div class="fw-bold fs-14 text-dark">{{ $invoice->invoice_number }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Invoice Date</div>
                                <div class="fw-bold fs-14 text-dark">{{ $invoice->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="fs-11 text-muted text-uppercase fw-bold mb-1"><i class="feather-calendar me-1"></i>Report Date & Time (for PDF)</div>
                        <div class="d-flex gap-2">
                            <input type="date" class="form-control form-control-sm" wire:model="report_date">
                            <input type="time" class="form-control form-control-sm" wire:model="report_time" style="max-width: 120px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ════════════════════════════════════════════════════════
             MACHINE INTEGRATION PANEL (HIDDEN FOR NOW)
             Auto-polls every 15s for new machine data
        ════════════════════════════════════════════════════════ --}}
        @if(false)
        <div wire:poll.15000ms="checkMachineData">

            {{-- Machine Data Ready Banner --}}
            @if(count($machineLogs) > 0)
                <div class="alert border-0 shadow-sm rounded-3 mb-3"
                     style="background: linear-gradient(135deg, #e8f5e9, #f1f8e9); border-left: 4px solid #28a745 !important; border-left-style: solid !important;">
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-success mb-1">
                                <i class="feather-cpu me-2"></i>🟢 Machine Data Ready!
                            </div>
                            @foreach($machineLogs as $mlog)
                                <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
                                    <div>
                                        @php
                                            $mIcon = match($mlog['machine_type']) { 'hematology' => '🩸', 'electrolyte' => '⚗️', default => '🔬' };
                                        @endphp
                                        <span class="fw-semibold">{{ $mIcon }} {{ $mlog['machine_name'] }}</span>
                                        <span class="text-muted small ms-2">{{ $mlog['params_count'] }} parameters — {{ $mlog['received_at'] }}</span>
                                    </div>
                                    <button wire:click="importFromMachine({{ $mlog['id'] }})"
                                            wire:loading.attr="disabled"
                                            class="btn btn-success btn-sm px-3">
                                        <span wire:loading wire:target="importFromMachine({{ $mlog['id'] }})" class="spinner-border spinner-border-sm me-1"></span>
                                        <i wire:loading.remove wire:target="importFromMachine({{ $mlog['id'] }})" class="feather-download me-1"></i>
                                        Import {{ $mlog['params_count'] }} Results
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Machine Import Success / Result --}}
            @if($machineImportResult)
                <div class="alert {{ $machineImportResult['filled'] > 0 ? 'alert-success' : 'alert-warning' }} border-0 shadow-sm rounded-3 mb-3 d-flex justify-content-between align-items-start">
                    <div>
                        @if($machineImportResult['filled'] > 0)
                            <i class="feather-check-circle me-2"></i>
                            <strong>{{ $machineImportResult['filled'] }} parameters</strong> auto-filled from
                            <strong>{{ $machineImportResult['machine_name'] }}</strong>!
                            Values are highlighted in <span class="badge bg-success text-white">green</span> below.
                        @else
                            <i class="feather-alert-triangle me-2"></i>
                            No parameters matched from {{ $machineImportResult['machine_name'] }}.
                        @endif
                        @if(!empty($machineImportResult['unmatched_params']))
                            <div class="mt-1 small text-muted">
                                Not matched: {{ implode(', ', array_slice($machineImportResult['unmatched_params'], 0, 8)) }}
                                {{ count($machineImportResult['unmatched_params']) > 8 ? '...' : '' }}
                            </div>
                        @endif
                    </div>
                    <button wire:click="dismissMachineImportResult" class="btn-close btn-sm ms-3"></button>
                </div>
            @endif

            {{-- Simulator Toggle Button (when no machines are online) --}}
            @if(count($machineLogs) === 0 && $availableMachines->count() > 0)
                <div class="mb-3">
                    <button wire:click="toggleMachinePanel" class="btn btn-sm btn-outline-purple">
                        🧪 {{ $showMachineImport ? 'Hide' : 'Simulate Machine Data (for testing)' }}
                    </button>

                    @if($showMachineImport)
                        <div class="card border-purple mt-2 shadow-sm">
                            <div class="card-body py-3">
                                <div class="fw-semibold mb-2" style="color:#6f42c1">🧪 Machine Simulator</div>
                                <p class="text-muted small mb-3">No machine connected. Use simulator to test the full import flow.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($availableMachines as $am)
                                        @php
                                            $amIcon = match($am->machine_type) { 'hematology' => '🩸', 'electrolyte' => '⚗️', default => '🔬' };
                                        @endphp
                                        @if($am->machine_type === 'biochemistry')
                                            <div class="d-flex gap-1">
                                                <button wire:click="simulateMachineData({{ $am->id }}, 'lft')" class="btn btn-sm btn-soft-purple">
                                                    {{ $amIcon }} {{ $am->name }} — LFT
                                                </button>
                                                <button wire:click="simulateMachineData({{ $am->id }}, 'rft')" class="btn btn-sm btn-soft-purple">
                                                    RFT
                                                </button>
                                                <button wire:click="simulateMachineData({{ $am->id }}, 'lipid')" class="btn btn-sm btn-soft-purple">
                                                    Lipid
                                                </button>
                                            </div>
                                        @else
                                            <button wire:click="simulateMachineData({{ $am->id }})" class="btn btn-sm btn-soft-purple">
                                                {{ $amIcon }} {{ $am->name }}
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- /wire:poll --}}
        @endif

        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3">
                <i class="feather-alert-octagon me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Entry Form Engine --}}
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fs-13"><i class="feather-edit-3 text-primary me-2"></i>Enter Test Values</h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <button wire:click="printSelected(1)" class="btn btn-sm btn-info shadow-sm" title="Prints all tests with results using Letterhead">
                                <i class="feather-printer me-1"></i>Print (With Header)
                            </button>
                            <button wire:click="printSelected(0)" class="btn btn-sm btn-outline-secondary shadow-sm" title="Prints all tests with results leaving space for Letterhead">
                                <i class="feather-file-text me-1"></i>Print (Without Header)
                            </button>
                        </div>
                        <span class="badge bg-soft-info text-info fs-11"><i class="feather-cpu me-1"></i>Auto-Calc</span>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive" x-data="resultEntrySortable()" x-init="initSortable($refs.sortableContainer)">
                    <table class="table table-hover align-middle mb-0" x-ref="sortableContainer">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Test Parameter</th>
                                <th style="width: 25%">Result Value</th>
                                <th style="width: 15%">Unit</th>
                                <th style="width: 20%">Reference Range</th>
                                <th style="width: 10%" class="text-center">Highlight</th>
                            </tr>
                        </thead>
                        @php $currentDept = null; @endphp
                        @foreach($groupedParams as $itemId => $testsInItem)
                            @php
                                $firstTestParams = $testsInItem->first();
                                $itemDept = $firstTestParams->first()['department'] ?? 'General';
                            @endphp
                            
                            @if($currentDept !== $itemDept)
                                <tbody class="department-header ignore-sort">
                                    <tr>
                                        <td colspan="5" class="bg-primary text-white py-2 fs-13 fw-bold">
                                            <i class="feather-layers me-2"></i>{{ strtoupper($itemDept) }}
                                        </td>
                                    </tr>
                                </tbody>
                                @php $currentDept = $itemDept; @endphp
                            @endif
                            
                            <tbody class="sortable-item" data-id="{{ $itemId }}">
                                    @php
                                        $testItem = $invoice->items->where('id', $itemId)->first();
                                        $isBillItemComplete = $testItem && $testItem->status === 'Completed';
                                        $billItemName = $testItem->labTest->name ?? 'Unknown';
                                    @endphp

                                    {{-- Bill Item Header (Package or Single Test) --}}
                                    @if($testItem && $testItem->labTest && $testItem->labTest->is_package)
                                        <tr>
                                            <td colspan="5" class="bg-soft-primary py-2 fs-12 fw-bold text-dark border-bottom">
                                                <div class="d-flex align-items-center justify-content-between px-2">
                                                    <div class="d-flex align-items-center">
                                                        <i class="feather-move drag-handle me-2 text-muted" style="cursor: grab;" title="Drag to reorder"></i>
                                                        <div class="d-flex flex-column me-2" style="line-height: 1;">
                                                            <button wire:click="moveTestUp({{ $itemId }})" class="btn btn-link p-0 text-muted" title="Move Up"><i class="feather-chevron-up fs-11"></i></button>
                                                            <button wire:click="moveTestDown({{ $itemId }})" class="btn btn-link p-0 text-muted" title="Move Down"><i class="feather-chevron-down fs-11"></i></button>
                                                        </div>
                                                        <input type="checkbox" class="form-check-input me-2" 
                                                               wire:model.live="selectedTests" value="{{ $itemId }}">
                                                        <i class="feather-box text-primary me-2"></i>{{ $billItemName }} (Package)
                                                        @php
                                                            $allInnerTestsFilled = true;
                                                            if($testItem && $testItem->labTest && $testItem->labTest->is_package) {
                                                                foreach($testsInItem as $paramsTemp) {
                                                                    $tempFilled = true;
                                                                    $hasAny = false;
                                                                    foreach($paramsTemp as $pt) {
                                                                        if(($pt['input_type'] ?? 'numeric') !== 'heading') {
                                                                            $hasAny = true;
                                                                            $val = $results[$pt['key']] ?? '';
                                                                            $isFilled = ($val !== '' && $val !== null);
                                                                            if(($pt['input_type'] ?? 'numeric') === 'culture_sensitivity') {
                                                                                $cData = $cultureResults[$pt['key']] ?? null;
                                                                                if($cData && (($cData['growth_status'] ?? '') === 'No Growth' || !empty($cData['organism_name']))) $isFilled = true; else $isFilled = false;
                                                                            }
                                                                            if(!$isFilled) $tempFilled = false;
                                                                        }
                                                                    }
                                                                    if(!$hasAny || !$tempFilled) $allInnerTestsFilled = false;
                                                                }
                                                            }
                                                        @endphp
                                                        <span class="ms-2 badge {{ $isBillItemComplete ? ($allInnerTestsFilled ? 'bg-success' : 'bg-info') : 'bg-warning text-dark' }} fs-9">
                                                            {{ $isBillItemComplete ? ($allInnerTestsFilled ? 'Completed' : 'Partial') : 'Pending' }}
                                                        </span>
                                                    </div>
                                                    @can('edit reports')
                                                        <button wire:click="toggleTestStatus({{ $itemId }})" 
                                                                class="btn btn-xs {{ $isBillItemComplete ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-2"
                                                                style="font-size: 10px;">
                                                            <i class="feather-{{ $isBillItemComplete ? 'x-circle' : 'check-circle' }} me-1"></i>
                                                            Mark {{ $isBillItemComplete ? 'Pending' : 'Complete' }}
                                                        </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @endif

                                    @foreach($testsInItem as $labTestId => $params)
                                        @php
                                            $testName = $params->first()['test_name'] ?? 'Unknown Test';
                                        @endphp
                                        
                                        {{-- Test Name Subheader --}}
                                        <tr>
                                            <td colspan="5" class="bg-light py-2 fs-12 fw-bold text-dark border-bottom">
                                                <div class="d-flex align-items-center justify-content-between px-3">
                                                    <div class="d-flex align-items-center">
                                                        @if(!($testItem->labTest->is_package ?? false))
                                                            <i class="feather-move drag-handle me-2 text-muted" style="cursor: grab;" title="Drag to reorder"></i>
                                                            <div class="d-flex flex-column me-2" style="line-height: 1;">
                                                                <button wire:click="moveTestUp({{ $itemId }})" class="btn btn-link p-0 text-muted" title="Move Up"><i class="feather-chevron-up fs-11"></i></button>
                                                                <button wire:click="moveTestDown({{ $itemId }})" class="btn btn-link p-0 text-muted" title="Move Down"><i class="feather-chevron-down fs-11"></i></button>
                                                            </div>
                                                        @endif
                                                        <input type="checkbox" class="form-check-input me-2" 
                                                               wire:model.live="selectedTests" 
                                                               value="{{ ($testItem->labTest->is_package ?? false) ? $itemId . '_' . $labTestId : $itemId }}">
                                                        <i class="feather-activity text-muted me-2"></i>{{ $testName }}
                                                        @php
                                                            $dlcCodes = ['NEU', 'LYM', 'MONO', 'EOS', 'BASO'];
                                                            $dlcSum = 0;
                                                            $hasDlc = false;
                                                            foreach($params as $p) {
                                                                $code = strtoupper($p['short_code'] ?? '');
                                                                if(in_array($code, $dlcCodes)) {
                                                                    $hasDlc = true;
                                                                    $dlcSum += (float)($results[$p['key']] ?? 0);
                                                                }
                                                            }
                                                        @endphp
                                                        @if($hasDlc)
                                                            <span class="ms-2 badge {{ abs($dlcSum - 100) < 0.01 ? 'bg-success' : 'bg-danger' }} fs-10" title="NEU + LYM + MONO + EOS + BASO">
                                                                DLC Sum: {{ round($dlcSum, 2) }}%
                                                            </span>
                                                        @endif
                                                        @php
                                                            $innerTestFilled = true;
                                                            $hasAnyInnerParam = false;
                                                            foreach($params as $p) {
                                                                if(($p['input_type'] ?? 'numeric') !== 'heading') {
                                                                    $hasAnyInnerParam = true;
                                                                    $val = $results[$p['key']] ?? '';
                                                                    $isFilled = ($val !== '' && $val !== null);
                                                                    
                                                                    if(($p['input_type'] ?? 'numeric') === 'culture_sensitivity') {
                                                                        $cData = $cultureResults[$p['key']] ?? null;
                                                                        if($cData && (($cData['growth_status'] ?? '') === 'No Growth' || !empty($cData['organism_name']))) {
                                                                            $isFilled = true;
                                                                        } else {
                                                                            $isFilled = false;
                                                                        }
                                                                    }
                                                                    
                                                                    if(!$isFilled) {
                                                                        $innerTestFilled = false;
                                                                    }
                                                                }
                                                            }
                                                            if(!$hasAnyInnerParam) $innerTestFilled = false;
                                                            
                                                            $innerTestStatusClass = $innerTestFilled ? 'bg-success' : 'bg-warning text-dark';
                                                            $innerTestStatusText = $innerTestFilled ? 'Completed' : 'Pending';
                                                            
                                                            // If it is not a package, we just use the bill item status as it matches the DB exactly
                                                            if(!($testItem->labTest->is_package ?? false)) {
                                                                $innerTestStatusClass = $isBillItemComplete ? 'bg-success' : 'bg-warning text-dark';
                                                                $innerTestStatusText = $isBillItemComplete ? 'Completed' : 'Pending';
                                                            }
                                                        @endphp
                                                        <span class="ms-2 badge {{ $innerTestStatusClass }} fs-9">
                                                            {{ $innerTestStatusText }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center gap-2">
                                                        @php $optKey = $itemId . '_' . $labTestId; @endphp
                                                        <div class="d-flex align-items-center gap-2 bg-white px-2 py-1 rounded border" title="PDF Report Print Options for this test">
                                                            <span class="fs-10 text-muted fw-bold text-uppercase me-1"><i class="feather-printer me-1 text-primary"></i>Print:</span>
                                                            <div class="form-check form-check-inline form-switch mb-0" title="Show/Hide Method on PDF Report">
                                                                <input class="form-check-input" type="checkbox" wire:model.live="testOptions.{{ $optKey }}.show_method" id="m_{{ $optKey }}">
                                                                <label class="form-check-label fs-10 fw-semibold {{ ($testOptions[$optKey]['show_method'] ?? true) ? 'text-primary' : 'text-muted text-decoration-line-through' }}" for="m_{{ $optKey }}">Method</label>
                                                            </div>
                                                            <div class="form-check form-check-inline form-switch mb-0" title="Show/Hide Clinical Interpretation on PDF Report">
                                                                <input class="form-check-input" type="checkbox" wire:model.live="testOptions.{{ $optKey }}.show_interpretation" id="i_{{ $optKey }}">
                                                                <label class="form-check-label fs-10 fw-semibold {{ ($testOptions[$optKey]['show_interpretation'] ?? true) ? 'text-primary' : 'text-muted text-decoration-line-through' }}" for="i_{{ $optKey }}">Interp</label>
                                                            </div>
                                                            <div class="form-check form-check-inline form-switch mb-0" title="Show/Hide Notes on PDF Report">
                                                                <input class="form-check-input" type="checkbox" wire:model.live="testOptions.{{ $optKey }}.show_note" id="n_{{ $optKey }}">
                                                                <label class="form-check-label fs-10 fw-semibold {{ ($testOptions[$optKey]['show_note'] ?? true) ? 'text-primary' : 'text-muted text-decoration-line-through' }}" for="n_{{ $optKey }}">Note</label>
                                                            </div>
                                                        </div>

                                                        @if(!($testItem->labTest->is_package ?? false))
                                                            @can('edit reports')
                                                                <button wire:click="toggleTestStatus({{ $itemId }})" 
                                                                        class="btn btn-xs {{ $isBillItemComplete ? 'btn-outline-danger' : 'btn-outline-success' }} py-0 px-2"
                                                                        style="font-size: 10px;">
                                                                    <i class="feather-{{ $isBillItemComplete ? 'x-circle' : 'check-circle' }} me-1"></i>
                                                                    Mark {{ $isBillItemComplete ? 'Pending' : 'Complete' }}
                                                                </button>
                                                            @endcan
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        @if($hasDlc && abs($dlcSum - 100) > 0.01)
                                            <tr>
                                                <td colspan="5" class="bg-soft-danger py-2 px-3 border-bottom">
                                                    <div class="d-flex align-items-center text-danger fs-12 fw-bold">
                                                        <i class="feather-alert-triangle me-2 fs-14"></i>
                                                        <span>DLC Sum Alert: Neutrophils + Lymphocytes + Monocytes + Eosinophils + Basophils is {{ round($dlcSum, 2) }}%. It must be exactly 100% before you can mark this test complete or approve the report!</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif

                                        @foreach($params as $p)
                                             @php
                                                 $paramKey = $p['key'];
                                                 $isHigh = $highlights[$paramKey] ?? false;
                                             @endphp
                                             @if(($p['input_type'] ?? 'numeric') === 'culture_sensitivity')
                                                 <tr class="{{ $isHigh ? 'table-danger' : '' }}" wire:key="param-{{ $paramKey }}">
                                                     <td colspan="5" class="bg-soft-light p-3 border-bottom">
                                                         <div class="card border border-primary border-opacity-25 shadow-sm rounded-3 mb-2">
                                                             <div class="card-header bg-soft-primary py-2 d-flex justify-content-between align-items-center">
                                                                 <span class="fw-bold text-primary fs-12"><i class="feather-activity me-1"></i>Microbiology Culture & Sensitivity: {{ $p['name'] }}</span>
                                                                 <div class="form-check form-switch">
                                                                     <label class="form-check-label fs-11 text-muted me-1" for="hl-{{ $paramKey }}">Highlight Report Row</label>
                                                                     <input class="form-check-input" type="checkbox" id="hl-{{ $paramKey }}" wire:model.live="highlights.{{ $paramKey }}">
                                                                 </div>
                                                             </div>
                                                             <div class="card-body p-3 fs-12">
                                                                 <div class="row g-3">
                                                                     <div class="col-md-4">
                                                                         <label class="form-label fw-bold text-muted text-uppercase mb-1 fs-10">Growth Status</label>
                                                                         <select class="form-select form-select-sm" wire:model.live="cultureResults.{{ $paramKey }}.growth_status">
                                                                             <option value="Growth">Significant Growth</option>
                                                                             <option value="No Growth">No Growth / Sterile</option>
                                                                             <option value="Contamination">Mixed Growth (Contaminant)</option>
                                                                         </select>
                                                                     </div>
                                                                     @if(($cultureResults[$paramKey]['growth_status'] ?? 'Growth') !== 'No Growth')
                                                                         <div class="col-md-4">
                                                                             <label class="form-label fw-bold text-muted text-uppercase mb-1 fs-10">Organism Isolated</label>
                                                                             <input type="text" class="form-control form-control-sm" 
                                                                                    wire:model.live="cultureResults.{{ $paramKey }}.organism_name"
                                                                                    placeholder="e.g. Escherichia coli"
                                                                                    list="common-organisms">
                                                                         </div>
                                                                         <div class="col-md-4">
                                                                             <label class="form-label fw-bold text-muted text-uppercase mb-1 fs-10">Colony Count</label>
                                                                             <input type="text" class="form-control form-control-sm" 
                                                                                    wire:model.live="cultureResults.{{ $paramKey }}.colony_count"
                                                                                    placeholder="e.g. 10^5 CFU/mL"
                                                                                    list="colony-counts">
                                                                         </div>
                                                                     @endif
                                                                 </div>
                                                                 
                                                                 @if(($cultureResults[$paramKey]['growth_status'] ?? 'Growth') !== 'No Growth')
                                                                     <div class="mt-4">
                                                                         <div class="d-flex justify-content-between align-items-center mb-2">
                                                                             <label class="form-label fw-bold text-muted text-uppercase mb-0 fs-10"><i class="feather-shield-alert me-1"></i>Antibiotic Susceptibility Grid</label>
                                                                             <button type="button" class="btn btn-xs btn-primary rounded-pill py-1 px-2 fs-10" wire:click="addAntibioticRow('{{ $paramKey }}')">
                                                                                 <i class="feather-plus me-1"></i>Add Antibiotic
                                                                             </button>
                                                                         </div>
                                                                         <div class="table-responsive border rounded-3 bg-white p-1">
                                                                             <table class="table table-sm align-middle mb-0" style="min-width: 500px;">
                                                                                 <thead class="bg-light">
                                                                                     <tr class="fs-10 text-muted text-uppercase fw-bold">
                                                                                         <th style="width: 45%;">Antibiotic Name</th>
                                                                                         <th class="text-center" style="width: 35%;">Susceptibility</th>
                                                                                         <th style="width: 15%;">MIC Value</th>
                                                                                         <th class="text-end pe-2" style="width: 5%;"></th>
                                                                                     </tr>
                                                                                 </thead>
                                                                                 <tbody>
                                                                                     @if(isset($cultureResults[$paramKey]['antibiotics']) && count($cultureResults[$paramKey]['antibiotics']) > 0)
                                                                                         @foreach($cultureResults[$paramKey]['antibiotics'] as $aIdx => $antibiotic)
                                                                                             <tr wire:key="cs-ab-{{ $paramKey }}-{{ $aIdx }}">
                                                                                                 <td>
                                                                                                     <input type="text" class="form-control form-control-sm" 
                                                                                                            wire:model.live="cultureResults.{{ $paramKey }}.antibiotics.{{ $aIdx }}.name"
                                                                                                            placeholder="e.g. Amikacin"
                                                                                                            list="common-antibiotics">
                                                                                                 </td>
                                                                                                 <td>
                                                                                                     <div class="d-flex justify-content-around">
                                                                                                         <div class="form-check form-check-inline mb-0">
                                                                                                             <input class="form-check-input" type="radio" 
                                                                                                                    id="rad-s-{{ $paramKey }}-{{ $aIdx }}"
                                                                                                                    value="S" 
                                                                                                                    wire:model.live="cultureResults.{{ $paramKey }}.antibiotics.{{ $aIdx }}.sensitivity">
                                                                                                             <label class="form-check-label text-success fw-bold fs-11" for="rad-s-{{ $paramKey }}-{{ $aIdx }}">S</label>
                                                                                                         </div>
                                                                                                         <div class="form-check form-check-inline mb-0">
                                                                                                             <input class="form-check-input" type="radio" 
                                                                                                                    id="rad-i-{{ $paramKey }}-{{ $aIdx }}"
                                                                                                                    value="I" 
                                                                                                                    wire:model.live="cultureResults.{{ $paramKey }}.antibiotics.{{ $aIdx }}.sensitivity">
                                                                                                             <label class="form-check-label text-warning fw-bold fs-11" for="rad-i-{{ $paramKey }}-{{ $aIdx }}">I</label>
                                                                                                         </div>
                                                                                                         <div class="form-check form-check-inline mb-0">
                                                                                                             <input class="form-check-input" type="radio" 
                                                                                                                    id="rad-r-{{ $paramKey }}-{{ $aIdx }}"
                                                                                                                    value="R" 
                                                                                                                    wire:model.live="cultureResults.{{ $paramKey }}.antibiotics.{{ $aIdx }}.sensitivity">
                                                                                                             <label class="form-check-label text-danger fw-bold fs-11" for="rad-r-{{ $paramKey }}-{{ $aIdx }}">R</label>
                                                                                                         </div>
                                                                                                     </div>
                                                                                                 </td>
                                                                                                 <td>
                                                                                                     <input type="text" class="form-control form-control-sm text-center" 
                                                                                                            wire:model.live="cultureResults.{{ $paramKey }}.antibiotics.{{ $aIdx }}.mic"
                                                                                                            placeholder="e.g. <= 2">
                                                                                                 </td>
                                                                                                 <td class="text-end pe-2">
                                                                                                     <button type="button" class="btn btn-icon btn-soft-secondary btn-xs border-0 me-1"
                                                                                                             wire:click="clearAntibioticRow('{{ $paramKey }}', {{ $aIdx }})" title="Clear selection">
                                                                                                         <i class="feather-x fs-12"></i>
                                                                                                     </button>
                                                                                                     <button type="button" class="btn btn-icon btn-soft-danger btn-xs border-0"
                                                                                                             wire:click="removeAntibioticRow('{{ $paramKey }}', {{ $aIdx }})" title="Remove row">
                                                                                                         <i class="feather-trash-2 fs-12"></i>
                                                                                                     </button>
                                                                                                 </td>
                                                                                             </tr>
                                                                                         @endforeach
                                                                                     @else
                                                                                         <tr>
                                                                                             <td colspan="4" class="text-center text-muted py-3 fs-11">
                                                                                                 No antibiotics added yet. Click "Add Antibiotic" to begin susceptibility testing.
                                                                                             </td>
                                                                                         </tr>
                                                                                     @endif
                                                                                 </tbody>
                                                                             </table>
                                                                         </div>
                                                                     </div>
                                                                 @endif
                                                             </div>
                                                         </div>
                                                     </td>
                                                 </tr>
                                             @elseif(($p['input_type'] ?? 'numeric') === 'heading')
                                                 @if(trim($p['name']) === '')
                                                     {{-- ── Group End Row ── --}}
                                                     @php $inGroup = false; @endphp
                                                     <tr wire:key="param-{{ $paramKey }}" class="table-borderless">
                                                         <td colspan="5" class="text-center py-2 text-muted fs-11" style="background: repeating-linear-gradient(45deg, #f8f9fa, #f8f9fa 10px, #ffffff 10px, #ffffff 20px); border-top: 1px dashed #ccc; border-bottom: 1px dashed #ccc;">
                                                             <i class="feather-corner-down-left me-1"></i> <i>Group Closed</i>
                                                         </td>
                                                     </tr>
                                                 @else
                                                     {{-- ── Group Heading / Section Separator Row ── --}}
                                                     @php $inGroup = true; @endphp
                                                     <tr wire:key="param-{{ $paramKey }}" class="table-secondary">
                                                         <td colspan="5" class="fw-bold fs-12 ps-3 py-2" 
                                                             style="background: #e8eeff; border-top: 2px solid #b0bfff; border-bottom: 1px solid #b0bfff; letter-spacing: 0.03em;">
                                                             <i class="feather-layers me-2 text-primary" style="font-size: 12px;"></i>
                                                             <span class="text-dark">{{ strtoupper($p['name']) }}</span>
                                                         </td>
                                                     </tr>
                                                 @endif
                                             @else
                                                 <tr class="{{ $isHigh ? 'table-danger' : '' }}" wire:key="param-{{ $paramKey }}">
                                                     <td class="fw-bold fs-12 {{ isset($inGroup) && $inGroup ? 'ps-5' : 'ps-3' }}">
                                                         <div class="d-flex align-items-center">
                                                             {{ $p['name'] }}
                                                             @if($isHigh)
                                                                 @php $f = $flags[$paramKey] ?? 'Abn'; @endphp
                                                                 <span class="ms-2 badge {{ in_array($f, ['H', 'Abn']) ? 'bg-danger' : 'bg-warning text-dark' }} px-2" style="font-size: 10px;">
                                                                     {{ $f === 'H' ? 'High' : ($f === 'L' ? 'Low' : 'Abnormal') }}
                                                                 </span>
                                                             @endif
                                                         </div>
                                                         @if(!empty($p['short_code']))
                                                             <div class="fs-10 text-muted">Code: {{ $p['short_code'] }}</div>
                                                         @endif
                                                     </td>
                                                     <td>
                                                         <div class="input-group input-group-sm w-100">
                                                             @if(($p['input_type'] ?? 'numeric') === 'selection')
                                                                 <select class="form-select {{ $isHigh ? 'border-danger text-danger fw-bold' : '' }}" 
                                                                         wire:model.live="results.{{ $paramKey }}">
                                                                     <option value="">Select Result</option>
                                                                     @foreach($p['options'] ?? [] as $opt)
                                                                         <option value="{{ $opt }}">{{ $opt }}</option>
                                                                     @endforeach
                                                                 </select>
                                                             @elseif(($p['input_type'] ?? 'numeric') === 'calculated')
                                                                 <input type="text" class="form-control {{ isset($manualOverrides[$paramKey]) ? 'border-warning fw-bold text-warning' : 'bg-light fw-bold text-primary border-primary border-opacity-25' }}" 
                                                                        wire:model.live.debounce.500ms="results.{{ $paramKey }}" 
                                                                        title="Auto-Calculated. Edit to override. Clear to restore formula.">
                                                                 <span class="input-group-text {{ isset($manualOverrides[$paramKey]) ? 'bg-soft-warning' : 'bg-soft-primary' }}">
                                                                     <i class="feather-cpu" style="font-size: 10px;"></i>
                                                                 </span>
                                                             @else
                                                                 <input type="text" class="form-control {{ $isHigh ? 'border-danger text-danger fw-bold' : '' }}" 
                                                                        wire:model.live.debounce.500ms="results.{{ $paramKey }}" 
                                                                        placeholder="-">
                                                             @endif

                                                             @if($isHigh && isset($flags[$paramKey]) && !in_array($p['input_type'] ?? '', ['selection', 'calculated']))
                                                                 <span class="input-group-text bg-danger text-white border-danger fw-bold fs-11 px-2">
                                                                     {{ $flags[$paramKey] }}
                                                                 </span>
                                                             @endif
                                                         </div>
                                                     </td>
                                                     <td class="fs-12 text-muted">{{ $p['unit'] }}</td>
                                                     <td class="fs-12 fw-medium text-dark">{!! nl2br(e($p['ref_range'] ?: '-')) !!}</td>
                                                     <td class="text-center">
                                                         <div class="form-check form-switch d-flex justify-content-center">
                                                             <input class="form-check-input" type="checkbox" 
                                                                    wire:model.live="highlights.{{ $paramKey }}" 
                                                                    style="width: 2.5em; height: 1.25em;">
                                                         </div>
                                                     </td>
                                                 </tr>
                                             @endif
                                         @endforeach

                                        {{-- Granular Remark Editor (Inside Test Loop) --}}
                                        <tr wire:key="remark-{{ $itemId }}-{{ $labTestId }}">
                                            <td colspan="5" class="bg-light p-3 border-bottom" wire:ignore>
                                                <label class="form-label fw-bold fs-11 text-muted text-uppercase mb-1">
                                                    <i class="feather-align-left me-1 text-primary"></i>Interpretation for {{ $testName }}
                                                </label>
                                                <textarea class="form-control" rows="2" 
                                                    x-data x-init="
                                                        ClassicEditor
                                                            .create($el, {
                                                                toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo']
                                                            })
                                                            .then(editor => {
                                                                editor.model.document.on('change:data', () => {
                                                                    @this.set('testComments.{{ $itemId }}_{{ $labTestId }}', editor.getData());
                                                                });
                                                                editor.setData(@js($testComments[$itemId . '_' . $labTestId] ?? ''));
                                                            })
                                                    " placeholder="Add specific interpretation for {{ $testName }}..."></textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                            </tbody>
                        @endforeach

                        @php
                            $parameterlessItems = $invoice->items->filter(fn($i) => !empty($i->lab_test_id) && !$i->hasParameters());
                        @endphp
                        @if($parameterlessItems->count() > 0)
                            <tbody class="department-header ignore-sort">
                                <tr>
                                    <td colspan="5" class="bg-soft-info text-dark py-2 px-3 fs-12 fw-bold border-top border-bottom">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span><i class="feather-info text-info me-2"></i>BILLING & SERVICES (NO TEST VALUES REQUIRED)</span>
                                            <span class="badge bg-success text-white fs-10 fw-semibold">Auto-Approved / Completed</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            @foreach($parameterlessItems as $pItem)
                                <tbody class="ignore-sort" wire:key="paramless-{{ $pItem->id }}">
                                    <tr>
                                        <td colspan="5" class="py-2 px-3 bg-white border-bottom">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <i class="feather-check-circle text-success me-2 fs-14"></i>
                                                    <span class="fw-bold text-dark fs-12">{{ $pItem->test_name ?? $pItem->labTest?->name }}</span>
                                                    <span class="badge bg-soft-success text-success ms-2 fs-10">Completed</span>
                                                </div>
                                                <div class="text-muted fs-11">
                                                    <i class="feather-check me-1 text-success"></i>Billing / Service item — No parameter results entry required
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        @endif
                    </table>
                </div>
                
                {{-- Report Comments --}}
                <div class="px-4 py-3 bg-light border-top" wire:ignore>
                    <label class="form-label fw-bold fs-12 text-dark"><i class="feather-message-square me-2 text-primary"></i>Global Report Comments / Interpretation (Appears at End)</label>
                    <textarea class="form-control rich-editor" id="report-comments-editor" rows="3" 
                        x-data x-init="
                            ClassicEditor
                                .create($el, {
                                    toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'insertTable', 'undo', 'redo']
                                })
                                .then(editor => {
                                    editor.model.document.on('change:data', () => {
                                        @this.set('comments', editor.getData());
                                    });
                                    editor.setData(@js($comments));
                                })
                        " placeholder="Add final interpretation or remarks..."></textarea>
                </div>
            </div>
            
            <div class="card-footer bg-light p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($testReport && $testReport->status === 'Approved')
                            <div class="text-success fw-bold"><i class="feather-check-circle me-1"></i>Report is Approved and Locked.</div>
                        @else
                            <div class="text-muted fs-11">Type values to auto-save to temporary state. Hit buttons to finalize.</div>
                        @endif
                    </div>
                    
                    <div class="d-flex gap-2">
                        @can('edit reports')
                            <button wire:click="saveReport('Draft')" class="btn btn-outline-primary fw-bold">
                                <i class="feather-save me-1"></i> Save Draft
                            </button>
                            <button wire:click="saveReport('Approved')" class="btn btn-success fw-bold px-4" {{ ($testReport && $testReport->status === 'Approved') ? 'disabled' : '' }}>
                                <i class="feather-check me-1"></i> Approve & Finalize
                            </button>
                        @endcan
                        <button wire:click="printSelected(1)" class="btn btn-info text-white fw-bold">
                            <i class="feather-printer me-1"></i> Print (With Header)
                        </button>
                        <button wire:click="printSelected(0)" class="btn btn-outline-secondary fw-bold">
                            <i class="feather-file-text me-1"></i> Print (Without Header)
                        </button>
                    </div>
                </div>
            </div>
        </div>

    <datalist id="common-organisms">
        <option value="Escherichia coli"></option>
        <option value="Staphylococcus aureus"></option>
        <option value="Pseudomonas aeruginosa"></option>
        <option value="Klebsiella pneumoniae"></option>
        <option value="Enterococcus faecalis"></option>
        <option value="Proteus mirabilis"></option>
        <option value="Acinetobacter baumannii"></option>
        <option value="Streptococcus pneumoniae"></option>
        <option value="Candida albicans"></option>
        <option value="Enterobacter cloacae"></option>
        <option value="Citrobacter koseri"></option>
        <option value="Citrobacter freundii"></option>
        <option value="Morganella morganii"></option>
        <option value="Serratia marcescens"></option>
        <option value="Salmonella typhi"></option>
        <option value="Shigella flexneri"></option>
        <option value="Vibrio cholerae"></option>
        <option value="Haemophilus influenzae"></option>
        <option value="Neisseria gonorrhoeae"></option>
        <option value="Neisseria meningitidis"></option>
        <option value="Streptococcus pyogenes"></option>
        <option value="Streptococcus agalactiae"></option>
        <option value="Enterococcus faecium"></option>
        <option value="Candida glabrata"></option>
        <option value="Candida tropicalis"></option>
        <option value="Aspergillus fumigatus"></option>
    </datalist>

    <datalist id="colony-counts">
        <option value="10^3 CFU/mL"></option>
        <option value="10^4 CFU/mL"></option>
        <option value="10^5 CFU/mL"></option>
        <option value="> 10^5 CFU/mL"></option>
        <option value="< 10^3 CFU/mL"></option>
        <option value="10^4 - 10^5 CFU/mL"></option>
    </datalist>

    <datalist id="common-antibiotics">
        <option value="Amikacin"></option>
        <option value="Amoxicillin/Clavulanate"></option>
        <option value="Ampicillin"></option>
        <option value="Azithromycin"></option>
        <option value="Ceftriaxone"></option>
        <option value="Cefotaxime"></option>
        <option value="Ceftazidime"></option>
        <option value="Cefuroxime"></option>
        <option value="Ciprofloxacin"></option>
        <option value="Clindamycin"></option>
        <option value="Cotrimoxazole"></option>
        <option value="Erythromycin"></option>
        <option value="Gentamicin"></option>
        <option value="Imipenem"></option>
        <option value="Meropenem"></option>
        <option value="Levofloxacin"></option>
        <option value="Linezolid"></option>
        <option value="Nitrofurantoin"></option>
        <option value="Norfloxacin"></option>
        <option value="Ofloxacin"></option>
        <option value="Penicillin"></option>
        <option value="Piperacillin/Tazobactam"></option>
        <option value="Tetracycline"></option>
        <option value="Tobramycin"></option>
        <option value="Vancomycin"></option>
    </datalist>

    @push('scripts')
    @endpush
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('resultEntrySortable', () => ({
            initSortable(el) {
                if (typeof Sortable === 'undefined') return;
                Sortable.create(el, {
                    draggable: '.sortable-item',
                    filter: '.ignore-sort',
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: (evt) => {
                        let orderedIds = [];
                        el.querySelectorAll('.sortable-item').forEach(tbody => {
                            let id = tbody.getAttribute('data-id');
                            if (id) orderedIds.push(id);
                        });
                        @this.call('updateTestOrder', orderedIds);
                    }
                });
            }
        }));
    });
</script>

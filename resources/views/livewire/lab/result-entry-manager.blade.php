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
                    <div class="col-md-6 border-end">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-text avatar-lg rounded-circle" style="background:rgba(59,113,202,0.1);">
                                <i class="feather-user text-primary fs-3"></i>
                            </div>
                            <div class="col-md-3">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Patient Info</div>
                                <div class="fw-bold fs-13">{{ $invoice->patient->name }} <span class="badge bg-soft-info text-info ms-1">{{ $invoice->patient->formatted_id }}</span></div>
                                <div class="fs-11 text-muted">{{ $invoice->patient->patientProfile->age ?? '--' }} {{ $invoice->patient->patientProfile->age_type ?? 'y' }} | {{ $invoice->patient->patientProfile->gender ?? '--' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center mt-3 mt-md-0">
                            <div class="col-6 border-end">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Invoice / PID</div>
                                <div class="fw-bold fs-14 text-dark">{{ $invoice->invoice_number }}</div>
                            </div>
                            <div class="col-6">
                                <div class="fs-11 text-muted text-uppercase fw-bold mb-1">Date</div>
                                <div class="fw-bold fs-14 text-dark">{{ $invoice->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 30%">Test Parameter</th>
                                <th style="width: 25%">Result Value</th>
                                <th style="width: 15%">Unit</th>
                                <th style="width: 20%">Reference Range</th>
                                <th style="width: 10%" class="text-center">Highlight</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedParams as $dept => $billItems)
                                {{-- Department Header --}}
                                <tr>
                                    <td colspan="5" class="bg-primary text-white py-2 fs-13 fw-bold">
                                        <i class="feather-layers me-2"></i>{{ strtoupper($dept) }}
                                    </td>
                                </tr>
                                
                                @foreach($billItems as $itemId => $testsInItem)
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
                                                        <input type="checkbox" class="form-check-input me-2" 
                                                               wire:model.live="selectedTests" value="{{ $itemId }}">
                                                        <i class="feather-box text-primary me-2"></i>{{ $billItemName }} (Package)
                                                        <span class="ms-2 badge {{ $isBillItemComplete ? 'bg-success' : 'bg-warning text-dark' }} fs-9">
                                                            {{ $isBillItemComplete ? 'Completed' : 'Pending' }}
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
                                                            <input type="checkbox" class="form-check-input me-2" 
                                                                   wire:model.live="selectedTests" value="{{ $itemId }}">
                                                        @endif
                                                        <i class="feather-activity text-muted me-2"></i>{{ $testName }}
                                                        @if(!($testItem->labTest->is_package ?? false))
                                                            <span class="ms-2 badge {{ $isBillItemComplete ? 'bg-success' : 'bg-warning text-dark' }} fs-9">
                                                                {{ $isBillItemComplete ? 'Completed' : 'Pending' }}
                                                            </span>
                                                        @endif
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
                                            </td>
                                        </tr>

                                        @foreach($params as $p)
                                            @php
                                                $paramKey = $p['key'];
                                                $isHigh = $highlights[$paramKey] ?? false;
                                            @endphp
                                            <tr class="{{ $isHigh ? 'table-danger' : '' }}" wire:key="param-{{ $paramKey }}">
                                                <td class="fw-bold fs-12 ps-4">
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
                                                            <input type="text" class="form-control bg-light fw-bold text-primary border-primary border-opacity-25" 
                                                                   wire:model="results.{{ $paramKey }}" readonly title="Auto-Calculated">
                                                            <span class="input-group-text bg-soft-primary"><i class="feather-cpu" style="font-size: 10px;"></i></span>
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
                                                <td class="fs-12 fw-medium text-dark">{{ $p['ref_range'] ?: '-' }}</td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch d-flex justify-content-center">
                                                        <input class="form-check-input" type="checkbox" 
                                                               wire:model.live="highlights.{{ $paramKey }}" 
                                                               style="width: 2.5em; height: 1.25em;">
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach

                                    {{-- Bill Item Level Comment --}}
                                    <tr>
                                        <td colspan="5" class="bg-light p-3 border-bottom" wire:ignore>
                                            <label class="form-label fw-bold fs-11 text-muted text-uppercase mb-1"><i class="feather-align-left me-1"></i>{{ $billItemName }} Feedback / Remarks</label>
                                            <textarea class="form-control" rows="2" 
                                                x-data x-init="
                                                    ClassicEditor
                                                        .create($el, {
                                                            toolbar: ['bold', 'italic', 'bulletedList', 'numberedList', 'undo', 'redo']
                                                        })
                                                        .then(editor => {
                                                            editor.model.document.on('change:data', () => {
                                                                @this.set('testComments.{{ $itemId }}', editor.getData());
                                                            });
                                                            editor.setData(@js($testComments[$itemId] ?? ''));
                                                        })
                                                " placeholder="Add specific interpretation for {{ $billItemName }}..."></textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
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

        @push('scripts')
        @endpush
</div>

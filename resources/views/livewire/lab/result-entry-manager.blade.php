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
                            <div>
                                <h5 class="mb-1 fw-bold">{{ $invoice->patient->name }}</h5>
                                <div class="fs-12 text-muted">
                                    {{ $invoice->patient->phone }} | {{ $invoice->patient->patientProfile->age ?? '--' }} {{ $invoice->patient->patientProfile->age_type ?? 'Y' }} / {{ $invoice->patient->patientProfile->gender ?? '--' }}
                                </div>
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
            <div class="alert alert-success alert-dismissible fade show">
                <i class="feather-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Entry Form Engine --}}
        <div class="card">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fs-13"><i class="feather-edit-3 text-primary me-2"></i>Enter Test Values</h6>
                    <div>
                        <span class="badge bg-soft-info text-info fs-11"><i class="feather-cpu me-1"></i>Auto-Calc Enabled</span>
                        <span class="badge bg-soft-warning text-warning fs-11 ms-2"><i class="feather-alert-circle me-1"></i>Auto-Flag Ranges</span>
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
                            @foreach($groupedParams as $dept => $tests)
                                {{-- Department Header --}}
                                <tr>
                                    <td colspan="5" class="bg-primary text-white py-2 fs-13 fw-bold">
                                        <i class="feather-layers me-2"></i>{{ strtoupper($dept) }}
                                    </td>
                                </tr>
                                
                                @foreach($tests as $testName => $params)
                                    {{-- Test Name Subheader --}}
                                    <tr>
                                        <td colspan="5" class="bg-light py-2 fs-12 fw-bold text-dark border-bottom">
                                            <i class="feather-activity text-muted me-2"></i>{{ $testName }}
                                        </td>
                                    </tr>

                                    @foreach($params as $k => $p)
                                        @php
                                            $itemKey = $p['lab_test_id'] . '_' . md5($p['name']);
                                            $isHigh = $highlights[$itemKey] ?? false;
                                        @endphp
                                        <tr class="{{ $isHigh ? 'table-danger' : '' }}">
                                            <td class="fw-bold fs-12 ps-4">
                                                {{ $p['name'] }}
                                                @if($isHigh)
                                                    <span class="ms-2 badge {{ $flags[$itemKey] == 'H' ? 'bg-danger' : 'bg-warning text-dark' }} px-2" style="font-size: 10px;">
                                                        {{ $flags[$itemKey] ?? 'Abnormal' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm w-75">
                                                    <input type="text" class="form-control {{ $isHigh ? 'border-danger text-danger fw-bold' : '' }}" 
                                                           wire:model.live.debounce.500ms="results.{{ $itemKey }}" 
                                                           placeholder="-">
                                                    @if($isHigh && isset($flags[$itemKey]))
                                                        <span class="input-group-text bg-danger text-white border-danger fw-bold fs-11 px-2" title="{{ $flags[$itemKey] == 'H' ? 'High Value' : 'Low Value' }}">
                                                            {{ $flags[$itemKey] }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="fs-12 text-muted">{{ $p['unit'] }}</td>
                                            <td class="fs-12">{{ $p['ref_range'] }}</td>
                                            <td class="text-center">
                                                <div class="form-check form-switch d-flex justify-content-center">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:model.live="highlights.{{ $itemKey }}" 
                                                           style="width: 2.5em; height: 1.25em;">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                {{-- Report Comments --}}
                <div class="px-4 py-3 bg-light border-top" wire:ignore>
                    <label class="form-label fw-bold fs-12 text-dark"><i class="feather-message-square me-2 text-primary"></i>Report Comments / Interpretation</label>
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
                        <button wire:click="saveReport('Draft')" class="btn btn-outline-primary fw-bold">
                            <i class="feather-save me-1"></i> Save Draft
                        </button>
                        <button wire:click="saveReport('Approved')" class="btn btn-success fw-bold px-4" {{ ($testReport && $testReport->status === 'Approved') ? 'disabled' : '' }}>
                            <i class="feather-check me-1"></i> Approve & Finalize
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold mb-0 fs-6 fs-md-5">{{ $test_id ? 'Edit Master Test' : 'Add New Master Test' }}</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.global-tests') }}" wire:navigate class="text-muted">Master Setup</a></li>
                <li class="breadcrumb-item text-primary fw-medium">{{ $test_id ? 'Edit Master' : 'New Master' }}</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <button type="button" class="btn btn-soft-info px-3 d-none d-md-flex align-items-center" data-bs-toggle="modal" data-bs-target="#testDocumentationModal">
                <i class="feather-book-open me-2"></i>Documentation
            </button>
            <a href="{{ route('admin.global-tests') }}" wire:navigate class="btn btn-light px-3 px-md-4">
                <i class="feather-arrow-left me-1 me-md-2"></i><span class="d-none d-sm-inline">Back to Library</span>
            </a>
            <button wire:click="save" class="btn btn-primary px-3 px-md-5">
                <i class="feather-save me-1 me-md-2"></i>{{ $test_id ? 'Update Master' : 'Save Master' }}
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        <form wire:submit.prevent="save">
            <div class="row g-4">
                <!-- Main Details Card -->
                <div class="col-xl-8">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-info text-primary me-2"></i>Master Test Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-4 col-md-3">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('test_code') is-invalid @enderror" wire:model="test_code" placeholder="E.G. CBC-01">
                                    @error('test_code') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-8 col-md-9">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Full name of the master test">
                                    @error('name') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Department / Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" wire:model="department_id">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">
                                                📂 {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-6 col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Suggested MRP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">₹</span>
                                        <input type="number" step="0.01" class="form-control @error('mrp') is-invalid @enderror" wire:model="mrp" placeholder="0.00">
                                    </div>
                                    @error('mrp') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Default B2B Rate</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">₹</span>
                                        <input type="number" step="0.01" class="form-control" wire:model="b2b_price" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Sample Type</label>
                                    <input type="text" class="form-control" wire:model="sample_type" placeholder="Blood, Serum…">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Method</label>
                                    <input type="text" class="form-control" wire:model="method" placeholder="CLIA, HPLC…">
                                </div>
                                <div class="col-6 col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">TAT (Hours)</label>
                                    <input type="number" class="form-control" wire:model="tat_hours">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Master Internal Description</label>
                                    <input type="text" class="form-control" wire:model="description" placeholder="Notes for administrators...">
                                </div>
                            </div>

                            <hr class="my-4 opacity-50">

                            <!-- Parameters Section -->
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Master Parameters</h6>
                                    <p class="fs-11 text-muted mb-0">Define default fields that will appear for all labs.</p>
                                </div>
                                <button type="button" wire:click="addParameter" class="btn btn-soft-primary btn-sm px-3 rounded-pill">
                                    <i class="feather-plus me-1"></i>Add Field
                                </button>
                            </div>

                            {{-- Desktop Table (hidden on small screens) --}}
                            <div class="d-none d-lg-block border rounded-4 bg-light p-1 shadow-sm">
                                <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="fs-10 text-uppercase text-muted fw-bold">
                                            <th class="ps-3 py-3" style="min-width: 180px;">Name</th>
                                            <th style="min-width: 70px;">Code</th>
                                            <th style="min-width: 120px;">Method</th>
                                            <th style="min-width: 100px;">Input</th>
                                            <th style="min-width: 250px;">Ref & Unit</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parameters as $index => $param)
                                            <tr wire:key="param-d-{{ $index }}" class="border-bottom border-white">
                                                <td class="ps-3 py-2">
                                                    <input type="text" class="form-control form-control-sm @error('parameters.'.$index.'.name') is-invalid @enderror" 
                                                        wire:model="parameters.{{ $index }}.name" placeholder="Parameter Name">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-center fw-bold text-primary" 
                                                        wire:model="parameters.{{ $index }}.short_code" placeholder="CODE">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" 
                                                        wire:model="parameters.{{ $index }}.method" placeholder="Method">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model.live="parameters.{{ $index }}.input_type">
                                                        <option value="numeric">Numerical</option>
                                                        <option value="text">Text/Qualitative</option>
                                                        <option value="selection">Dropdown List</option>
                                                        <option value="calculated">Formula</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="text" class="form-control form-control-sm" style="width: 80px;" wire:model="parameters.{{ $index }}.unit" placeholder="Unit">
                                                        <button type="button" wire:click="openRangeModal({{ $index }})" 
                                                            class="btn btn-sm {{ count($parameters[$index]['ranges'] ?? []) > 0 ? 'btn-soft-success' : 'btn-soft-primary' }} flex-grow-1 py-1 rounded-pill">
                                                            <i class="feather-settings me-1"></i>Config
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-end pe-3">
                                                    <button type="button" wire:click="removeParameter({{ $index }})" class="btn btn-icon btn-soft-danger btn-sm border-0">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>

                            {{-- Mobile Layout --}}
                            <div class="d-lg-none">
                                @foreach($parameters as $index => $param)
                                    <div wire:key="param-m-{{ $index }}" class="card border shadow-sm rounded-3 mb-2">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold fs-10 px-2">{{ $index + 1 }}</span>
                                                <input type="text" class="form-control form-control-sm flex-grow-1 @error('parameters.'.$index.'.name') is-invalid @enderror" 
                                                    wire:model="parameters.{{ $index }}.name" placeholder="Parameter Name">
                                                <button type="button" wire:click="removeParameter({{ $index }})" class="btn btn-icon btn-soft-danger btn-sm border-0 flex-shrink-0">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-4">
                                                    <input type="text" class="form-control form-control-sm text-center fw-bold text-primary" 
                                                        wire:model="parameters.{{ $index }}.short_code" placeholder="CODE">
                                                </div>
                                                <div class="col-8">
                                                    <select class="form-select form-select-sm" wire:model.live="parameters.{{ $index }}.input_type">
                                                        <option value="numeric">Numerical</option>
                                                        <option value="text">Text</option>
                                                        <option value="selection">Dropdown</option>
                                                        <option value="calculated">Formula</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side Cards -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-file-text text-primary me-2"></i>Master Interpretation</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-0" wire:ignore>
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-2">Clinical Notes for All Labs</label>
                                <textarea class="form-control rich-editor" id="global-interpretation-editor" 
                                    x-data x-init="
                                        ClassicEditor
                                            .create($el, {
                                                toolbar: ['bold', 'italic', 'link', 'bulletedList', 'numberedList', 'insertTable', 'undo', 'redo']
                                            })
                                            .then(editor => {
                                                editor.model.document.on('change:data', () => {
                                                    @this.set('interpretation', editor.getData());
                                                });
                                                editor.setData(@js($interpretation));
                                            })
                                    "></textarea>
                            </div>
                            <div class="mt-4 p-3 bg-soft-info text-info rounded-4 border fs-12">
                                <i class="feather-help-circle me-2"></i>This content will be synced to labs when they import this test. It can be edited by labs later.
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 mb-2">
                                <i class="feather-check-circle me-2"></i>Finalize & Save Master
                            </button>
                            <a href="{{ route('admin.global-tests') }}" wire:navigate class="btn btn-light w-100 py-3 fw-bold rounded-3">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Range Modal (Same as lab version for consistency) -->
    @if($isRangeModalOpen && $editingParamIndex !== null)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index: 1060;" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light p-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-settings text-primary me-2"></i>
                            Configure: {{ $parameters[$editingParamIndex]['name'] ?: 'Unnamed Parameter' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeRangeModal"></button>
                    </div>
                    <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase">Input Type</label>
                                <select class="form-select" wire:model.live="parameters.{{ $editingParamIndex }}.input_type">
                                    <option value="numeric">Numerical (Normal evaluation)</option>
                                    <option value="text">Free Text / Qualitative</option>
                                    <option value="selection">Dropdown Options</option>
                                    <option value="calculated">Calculated Formula</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase">Parameter Unit</label>
                                <input type="text" class="form-control" wire:model="parameters.{{ $editingParamIndex }}.unit" placeholder="e.g. mg/dL, %">
                            </div>
                        </div>

                        @if($parameters[$editingParamIndex]['input_type'] === 'calculated')
                            <div class="bg-soft-primary p-3 rounded-4 mb-4 border border-primary border-opacity-25">
                                <label class="form-label fs-11 fw-bold text-primary text-uppercase mb-2">Calculation Formula</label>
                                <input type="text" class="form-control form-control-lg border-primary text-primary fw-bold" 
                                    wire:model="parameters.{{ $editingParamIndex }}.formula" placeholder="Example: {HB} * 3">
                            </div>
                        @endif

                        @if($parameters[$editingParamIndex]['input_type'] === 'selection')
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-0">Dropdown Options</label>
                                    <button type="button" wire:click="addOption" class="btn btn-soft-primary btn-xs py-0 px-2 rounded-pill">Add Option</button>
                                </div>
                                <div class="d-flex flex-wrap gap-2 p-3 bg-light rounded-4 border">
                                    @foreach($parameters[$editingParamIndex]['options'] ?? [] as $optIdx => $opt)
                                        <div class="input-group input-group-sm" style="width: auto; min-width: 120px;">
                                            <input type="text" class="form-control" wire:model="parameters.{{ $editingParamIndex }}.options.{{ $optIdx }}">
                                            <button class="btn btn-outline-danger" type="button" wire:click="removeOption({{ $optIdx }})"><i class="feather-x"></i></button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                            <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-0">Default Reference Ranges</label>
                            <button type="button" wire:click="addRange" class="btn btn-soft-primary btn-xs py-0 px-2 rounded-pill">Add Range Variation</button>
                        </div>

                        <div class="table-responsive border rounded-4 bg-white shadow-sm mb-3">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="fs-10 text-muted text-uppercase fw-bold">
                                        <th class="ps-3 py-3">Gender</th>
                                        <th>Age Range</th>
                                        <th>Min Val</th>
                                        <th>Max Val</th>
                                        <th>Display (Report)</th>
                                        <th class="text-end pe-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($parameters[$editingParamIndex]['ranges'] ?? [] as $rIndex => $range)
                                        <tr wire:key="range-{{ $rIndex }}">
                                            <td class="ps-3">
                                                <select class="form-select form-select-sm" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.gender">
                                                    <option value="Both">Both</option>
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-1">
                                                    <input type="number" class="form-control form-control-sm px-1 text-center" style="width: 45px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.age_min">
                                                    <span class="fs-10 text-muted">-</span>
                                                    <input type="number" class="form-control form-control-sm px-1 text-center" style="width: 45px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.age_max">
                                                    <select class="form-select form-select-sm px-1" style="width: 85px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.age_unit">
                                                        <option value="Years">Years</option>
                                                        <option value="Months">Months</option>
                                                        <option value="Days">Days</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td><input type="text" class="form-control form-control-sm text-center" style="width: 65px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.min_val" placeholder="Min"></td>
                                            <td><input type="text" class="form-control form-control-sm text-center" style="width: 65px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.max_val" placeholder="Max"></td>
                                            <td><input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.display_range" placeholder="e.g. 13.5 - 17.5"></td>
                                            <td class="text-end pe-3">
                                                <button type="button" wire:click="removeRange({{ $rIndex }})" class="btn btn-icon btn-soft-danger btn-xs border-0">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0 p-4">
                        <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3" wire:click="closeRangeModal">Done, Save Master Config</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .ck-editor__editable { min-height: 400px; border-radius: 0 0 12px 12px !important; border-color: #e2e8f0 !important; }
    </style>
    
    <!-- Test Documentation Modal -->
    @include('components.test-documentation-modal')

</div>

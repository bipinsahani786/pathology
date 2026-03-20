<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">{{ $test_id ? 'Edit Lab Test' : 'Add New Lab Test' }}</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lab.tests') }}" wire:navigate class="text-muted">Test Catalog</a></li>
                <li class="breadcrumb-item text-primary fw-medium">{{ $test_id ? 'Edit Test' : 'New Test' }}</li>
            </ul>
        </div>
        <div class="page-header-right">
            <a href="{{ route('lab.tests') }}" wire:navigate class="btn btn-light px-4 me-2">
                <i class="feather-arrow-left me-2"></i>Back to Catalog
            </a>
            <button wire:click="save" class="btn btn-primary px-5">
                <i class="feather-save me-2"></i>{{ $test_id ? 'Update Test' : 'Save Test' }}
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        <form wire:submit.prevent="save">
            <div class="row g-4">
                <!-- Main Details Card -->
                <div class="col-xl-8">
                    <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-info text-primary me-2"></i>Primary Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Code</label>
                                    <input type="text" class="form-control" wire:model="test_code" placeholder="E.G. CBC-01">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Full name of the test">
                                    @error('name') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Department / Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" wire:model="department_id">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">
                                                {{ $dept->is_system ? '🛡️ ' : '📂 ' }} {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Price / MRP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">₹</span>
                                        <input type="number" step="0.01" class="form-control @error('mrp') is-invalid @enderror" wire:model="mrp">
                                    </div>
                                    @error('mrp') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">B2B Rate</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">₹</span>
                                        <input type="number" step="0.01" class="form-control" wire:model="b2b_price">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Sample Type</label>
                                    <input type="text" class="form-control" wire:model="sample_type" placeholder="Serum, Blood, etc.">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">TAT (Hours)</label>
                                    <input type="number" class="form-control" wire:model="tat_hours">
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Internal Description</label>
                                    <input type="text" class="form-control" wire:model="description" placeholder="Notes for lab staff...">
                                </div>
                            </div>

                            <hr class="my-4 opacity-50">

                            <!-- Parameters Section -->
                            <div class="mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Report Parameters</h6>
                                    <p class="fs-11 text-muted mb-0">Define fields that will appear on the final report.</p>
                                </div>
                                <button type="button" wire:click="addParameter" class="btn btn-soft-primary btn-sm px-3 rounded-pill">
                                    <i class="feather-plus me-1"></i>Add Field
                                </button>
                            </div>

                            <div class="table-responsive border rounded-4 bg-light p-1 shadow-sm overflow-visible">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-white">
                                        <tr class="fs-10 text-uppercase text-muted fw-bold">
                                            <th class="ps-3 py-3" style="min-width: 200px;">Param Name</th>
                                            <th style="min-width: 80px;">S.Code</th>
                                            <th style="min-width: 100px;">Input</th>
                                            <th style="min-width: 120px;">Range Type</th>
                                            <th style="min-width: 300px;">Reference & Units</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parameters as $index => $param)
                                            <tr wire:key="param-{{ $index }}" class="border-bottom border-white">
                                                <td class="ps-3 py-2">
                                                    <input type="text" class="form-control form-control-sm @error('parameters.'.$index.'.name') is-invalid @enderror" 
                                                        wire:model="parameters.{{ $index }}.name" placeholder="Parameter Name">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-center fw-bold text-primary" 
                                                        wire:model="parameters.{{ $index }}.short_code" placeholder="CODE">
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
                                                    <input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $index }}.unit" placeholder="Unit">
                                                </td>
                                                <td>
                                                    <button type="button" wire:click="openRangeModal({{ $index }})" 
                                                        class="btn btn-sm {{ count($parameters[$index]['ranges'] ?? []) > 0 ? 'btn-soft-success' : 'btn-soft-primary' }} w-100 py-1 rounded-pill">
                                                        <i class="feather-settings me-1"></i>
                                                        {{ count($parameters[$index]['ranges'] ?? []) }} Range(s) / Config
                                                    </button>
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
                    </div>
                </div>

                <!-- Interpretation Side Card -->
                <div class="col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white py-3">
                            <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-file-text text-primary me-2"></i>Interpretation Template</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-0" wire:ignore>
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-2">Clinical Interpretation (HTML)</label>
                                <textarea class="form-control rich-editor" id="lab-interpretation-editor" 
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
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" wire:model="is_active" id="test-active">
                                <label class="form-check-label fw-bold text-dark" for="test-active">Active in Catalog</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 mb-2">
                                <i class="feather-check-circle me-2"></i>Complete & Save
                            </button>
                            <a href="{{ route('lab.tests') }}" wire:navigate class="btn btn-light w-100 py-3 fw-bold rounded-3">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Advanced Range / Config Modal -->
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
                    <!-- General Settings -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fs-11 fw-bold text-muted text-uppercase">Input Type</label>
                            <select class="form-select" wire:model.live="parameters.{{ $editingParamIndex }}.input_type">
                                <option value="numeric">Numerical (Normal evaluation)</option>
                                <option value="text">Free Text / Qualitative</option>
                                <option value="selection">Dropdown Options (e.g. Widal Titers)</option>
                                <option value="calculated">Calculated Formula</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-11 fw-bold text-muted text-uppercase">Parameter Unit</label>
                            <input type="text" class="form-control" wire:model="parameters.{{ $editingParamIndex }}.unit" placeholder="e.g. mg/dL, %">
                        </div>
                    </div>

                    <!-- Formula Section -->
                    @if($parameters[$editingParamIndex]['input_type'] === 'calculated')
                    <div class="bg-soft-primary p-3 rounded-4 mb-4 border border-primary border-opacity-25">
                        <label class="form-label fs-11 fw-bold text-primary text-uppercase mb-2">Calculation Formula</label>
                        <input type="text" class="form-control form-control-lg border-primary text-primary fw-bold" 
                            wire:model="parameters.{{ $editingParamIndex }}.formula" placeholder="Example: {HB} * 3">
                        <p class="fs-10 text-muted mt-2 mb-0">Use parameter codes in curly braces, e.g., <code>{HB}</code>, <code>{RBC}</code>.</p>
                    </div>
                    @endif

                    <!-- Options Section for Selection -->
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
                            @if(empty($parameters[$editingParamIndex]['options']))
                            <span class="text-muted fs-11">No options added yet. Click 'Add Option' to start.</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Advanced Ranges Section -->
                    <div class="d-flex justify-content-between align-items-center mb-2 mt-4">
                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-0">Reference Ranges (Age/Gender Based)</label>
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
                                    <th>Display Text (Report)</th>
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
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-center" style="width: 65px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.min_val" placeholder="Min">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-center" style="width: 65px;" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.max_val" placeholder="Max">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $editingParamIndex }}.ranges.{{ $rIndex }}.display_range" placeholder="e.g. 13.5 - 17.5">
                                    </td>
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
                    <button type="button" class="btn btn-primary w-100 py-2 fw-bold rounded-3" wire:click="closeRangeModal">Done, Save Configuration</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <style>
        .btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
        .fs-9 { font-size: 9px; }
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .ck-editor__editable { min-height: 400px; border-radius: 0 0 12px 12px !important; border-color: #e2e8f0 !important; }
    </style>
</div>

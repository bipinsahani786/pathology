<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">{{ $test_id ? 'Edit Master Test' : 'Add New Global Test' }}</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.global-tests') }}" wire:navigate class="text-muted">Master Setup</a></li>
                <li class="breadcrumb-item text-primary fw-medium">{{ $test_id ? 'Edit Test' : 'New Test' }}</li>
            </ul>
        </div>
        <div class="page-header-right">
            <a href="{{ route('admin.global-tests') }}" wire:navigate class="btn btn-light px-4 me-2">
                <i class="feather-arrow-left me-2"></i>Back to Library
            </a>
            <button wire:click="save" class="btn btn-primary px-5">
                <i class="feather-save me-2"></i>{{ $test_id ? 'Update Master' : 'Save Master' }}
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
                            <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-info text-primary me-2"></i>Test Information</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('test_code') is-invalid @enderror" wire:model="test_code" placeholder="E.G. CBC-01">
                                    @error('test_code') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Test Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="E.G. Complete Blood Count">
                                    @error('name') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">System Department <span class="text-danger">*</span></label>
                                    <select class="form-select @error('department_id') is-invalid @enderror" wire:model="department_id">
                                        <option value="">Select Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Suggested Price (₹)</label>
                                    <input type="number" step="0.01" class="form-control" wire:model="suggested_price" placeholder="0.00">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Short Description</label>
                                    <textarea class="form-control" wire:model="description" rows="2" placeholder="Reference for administrators..."></textarea>
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

                            <div class="table-responsive border rounded-4 bg-light p-1 shadow-sm overflow-visible">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-white">
                                        <tr class="fs-10 text-uppercase text-muted fw-bold">
                                            <th class="ps-3 py-3" style="min-width: 200px;">Internal Name</th>
                                            <th style="min-width: 100px;">Code</th>
                                            <th style="min-width: 120px;">Mode</th>
                                            <th style="min-width: 140px;">Range Type</th>
                                            <th style="min-width: 300px;">Reference Logic & Units</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($parameters as $index => $param)
                                            <tr wire:key="param-{{ $index }}" class="border-bottom border-white">
                                                <td class="ps-3 py-2">
                                                    <input type="text" class="form-control form-control-sm @error('parameters.'.$index.'.name') is-invalid @enderror" 
                                                        wire:model="parameters.{{ $index }}.name">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm text-center fw-bold" 
                                                        wire:model="parameters.{{ $index }}.short_code" placeholder="HB">
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model.live="parameters.{{ $index }}.input_type">
                                                        <option value="numeric">Numeric</option>
                                                        <option value="text">Textual</option>
                                                        <option value="calculated">Calculated</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" wire:model.live="parameters.{{ $index }}.range_type">
                                                        <option value="general">Global</option>
                                                        <option value="gender">M/F Split</option>
                                                        <option value="value">Qualitative</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column gap-1">
                                                        <div class="row g-1">
                                                            <div class="col-8">
                                                                @if(($parameters[$index]['range_type'] ?? 'general') === 'general')
                                                                    <input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $index }}.general_range" placeholder="e.g. 13 - 17">
                                                                @elseif(($parameters[$index]['range_type'] ?? 'general') === 'gender')
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text p-1 fs-9">M</span>
                                                                        <input type="text" class="form-control" wire:model="parameters.{{ $index }}.male_range">
                                                                        <span class="input-group-text p-1 fs-9">F</span>
                                                                        <input type="text" class="form-control" wire:model="parameters.{{ $index }}.female_range">
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $index }}.normal_value" placeholder="Positive">
                                                                @endif
                                                            </div>
                                                            <div class="col-4">
                                                                <input type="text" class="form-control form-control-sm" wire:model="parameters.{{ $index }}.unit" placeholder="Unit">
                                                            </div>
                                                        </div>
                                                        @if(($parameters[$index]['input_type'] ?? 'numeric') === 'calculated')
                                                            <input type="text" class="form-control form-control-sm border-primary text-primary fw-bold" 
                                                                wire:model="parameters.{{ $index }}.formula" placeholder="Formula: {HB}*3">
                                                        @endif
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

                    <!-- Status Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 mb-2">
                                <i class="feather-check-circle me-2"></i>Finalize & Save Test
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

    <style>
        .fs-9 { font-size: 9px; }
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .ck-editor__editable { min-height: 350px; border-radius: 0 0 12px 12px !important; border-color: #e2e8f0 !important; }
    </style>
</div>

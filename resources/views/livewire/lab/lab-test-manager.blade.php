<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10">Lab Test Catalog</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item text-primary">Test Catalog</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <button wire:click="openImportModal" class="btn btn-outline-primary w-100 w-md-auto shadow-sm bg-white">
                <i class="feather-download"></i>
                <span class="d-none d-sm-inline ms-2">Import Library</span>
            </button>
            <button wire:click="create" class="btn btn-primary w-100 w-md-auto shadow-sm">
                <i class="feather-plus"></i>
                <span class="d-none d-sm-inline ms-2">Add Custom Test</span>
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3">

                    <div class="col-md-7">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm"
                                class="form-control"
                                placeholder="Search test name or code...">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select wire:model.live="filterCategory" class="form-select shadow-sm">
                            <option value="">All Departments</option>
                            <option value="Haematology">Haematology</option>
                            <option value="Biochemistry">Biochemistry</option>
                            <option value="Serology">Serology</option>
                            <option value="Pathology">Pathology</option>
                            <option value="Microbiology">Microbiology</option>
                            <option value="Clinical Pathology">Clinical Pathology</option>
                            <option value="Immunology">Immunology</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button wire:click="$set('searchTerm',''); $set('filterCategory','')"
                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                            title="Reset Filters">
                            <i class="feather-refresh-ccw fs-12 me-1"></i>
                            <span>Reset</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light fs-11 text-uppercase text-muted">
                            <tr>
                                <th class="ps-4">Code</th>
                                <th>Test Name & Dept</th>
                                <th>Pricing</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tests as $test)
                                <tr wire:key="test-{{ $test->id }}" class="border-bottom border-light">
                                    <td class="ps-4">
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">{{ $test->test_code ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">{{ $test->name }}</div>
                                        <small class="text-muted fw-medium">{{ $test->department }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary fs-14">₹{{ number_format($test->mrp, 2) }}</div>
                                        <div class="fs-11 text-muted">B2B: ₹{{ number_format($test->b2b_price, 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $test->id }})"
                                                {{ $test->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <span class="fs-12 ms-2 fw-medium {{ $test->is_active ? 'text-success' : 'text-danger' }}">
                                                {{ $test->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button wire:click="edit({{ $test->id }})"
                                                class="avatar-text avatar-md bg-soft-info text-info rounded border-0"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </button>
                                            <button wire:click="delete({{ $test->id }})"
                                                wire:confirm="Are you sure you want to delete this lab test? This action cannot be undone."
                                                class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0"
                                                data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted mb-2"><i class="feather-inbox fs-1"></i></div>
                                        <h6 class="fw-bold text-dark">No Lab Tests Found</h6>
                                        <p class="text-muted fs-13">Start by adding a custom test or importing from the global library.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3">{{ $tests->links() }}</div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-{{ $test_id ? 'edit' : 'plus-circle' }} text-primary me-2"></i>
                            {{ $test_id ? 'Edit Lab Test' : 'New Custom Test' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Code</label>
                                <input type="text" class="form-control" wire:model="test_code" placeholder="E.g. CBC-01">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="E.g. Complete Blood Count">
                                @error('name') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Department <span class="text-danger">*</span></label>
                                <select class="form-select @error('department') is-invalid @enderror" wire:model="department">
                                    <option value="">Select Department</option>
                                    <option value="Haematology">Haematology</option>
                                    <option value="Biochemistry">Biochemistry</option>
                                    <option value="Serology">Serology</option>
                                    <option value="Pathology">Pathology</option>
                                    <option value="Microbiology">Microbiology</option>
                                    <option value="Immunology">Immunology</option>
                                    <option value="Clinical Pathology">Clinical Pathology</option>
                                </select>
                                @error('department') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">MRP (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control @error('mrp') is-invalid @enderror" wire:model="mrp" placeholder="0.00">
                                @error('mrp') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">B2B Price (₹)</label>
                                <input type="number" step="0.01" class="form-control" wire:model="b2b_price" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Sample Type</label>
                                <input type="text" class="form-control" wire:model="sample_type" placeholder="Serum">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">TAT (Hours)</label>
                                <input type="number" class="form-control" wire:model="tat_hours">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Description / Instruction</label>
                                <input type="text" class="form-control @error('description') is-invalid @enderror" wire:model="description" placeholder="E.g. Fasting required for 10-12 hours">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Interpretation Details <span class="text-info fw-normal">(Supports HTML tables — shown on report)</span></label>
                                <textarea class="form-control" wire:model="interpretation" rows="4" placeholder="You can paste HTML tables here."></textarea>
                            </div>
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                            <div>
                                <h6 class="fw-bold text-dark mb-0">Test Parameters, Ranges & Formulas</h6>
                                <p class="fs-12 text-muted mb-0">Configure master ranges or calculation formulas for results.</p>
                            </div>
                            <button type="button" wire:click="addParameter" class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                                <i class="feather-plus me-1"></i> Add Row
                            </button>
                        </div>

                        <div class="border rounded" style="overflow: visible;">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3 py-2" style="width: 18%;">Parameter Name <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">Short Code</th>
                                        <th style="width: 12%;">Input Type</th>
                                        <th style="width: 15%;">Range Type</th>
                                        <th style="width: 25%;">Ref Range & Formula</th>
                                        <th style="width: 12%;">Unit</th>
                                        <th class="text-end pe-3" style="width: 8%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($parameters as $index => $param)
                                        <tr wire:key="param-row-{{ $index }}" class="border-bottom border-light">
                                            <td class="ps-3 align-top pt-2 pb-2">
                                                <input type="text" class="form-control form-control-sm w-100" wire:model="parameters.{{ $index }}.name" placeholder="Name">
                                                @error('parameters.' . $index . '.name') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="align-top pt-2 pb-2">
                                                <input type="text" class="form-control form-control-sm text-uppercase w-100" wire:model="parameters.{{ $index }}.short_code" placeholder="CODE">
                                            </td>
                                            <td class="align-top pt-2 pb-2">
                                                <select class="form-select form-select-sm w-100" wire:model.live="parameters.{{ $index }}.input_type">
                                                    <option value="numeric">Numeric</option>
                                                    <option value="text">Textual</option>
                                                    <option value="calculated">Calculated</option>
                                                </select>
                                            </td>
                                            <td class="align-top pt-2 pb-2">
                                                <select class="form-select form-select-sm w-100" wire:model.live="parameters.{{ $index }}.range_type">
                                                    <option value="general">General</option>
                                                    <option value="gender">Gender Specific</option>
                                                    <option value="value">Qualitative</option>
                                                </select>
                                            </td>
                                            <td class="align-top pt-2 pb-2">
                                                <input type="text" class="form-control form-control-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'general' ? '' : 'd-none' }}" wire:model="parameters.{{ $index }}.general_range" placeholder="e.g. 70 - 100">

                                                <div class="input-group input-group-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'gender' ? 'd-flex' : 'd-none' }}">
                                                    <span class="input-group-text px-2 text-primary bg-light">M</span>
                                                    <input type="text" class="form-control px-2" wire:model="parameters.{{ $index }}.male_range" placeholder="Range">
                                                    <span class="input-group-text px-2 text-danger bg-light border-start-0">F</span>
                                                    <input type="text" class="form-control px-2" wire:model="parameters.{{ $index }}.female_range" placeholder="Range">
                                                </div>

                                                <input type="text" class="form-control form-control-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'value' ? '' : 'd-none' }}" wire:model="parameters.{{ $index }}.normal_value" placeholder="e.g. Negative">

                                                <input type="text" class="form-control form-control-sm border-info bg-soft-info w-100 mt-1 {{ ($parameters[$index]['input_type'] ?? 'numeric') === 'calculated' ? '' : 'd-none' }}" wire:model="parameters.{{ $index }}.formula" placeholder="Formula: {TC} - {HDL}">
                                            </td>
                                            <td class="align-top pt-2 pb-2">
                                                <input type="text" class="form-control form-control-sm w-100" wire:model="parameters.{{ $index }}.unit" placeholder="Unit">
                                            </td>
                                            <td class="text-end pe-3 align-top pt-2 pb-2">
                                                <button type="button" wire:click="removeParameter({{ $index }})" class="btn btn-sm btn-icon btn-light text-danger border shadow-sm rounded">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted fs-13">No parameters added. Click '+ Add Row' to start.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm"><i class="feather-x me-2"></i> Cancel</button>
                        <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm d-flex align-items-center">
                            <div wire:loading.remove wire:target="store">
                                <i class="feather-save me-2"></i> {{ $test_id ? 'Update Lab Test' : 'Save Lab Test' }}
                            </div>
                            <div wire:loading wire:target="store">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...
                            </div>
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    @endif

    @if ($isImportModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="feather-globe text-primary me-2"></i>Global Test Library</h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>
                    <div class="modal-body p-4 bg-white">

                        <div class="input-group mb-4">
                            <span class="input-group-text bg-white text-muted">
                                <i class="feather-search"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="globalSearch" class="form-control py-2 shadow-none" placeholder="Search Master Catalog (e.g. Lipid Profile, CBC)...">
                        </div>

                        <div class="list-group border rounded-4 overflow-hidden" style="max-height: 400px; overflow-y: auto;">
                            @forelse($globalTests as $gTest)
                                <div class="list-group-item d-flex align-items-center justify-content-between py-3 px-4 bg-white hover-bg-light transition-all">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <h6 class="mb-0 fw-bold text-dark">{{ $gTest->name }}</h6>
                                            <span class="badge bg-soft-primary text-primary ms-2 fs-10">{{ $gTest->test_code }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <small class="text-muted text-uppercase fs-11 fw-medium"><i class="feather-tag me-1"></i>{{ $gTest->category }}</small>
                                            <small class="text-dark fs-11 fw-bold"><i class="feather-pocket me-1"></i>Sugg: ₹{{ $gTest->suggested_price ?? 0 }}</small>
                                            <small class="text-info fs-11 fw-medium"><i class="feather-layers me-1"></i>{{ count($gTest->default_parameters ?? []) }} Params</small>
                                        </div>
                                    </div>
                                    <button wire:click="importGlobalTest({{ $gTest->id }})" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                        <div wire:loading.remove wire:target="importGlobalTest({{ $gTest->id }})">Import</div>
                                        <div wire:loading wire:target="importGlobalTest({{ $gTest->id }})"><span class="spinner-border spinner-border-sm" role="status"></span></div>
                                    </button>
                                </div>
                            @empty
                                <div class="p-5 text-center bg-white">
                                    <i class="feather-search text-muted fs-1 mb-2 d-block"></i>
                                    <h6 class="text-muted fw-bold mb-0">No master tests found matching your search.</h6>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

      <style>
        .form-select-sm,
        .form-control-sm {
            padding: 0.4rem 0.5rem;
            border-color: #e2e8f0;
        }

        .bg-soft-primary {
            background-color: rgba(59, 113, 202, 0.08) !important;
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.08) !important;
        }

        .bg-soft-info {
            background-color: rgba(23, 162, 184, 0.08) !important;
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.08) !important;
        }

        .text-primary {
            color: #3b71ca !important;
        }

        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6c757d;
            border-top: none !important;
        }

        /* Hover effect for list items */
        .hover-bg-light:hover {
            background-color: #f8fafc !important;
        }

        @media (max-width: 768px) {
            .modal-xl {
                max-width: 100%;
                margin: 0.5rem;
            }
        }
    </style>
</div>
<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10">Global Test Library</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item">Master Setup</li>
                <li class="breadcrumb-item text-primary">Global Tests</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary w-100 w-md-auto shadow-sm">
                <i class="feather-plus"></i>
                <span class="d-none d-sm-inline ms-2">Add New Test</span>
            </button>
        </div>
    </div>
    <div class="main-content">

        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show border-success" role="alert">
                <div class="d-flex align-items-center">
                    <i class="feather-check-circle fs-4 text-success me-2"></i>
                    <strong>{{ session('message') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

                    <div class="flex-grow-1" style="max-width: 450px;">
                        <div class="input-group input-group-sm border rounded-pill px-3 bg-light">
                            <span class="input-group-text bg-transparent border-0 pe-2">
                                <i class="feather-search text-muted"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm"
                                class="form-control border-0 bg-transparent py-2 shadow-none"
                                placeholder="Search test name or code...">
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fs-13 d-none d-lg-block me-1">Filter by:</span>
                        <div style="min-width: 180px;">
                            <select wire:model.live="filterCategory"
                                class="form-select form-select-sm border shadow-none bg-white py-2 px-3 rounded-pill">
                                <option value="">All Categories</option>
                                <option value="Haematology">Haematology</option>
                                <option value="Biochemistry">Biochemistry</option>
                                <option value="Serology">Serology</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Microbiology">Microbiology</option>
                                <option value="Clinical Pathology">Clinical Pathology</option>
                                <option value="Immunology">Immunology</option>
                            </select>
                        </div>
                        <button wire:click="$set('filterCategory',''); $set('searchTerm','')"
                            class="btn btn-sm btn-outline-light text-dark border py-2 px-3 d-flex align-items-center bg-white shadow-sm rounded-pill"
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
                                <th class="ps-4">Test Code</th>
                                <th>Test Name & Desc</th>
                                <th>Category</th>
                                <th>Parameters</th>
                                <th>Sugg. Price (₹)</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tests as $test)
                                <tr>
                                    <td class="ps-4"><span
                                            class="badge bg-soft-primary text-primary fw-bold">{{ $test->test_code }}</span>
                                    </td>
                                    <td>
                                        <span class="d-block fw-bold text-dark">{{ $test->name }}</span>
                                        <span
                                            class="fs-12 text-muted text-truncate-1-line">{{ Str::limit($test->description ?? 'No description added', 40) }}</span>
                                    </td>
                                    <td><span class="badge bg-soft-success text-success">{{ $test->category }}</span>
                                    </td>
                                    <td><span
                                            class="badge bg-soft-info text-info">{{ is_array($test->default_parameters) ? count($test->default_parameters) : 0 }}
                                            Params</span></td>
                                    <td class="fw-semibold text-dark">
                                        {{ $test->suggested_price ? '₹' . $test->suggested_price : 'N/A' }}</td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button wire:click="edit({{ $test->id }})"
                                                class="avatar-text avatar-md bg-soft-info text-info rounded border-0"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </button>
                                            <button wire:click="delete({{ $test->id }})"
                                                wire:confirm="Are you sure you want to delete this master test?"
                                                class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0"
                                                data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="feather-inbox fs-1 d-block mb-2"></i>
                                        No Master Tests Found. Click "Add New Test" to begin.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-3">
                {{ $tests->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-{{ $test_id ? 'edit' : 'plus-circle' }} text-primary me-2"></i>
                            {{ $test_id ? 'Update Global Test' : 'Add New Global Test' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"
                            aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                            <div class="row g-3 mb-4">

                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('test_code') is-invalid @enderror"
                                        wire:model="test_code" placeholder="e.g. CBC-01">
                                    @error('test_code')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="e.g. Complete Blood Count">
                                    @error('name')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Category / Dept
                                        <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                        wire:model="category">
                                        <option value="">Select Category</option>
                                        <option value="Haematology">Haematology</option>
                                        <option value="Biochemistry">Biochemistry</option>
                                        <option value="Serology">Serology</option>
                                        <option value="Pathology">Pathology</option>
                                        <option value="Microbiology">Microbiology</option>
                                        <option value="Clinical Pathology">Clinical Pathology</option>
                                        <option value="Immunology">Immunology</option>
                                    </select>
                                    @error('category')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Suggested Price
                                        (₹)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('suggested_price') is-invalid @enderror"
                                        wire:model="suggested_price" placeholder="0.00">
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label fs-12 fw-bold text-muted text-uppercase">Test Description
                                        / Instruction</label>
                                    <input type="text"
                                        class="form-control @error('description') is-invalid @enderror"
                                        wire:model="description" placeholder="e.g. Fasting required for 10-12 hours">
                                </div>
                            </div>

                            <div
                                class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">Test Parameters, Ranges & Formulas</h6>
                                    <p class="fs-12 text-muted mb-0">These will be synced to all labs when they import
                                        this test.</p>
                                </div>
                                <button type="button" wire:click="addParameter"
                                    class="btn btn-sm btn-primary shadow-sm rounded-pill px-3">
                                    <i class="feather-plus me-1"></i> Add Row
                                </button>
                            </div>

                            <div class="border rounded" style="overflow: visible;">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-3 py-2" style="width: 18%;">Parameter Name <span
                                                    class="text-danger">*</span></th>
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
                                            <tr wire:key="param-row-{{ $index }}"
                                                class="border-bottom border-light">

                                                <td class="ps-3 align-top pt-2 pb-2">
                                                    <input type="text" class="form-control form-control-sm w-100"
                                                        wire:model="parameters.{{ $index }}.name"
                                                        placeholder="Name">
                                                    @error('parameters.' . $index . '.name')
                                                        <span class="text-danger fs-11">{{ $message }}</span>
                                                    @enderror
                                                </td>

                                                <td class="align-top pt-2 pb-2">
                                                    <input type="text"
                                                        class="form-control form-control-sm text-uppercase w-100"
                                                        wire:model="parameters.{{ $index }}.short_code"
                                                        placeholder="CODE">
                                                </td>

                                                <td class="align-top pt-2 pb-2">
                                                    <select class="form-select form-select-sm w-100"
                                                        wire:model.live="parameters.{{ $index }}.input_type">
                                                        <option value="numeric">Numeric</option>
                                                        <option value="text">Textual</option>
                                                        <option value="calculated">Calculated</option>
                                                    </select>
                                                </td>

                                                <td class="align-top pt-2 pb-2">
                                                    <select class="form-select form-select-sm w-100"
                                                        wire:model.live="parameters.{{ $index }}.range_type">
                                                        <option value="general">General</option>
                                                        <option value="gender">Gender Specific</option>
                                                        <option value="value">Qualitative</option>
                                                    </select>
                                                </td>

                                                <td class="align-top pt-2 pb-2">
                                                    <input type="text"
                                                        class="form-control form-control-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'general' ? '' : 'd-none' }}"
                                                        wire:model="parameters.{{ $index }}.general_range"
                                                        placeholder="e.g. 70 - 100">

                                                    <div
                                                        class="input-group input-group-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'gender' ? 'd-flex' : 'd-none' }}">
                                                        <span
                                                            class="input-group-text px-2 text-primary bg-light">M</span>
                                                        <input type="text" class="form-control px-2"
                                                            wire:model="parameters.{{ $index }}.male_range"
                                                            placeholder="Range">
                                                        <span
                                                            class="input-group-text px-2 text-danger bg-light border-start-0">F</span>
                                                        <input type="text" class="form-control px-2"
                                                            wire:model="parameters.{{ $index }}.female_range"
                                                            placeholder="Range">
                                                    </div>

                                                    <input type="text"
                                                        class="form-control form-control-sm w-100 {{ ($parameters[$index]['range_type'] ?? 'general') === 'value' ? '' : 'd-none' }}"
                                                        wire:model="parameters.{{ $index }}.normal_value"
                                                        placeholder="e.g. Negative">

                                                    <input type="text"
                                                        class="form-control form-control-sm border-info bg-soft-info w-100 mt-1 {{ ($parameters[$index]['input_type'] ?? 'numeric') === 'calculated' ? '' : 'd-none' }}"
                                                        wire:model="parameters.{{ $index }}.formula"
                                                        placeholder="Formula: {TC} - {HDL}">
                                                </td>

                                                <td class="align-top pt-2 pb-2">
                                                    <input type="text" class="form-control form-control-sm w-100"
                                                        wire:model="parameters.{{ $index }}.unit"
                                                        placeholder="Unit">
                                                </td>

                                                <td class="text-end pe-3 align-top pt-2 pb-2">
                                                    <button type="button"
                                                        wire:click="removeParameter({{ $index }})"
                                                        class="btn btn-sm btn-icon btn-light text-danger border shadow-sm rounded">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted fs-13">No
                                                    parameters added. Click '+ Add Row' to start.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                            <button type="button" wire:click="closeModal"
                                class="btn btn-light border px-4 fw-medium shadow-sm"><i class="feather-x me-2"></i>
                                Cancel</button>
                            <button type="submit"
                                class="btn btn-success px-5 fw-bold shadow-sm d-flex align-items-center">
                                <div wire:loading.remove wire:target="store">
                                    <i class="feather-save me-2"></i>
                                    {{ $test_id ? 'Update Master Test' : 'Save Master Test' }}
                                </div>
                                <div wire:loading wire:target="store">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                    Saving...
                                </div>
                            </button>
                        </div>
                    </form>
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

        @media (max-width: 768px) {
            .modal-xl {
                max-width: 100%;
                margin: 0.5rem;
            }
        }
    </style>
</div>

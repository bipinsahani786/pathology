<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10">Global Test Library</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item">Master Setup</li>
                <li class="breadcrumb-item">Global Tests</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary w-100 w-md-auto">
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

        <div class="card stretch stretch-full">
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
                                class="form-select form-select-sm border shadow-none bg-white py-2 px-3">
                                <option value="">All Categories</option>
                                <option value="Haematology">Haematology</option>
                                <option value="Biochemistry">Biochemistry</option>
                                <option value="Serology">Serology</option>
                                <option value="Pathology">Pathology</option>
                                <option value="Microbiology">Microbiology</option>
                            </select>
                        </div>

                        <button wire:click="$set('filterCategory','')"
                            class="btn btn-sm btn-outline-light text-dark border py-2 px-3 d-flex align-items-center bg-white shadow-sm"
                            title="Reset Filters">
                            <i class="feather-refresh-ccw fs-12 me-1"></i>
                            <span>Reset</span>
                        </button>
                    </div>

                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Test Code</th>
                                <th>Test Name & Desc</th>
                                <th>Category</th>
                                <th>Parameters</th>
                                <th>Price (₹)</th>
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
                                            class="fs-12 text-muted text-truncate-1-line">{{ $test->description ?? 'No description added' }}</span>
                                    </td>
                                    <td><span class="badge bg-soft-success text-success">{{ $test->category }}</span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-soft-info text-info">{{ is_array($test->default_parameters) ? count($test->default_parameters) : 0 }}
                                            Params</span>
                                    </td>
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
                                                onclick="confirm('Are you sure you want to delete this test?') || event.stopImmediatePropagation()"
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
                                        No Tests Found. Click "Add New Test" to begin.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $tests->links() }}
            </div>
        </div>
    </div>
    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-soft-primary">
                        <h5 class="modal-title fw-bold text-primary">
                            {{ $test_id ? 'Update Global Test' : 'Add New Global Test' }}</h5>
                        <button type="button" wire:click="closeModal" class="btn-close" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                            <div class="row g-4">

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Test Code <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('test_code') is-invalid @enderror"
                                        wire:model="test_code" placeholder="e.g. CBC-01">
                                    @error('test_code')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold">Test Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="e.g. Complete Blood Count">
                                    @error('name')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Category <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                        wire:model="category">
                                        <option value="">Select Category</option>
                                        <option value="Haematology">Haematology</option>
                                        <option value="Biochemistry">Biochemistry</option>
                                        <option value="Serology">Serology</option>
                                        <option value="Pathology">Pathology</option>
                                        <option value="Microbiology">Microbiology</option>
                                    </select>
                                    @error('category')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Suggested Price (₹)</label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('suggested_price') is-invalid @enderror"
                                        wire:model="suggested_price" placeholder="0.00">
                                    @error('suggested_price')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-9">
                                    <label class="form-label fw-semibold">Test Description / Instruction</label>
                                    <input type="text"
                                        class="form-control @error('description') is-invalid @enderror"
                                        wire:model="description" placeholder="e.g. Fasting required for 10-12 hours">
                                    @error('description')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded border">
                                        <div>
                                            <h6 class="fw-bold mb-0">Test Parameters & Reference Ranges</h6>
                                            <p class="fs-12 text-muted mb-0">Select Range Type (General, Gender
                                                Specific, or Value) for each parameter.</p>
                                        </div>
                                        <button type="button" wire:click="addParameter" class="btn btn-primary">
                                            <i class="feather-plus me-1"></i> Add Parameter
                                        </button>
                                    </div>

                                    <div class="table-responsive border rounded">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="25%">Parameter Name <span
                                                            class="text-danger">*</span></th>
                                                    <th width="15%">Unit</th>
                                                    <th width="20%">Range Type</th>
                                                    <th width="30%">Normal / Reference Range</th>
                                                    <th width="10%" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($parameters as $index => $param)
                                                    <tr>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="parameters.{{ $index }}.name"
                                                                placeholder="e.g. Blood Sugar">
                                                            @error('parameters.' . $index . '.name')
                                                                <span class="text-danger fs-11">{{ $message }}</span>
                                                            @enderror
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="parameters.{{ $index }}.unit"
                                                                placeholder="e.g. mg/dL">
                                                        </td>

                                                        <td>
                                                            <select class="form-select form-select-sm"
                                                                wire:model.live="parameters.{{ $index }}.range_type">
                                                                <option value="general">General (Unisex)</option>
                                                                <option value="gender">Gender Specific (M/F)</option>
                                                                <option value="value">Qualitative (Text)</option>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            @if ($param['range_type'] === 'general')
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    wire:model="parameters.{{ $index }}.general_range"
                                                                    placeholder="e.g. 70 - 100">
                                                            @elseif($param['range_type'] === 'gender')
                                                                <div class="d-flex gap-2">
                                                                    <div class="input-group input-group-sm">
                                                                        <span
                                                                            class="input-group-text bg-light text-primary fw-bold">M</span>
                                                                        <input type="text" class="form-control"
                                                                            wire:model="parameters.{{ $index }}.male_range"
                                                                            placeholder="13 - 17">
                                                                    </div>
                                                                    <div class="input-group input-group-sm">
                                                                        <span
                                                                            class="input-group-text bg-light text-danger fw-bold">F</span>
                                                                        <input type="text" class="form-control"
                                                                            wire:model="parameters.{{ $index }}.female_range"
                                                                            placeholder="12 - 15">
                                                                    </div>
                                                                </div>
                                                            @elseif($param['range_type'] === 'value')
                                                                <input type="text"
                                                                    class="form-control form-control-sm"
                                                                    wire:model="parameters.{{ $index }}.normal_value"
                                                                    placeholder="e.g. Negative / Non-Reactive">
                                                            @endif
                                                        </td>

                                                        <td class="text-center">
                                                            <button type="button"
                                                                wire:click="removeParameter({{ $index }})"
                                                                class="btn btn-sm btn-icon btn-light-danger"
                                                                data-bs-toggle="tooltip" title="Remove">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center text-muted py-4 fs-13">
                                                            No parameters added. Click "Add Parameter" button above.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top-0 d-flex justify-content-between">
                            <button type="button" wire:click="closeModal" class="btn btn-light-danger"><i
                                    class="feather-x me-2"></i> Cancel</button>
                            <button type="submit" class="btn btn-success px-4">
                                <span wire:loading.remove wire:target="store">
                                    <i class="feather-save me-2"></i> {{ $test_id ? 'Update Test' : 'Save Test' }}
                                </span>
                                <span wire:loading wire:target="store">Saving Database...</span>
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
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-color: #e9ecef;
        }

        .input-group:focus-within {
            border-color: #3b71ca !important;
            /* Blue border on focus */
            box-shadow: 0 0 0 0.2rem rgba(59, 113, 202, 0.1) !important;
        }

        @media (max-width: 576px) {
            .page-header {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .page-header-right {
                width: 100%;
            }

            .page-header-right .btn {
                width: 100% !important;
            }

            .modal-xl {
                max-width: 95vw;
            }

            .table-responsive {
                font-size: 0.875rem;
            }

            .hstack.gap-2 {
                gap: 0.5rem !important;
            }

            .avatar-text {
                padding: 0.5rem !important;
            }
        }

        @media (max-width: 768px) {
            .page-header-title h5 {
                font-size: 1.25rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }

            .modal-body {
                padding: 1rem !important;
            }
        }
    </style>
    <style>
      
        .table thead th {
            background-color: #f8f9fa;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #6c757d;
            border-top: none !important;
        }

        .input-group:focus-within {
            border-color: #4e73df !important;
            background-color: #fff !important;
        }
    </style>
</div>

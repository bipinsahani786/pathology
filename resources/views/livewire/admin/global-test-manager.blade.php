<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Global Test Library</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Test Library</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button type="button" class="btn btn-soft-info px-3 me-2" data-bs-toggle="modal" data-bs-target="#testDocumentationModal">
                <i class="feather-book-open me-2"></i>Documentation
            </button>
            <a href="{{ route('admin.global-tests.create') }}" wire:navigate class="btn btn-primary px-4">
                <i class="feather-plus me-2"></i>Add New Test
            </a>
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4">
                <i class="feather-check-circle fs-4 text-success me-3"></i>
                <div class="fw-bold">{{ session('message') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group search-group shadow-sm" style="height: 48px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control border-start-0 ps-0 shadow-none" 
                                placeholder="Search by name, code or keyword...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group shadow-sm" style="height: 48px;">
                            <span class="input-group-text bg-white border-end-0">
                                <i class="feather-filter text-primary"></i>
                            </span>
                            <select wire:model.live="filterDepartment" class="form-select border-start-0 ps-0 shadow-none" style="cursor: pointer;">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button wire:click="$set('filterDepartment',''); $set('searchTerm','')" 
                            class="btn btn-soft-danger w-100 fw-bold d-flex align-items-center justify-content-center" 
                            style="height: 48px;">
                            <i class="feather-refresh-ccw me-2 fs-12"></i>RESET FILTERS
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                            <tr>
                                <th class="ps-4" style="width: 120px;">Code</th>
                                <th>Test Information</th>
                                <th>Department</th>
                                <th>Parameters</th>
                                <th style="width: 150px;">Sugg. Price</th>
                                <th class="text-end pe-4" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tests as $test)
                                <tr class="border-bottom border-light" wire:key="test-row-{{ $test->id }}">
                                    <td class="ps-4">
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">{{ $test->test_code }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14 mb-0">{{ $test->name }}</div>
                                        <div class="text-muted fs-11 text-truncate-1-line" title="{{ $test->description }}">{{ $test->description ?: 'No description added' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info px-3 fw-medium">{{ $test->dept?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar-text avatar-xs bg-soft-success text-success rounded-circle me-3 fw-bold text-center d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">{{ is_array($test->default_parameters) ? count($test->default_parameters) : 0 }}</span>
                                            <span class="fs-12 fw-medium text-dark">Parameters</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">₹{{ number_format($test->suggested_price ?? 0, 2) }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('admin.global-tests.edit', $test->id) }}" wire:navigate class="btn btn-icon btn-soft-info btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <button wire:click="delete({{ $test->id }})" 
                                                wire:confirm="Permanent Delete this global test? Warning: This might break lab sync." 
                                                class="btn btn-icon btn-soft-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="p-5 text-center">
                                            <i class="feather-inbox fs-1 text-muted opacity-25 d-block mb-3"></i>
                                            <h6 class="text-muted fw-bold">No master tests found in the library.</h6>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $tests->links() }}
            </div>
        </div>
    </div>

    <style>
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-14 { font-size: 14px; }
    </style>

    @include('components.test-documentation-modal')
</div>

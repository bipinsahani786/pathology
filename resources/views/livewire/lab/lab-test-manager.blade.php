<div>
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Test Catalog</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Catalog</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="openImportModal" class="btn btn-soft-primary px-4 me-2">
                <i class="feather-download me-2"></i>Import Global
            </button>
            <a href="{{ route('lab.tests.create') }}" wire:navigate class="btn btn-primary px-4">
                <i class="feather-plus me-2"></i>Add Custom Test
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
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search tests name, code or keyword...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select wire:model.live="filterCategory" class="form-select shadow-sm">
                            <option value="">All Categories</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button wire:click="$set('filterCategory',''); $set('searchTerm','')" 
                            class="btn btn-soft-danger w-100 fw-bold d-flex align-items-center justify-content-center">
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
                                <th>Category</th>
                                <th style="width: 150px;">Pricing (₹)</th>
                                <th style="width: 120px;">Status</th>
                                <th class="text-end pe-4" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tests as $test)
                                <tr class="border-bottom border-light" wire:key="test-row-{{ $test->id }}">
                                    <td class="ps-4">
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">{{ $test->test_code ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14 mb-0">{{ $test->name }}</div>
                                        <div class="text-muted fs-11 text-truncate-1-line" title="{{ $test->description }}">{{ $test->description ?: 'No internal description' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info px-3 fw-medium">{{ $test->dept?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">₹{{ number_format($test->mrp ?? 0, 2) }}</div>
                                        <div class="text-muted fs-11">B2B: ₹{{ number_format($test->b2b_price ?? 0, 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input shadow-none" type="checkbox" 
                                                wire:click="toggleStatus({{ $test->id }})" 
                                                {{ $test->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('lab.tests.edit', $test->id) }}" wire:navigate class="btn btn-icon btn-soft-info btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <button wire:click="delete({{ $test->id }})" 
                                                wire:confirm="Are you sure you want to delete this test?" 
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
                                            <h6 class="text-muted fw-bold">No tests found in your catalog.</h6>
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

    <!-- Import Global Tests Modal -->
    @if ($isImportModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-download-cloud text-primary me-2"></i>Import from Global Master
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="input-group mb-4 shadow-sm border rounded-pill overflow-hidden">
                            <span class="input-group-text bg-white border-0 ps-3">
                                <i class="feather-search text-muted"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="globalSearch" 
                                class="form-control border-0 py-2 shadow-none" 
                                placeholder="Search by test name or code in global library...">
                        </div>

                        <div class="row g-3">
                            @foreach($globalTests as $gt)
                                <div class="col-md-6" wire:key="global-{{ $gt->id }}">
                                    <div class="card border rounded-3 p-3 shadow-none h-100 hover-border-primary transition-all">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-soft-primary text-primary fs-10 px-2">{{ $gt->test_code }}</span>
                                            <button wire:click="importGlobalTest({{ $gt->id }})" class="btn btn-sm btn-primary rounded-pill px-3 fs-11">
                                                Import
                                            </button>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $gt->name }}</h6>
                                        <p class="text-muted fs-11 mb-2">{{ $gt->dept?->name ?? 'N/A' }}</p>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="fs-11 text-muted"><i class="feather-list me-1"></i>{{ is_array($gt->default_parameters) ? count($gt->default_parameters) : 0 }} Params</span>
                                            <span class="fs-11 text-muted"><i class="feather-tag me-1"></i>₹{{ number_format($gt->suggested_price ?? 0, 2) }} (Sugg.)</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if(count($globalTests) >= $globalLimit)
                            <div class="text-center mt-4">
                                <button wire:click="loadMoreGlobalTests" class="btn btn-light border rounded-pill px-4 fs-12 fw-bold">
                                    Load More Tests
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .transition-all { transition: all 0.2s ease; }
        .hover-border-primary:hover { border-color: var(--bs-primary) !important; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; }
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-14 { font-size: 14px; }
    </style>
</div>
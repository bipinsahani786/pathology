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
            <button type="button" class="btn btn-soft-info px-4 me-2" data-bs-toggle="modal" data-bs-target="#testDocumentationModal">
                <i class="feather-book-open me-2"></i>Documentation
            </button>
            @can('create lab_tests')
                <button wire:click="openImportModal" class="btn btn-soft-primary px-4 me-2">
                    <i class="feather-download me-2"></i>Import Global
                </button>
                <a href="{{ route('lab.tests.create') }}" wire:navigate class="btn btn-primary px-4">
                    <i class="feather-plus me-2"></i>Add Custom Test
                </a>
            @endcan
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
                                <th style="width: 140px;">Parameters</th>
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
                                        <div class="d-flex align-items-center">
                                            <span class="avatar-text bg-soft-success text-success rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px;">{{ is_array($test->parameters) ? count($test->parameters) : 0 }}</span>
                                            <span class="fs-12 fw-bold text-dark">Params</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="price-input-wrapper mb-1">
                                            <div class="d-flex align-items-center pricing-row mrp-row px-2 py-1 rounded-3">
                                                <span class="currency-symbol text-primary fw-bold me-1">₹</span>
                                                <input type="number" step="0.01" 
                                                    value="{{ $test->mrp }}" 
                                                    wire:blur="updateMrp({{ $test->id }}, $event.target.value)"
                                                    class="inline-price-input fw-bold text-dark fs-14 border-0 bg-transparent w-100 shadow-none" 
                                                    title="Edit MRP">
                                            </div>
                                        </div>
                                        <div class="price-input-wrapper">
                                            <div class="d-flex align-items-center pricing-row b2b-row px-2 py-1 rounded-3 bg-soft-light">
                                                <span class="label-text text-muted fw-medium me-1" style="font-size: 10px; width: 25px;">B2B</span>
                                                <input type="number" step="0.01" 
                                                    value="{{ $test->b2b_price }}" 
                                                    wire:blur="updateB2BPrice({{ $test->id }}, $event.target.value)"
                                                    class="inline-price-input text-muted fs-11 border-0 bg-transparent w-100 shadow-none" 
                                                    title="Edit B2B Price">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input shadow-none" type="checkbox" 
                                                wire:click="toggleStatus({{ $test->id }})" 
                                                {{ $test->is_active ? 'checked' : '' }}
                                                @cannot('edit lab_tests') disabled @endcannot>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            @can('edit lab_tests')
                                                <a href="{{ route('lab.tests.edit', $test->id) }}" wire:navigate class="btn btn-icon btn-soft-info btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="feather-edit-3"></i>
                                                </a>
                                            @endcan
                                            @can('delete lab_tests')
                                                <button wire:click="delete({{ $test->id }})" 
                                                    wire:confirm="Are you sure you want to delete this test?" 
                                                    class="btn btn-icon btn-soft-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="feather-trash-2"></i>
                                                </button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
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
                    <div class="modal-header bg-light border-bottom p-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold text-dark mb-0">
                            <i class="feather-download-cloud text-primary me-2"></i>Import from Global Master
                        </h5>
                        <div class="d-flex align-items-center gap-2">
                            @if(count($selectedGlobalTests) > 0)
                                <button wire:click="bulkImport" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm rounded-pill animate__animated animate__pulse animate__infinite">
                                    <i class="feather-download me-1"></i> Bulk Import ({{ count($selectedGlobalTests) }})
                                </button>
                                <button wire:click="resetGlobalSelection" class="btn btn-soft-danger btn-sm px-3 fw-bold rounded-pill">
                                    Clear
                                </button>
                            @endif
                            <button type="button" wire:click="closeModal" class="btn-close shadow-none ms-2" aria-label="Close"></button>
                        </div>
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
                                @php 
                                    $isImported = in_array($gt->id, $importedGlobalTestIds); 
                                    $isSelected = in_array($gt->id, $selectedGlobalTests);
                                @endphp
                                <div class="col-md-6" wire:key="global-{{ $gt->id }}">
                                    <div class="card border rounded-3 p-3 shadow-none h-100 hover-border-primary transition-all {{ $isImported ? 'bg-light border-success-subtle' : '' }} {{ $isSelected ? 'border-primary bg-soft-primary' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                @if(!$isImported)
                                                    <div class="form-check m-0">
                                                        <input class="form-check-input shadow-none" type="checkbox" 
                                                            wire:model.live="selectedGlobalTests" value="{{ $gt->id }}">
                                                    </div>
                                                @endif
                                                <span class="badge bg-soft-primary text-primary fs-10 px-2">{{ $gt->test_code }}</span>
                                            </div>
                                            
                                            @if($isImported)
                                                <div class="text-end">
                                                    <span class="badge bg-success text-white fs-10 px-3 py-1 rounded-pill mb-1 d-block">
                                                        <i class="feather-check me-1"></i>Imported
                                                    </span>
                                                    <a href="#" wire:click.prevent="importGlobalTest({{ $gt->id }})" 
                                                        wire:confirm="This test is already in your catalog. Do you want to import it again?" 
                                                        class="text-primary fs-10 text-decoration-underline">Import again?</a>
                                                </div>
                                            @else
                                                <button wire:click="importGlobalTest({{ $gt->id }})" class="btn btn-sm btn-primary rounded-pill px-3 fs-11">
                                                    Import
                                                </button>
                                            @endif
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $gt->name }}</h6>
                                        <p class="text-muted fs-11 mb-2">{{ $gt->dept?->name ?? 'N/A' }}</p>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar-text bg-soft-success text-success rounded-circle me-2 fw-bold d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 11px;">{{ is_array($gt->default_parameters) ? count($gt->default_parameters) : 0 }}</span>
                                                <span class="fs-11 fw-bold text-dark">Params</span>
                                            </div>
                                            <span class="fs-11 text-muted"><i class="feather-tag me-1 text-primary"></i>₹{{ number_format($gt->mrp ?? 0, 2) }}</span>
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

        /* Inline Pricing Styles */
        .pricing-row {
            border: 1px solid transparent;
            transition: all 0.2s ease;
            max-width: 120px;
        }
        .pricing-row:hover {
            background-color: #f8f9fa !important;
            border-color: #e9ecef;
        }
        .pricing-row:focus-within {
            background-color: #fff !important;
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(59, 113, 202, 0.1);
        }
        .inline-price-input {
            outline: none;
            padding: 0;
            cursor: pointer;
        }
        .inline-price-input::-webkit-outer-spin-button,
        .inline-price-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .inline-price-input[type=number] {
            -moz-appearance: textfield;
        }
        .currency-symbol { font-size: 12px; }
        .bg-soft-light { background-color: rgba(0,0,0,0.02); }
    </style>

    @include('components.test-documentation-modal')
</div>
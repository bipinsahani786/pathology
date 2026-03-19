<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 text-dark fw-bold">Test Packages & Profiles</h5>
                {{-- <p class="fs-13 text-muted mb-0">Group multiple single tests into comprehensive health packages.</p> --}}
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0 ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item active">Packages</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <button wire:click="create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center transition-all hover-lift">
                <i class="feather-plus me-1"></i> Create New Package
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-alert-triangle fs-4 me-2"></i>
                <strong>{{ session('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3">
                    <div class="col-md-9">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search packages by name or code...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button wire:click="$set('searchTerm','')"
                            class="btn btn-light border bg-white shadow-sm h-100 w-100 d-flex align-items-center justify-content-center transition-all hover-lift"
                            title="Reset Filters">
                            <i class="feather-refresh-ccw fs-12 me-2 text-primary"></i>
                            <span class="fw-bold text-dark fs-12">RESET</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Code</th>
                                <th class="py-3">Package Name</th>
                                <th class="py-3">Tests Included</th>
                                <th class="py-3">Pricing (₹)</th>
                                <th class="py-3">Status</th>
                                <th class="text-center py-3" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packages as $pkg)
                                <tr wire:key="pkg-{{ $pkg->id }}" class="border-bottom border-light">
                                    <td class="ps-4">
                                        <span class="badge bg-soft-primary text-primary px-2 py-1">{{ $pkg->test_code ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">{{ $pkg->name }}</div>
                                        <small class="text-muted fw-medium">{{ $pkg->department }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info border border-info px-2 py-1">
                                            {{ is_array($pkg->linked_test_ids) ? count($pkg->linked_test_ids) : 0 }} Tests
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary fs-14">₹{{ number_format($pkg->mrp, 2) }}</div>
                                        <div class="fs-11 text-muted">B2B: ₹{{ number_format($pkg->b2b_price, 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleStatus({{ $pkg->id }})" {{ $pkg->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <span class="fs-12 ms-2 fw-medium {{ $pkg->is_active ? 'text-success' : 'text-danger' }}">
                                                {{ $pkg->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="edit({{ $pkg->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-2 fs-14"></i>
                                            </button>
                                            <button wire:click="delete({{ $pkg->id }})" wire:confirm="Are you sure you want to delete this package?" class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2 fs-14"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-layers" style="font-size: 3.5rem; opacity: 0.5;"></i></div>
                                        <h6 class="fw-bold text-dark">No Packages Found</h6>
                                        <p class="text-muted fs-13">Create profiles like 'Full Body Checkup' or 'Fever Panel'.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3">
                {{ $packages->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable mb-5">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <i class="feather-{{ $package_id ? 'edit' : 'layers' }} text-primary me-2"></i>
                            {{ $package_id ? 'Edit Package Configuration' : 'Create New Test Package' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <form wire:submit.prevent="store" class="d-flex flex-column" style="overflow: hidden;">
                        <div class="modal-body p-0 bg-white" style="overflow-y: auto;">
                            
                            @if (session()->has('error'))
                                <div class="alert alert-danger border-0 rounded-0 mb-0 py-2 fs-12 px-4 shadow-sm">
                                    <i class="feather-alert-octagon me-2"></i>{{ session('error') }}
                                </div>
                            @endif

                            <div class="p-4">
                                <div class="bg-light p-3 p-md-4 rounded-4 border mb-4 shadow-sm">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-3">
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1 small-caps">Step 1: Build Your Profile <span class="text-danger">*</span></h6>
                                            <p class="fs-11 text-muted mb-0">Search and aggregate single tests into this package.</p>
                                        </div>
                                        @if(count($selectedTests) > 0)
                                            <div class="bg-white border-primary border px-3 py-1 rounded-pill shadow-sm">
                                                <span class="fs-10 text-muted text-uppercase fw-bold me-1">Original Sum:</span>
                                                <span class="fs-14 fw-bold text-primary">₹{{ number_format(array_sum(array_column($selectedTests, 'mrp')), 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @error('selectedTests') 
                                        <div class="alert alert-danger py-2 fs-11 mb-3 border-0 shadow-sm"><i class="feather-alert-octagon me-1"></i>{{ $message }}</div> 
                                    @enderror

                                    <div class="position-relative mb-3">
                                        <div class="input-group border border-primary rounded-pill bg-white shadow-sm px-3 py-1 overflow-hidden" style="border-width: 2px !important;">
                                            <span class="input-group-text bg-transparent border-0 pe-2 text-primary">
                                                <div wire:loading.remove wire:target="testSearchTerm"><i class="feather-search fs-5"></i></div>
                                                <div wire:loading wire:target="testSearchTerm"><span class="spinner-border spinner-border-sm" role="status"></span></div>
                                            </span>
                                            <input type="text" wire:model.live.debounce.300ms="testSearchTerm" class="form-control border-0 bg-transparent shadow-none fw-medium text-dark fs-14" placeholder="Type test name (e.g. CBC, Liver)...">
                                        </div>

                                        @if(strlen($testSearchTerm) > 1)
                                            <div class="position-absolute w-100 mt-2 bg-white border border-light rounded-4 shadow-xl overflow-hidden z-3" style="max-height: 250px; overflow-y: auto;">
                                                <div class="list-group list-group-flush">
                                                    @forelse($searchResultTests as $srt)
                                                        @if(!isset($selectedTests[$srt->id]))
                                                            <button type="button" wire:click="addTestToPackage({{ $srt->id }}, '{{ addslashes($srt->name) }}', '{{ addslashes($srt->department) }}', {{ $srt->mrp ?? 0 }})" 
                                                                class="list-group-item list-group-item-action py-3 px-4 border-bottom d-flex justify-content-between align-items-center hover-bg-light transition-all">
                                                                <div class="text-start">
                                                                    <span class="fw-bold text-dark d-block fs-13">{{ $srt->name }}</span>
                                                                    <span class="fs-10 text-muted text-uppercase fw-bold opacity-75">{{ $srt->department }}</span>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="badge bg-soft-primary text-primary fs-11 rounded-pill px-2">₹{{ number_format($srt->mrp, 0) }}</span>
                                                                    <i class="feather-plus-circle text-primary fs-5 mt-1"></i>
                                                                </div>
                                                            </button>
                                                        @endif
                                                    @empty
                                                        <div class="p-4 text-center text-muted fs-12 bg-soft-light">
                                                            <i class="feather-info fs-5 d-block mb-1 text-warning"></i>
                                                            No matches found in catalog.
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="selected-tests-container" style="max-height: 300px; overflow-y: auto;">
                                        @if(count($selectedTests) > 0)
                                            <div class="list-group border-0 rounded-3 overflow-hidden shadow-sm">
                                                @foreach($selectedTests as $testId => $sTest)
                                                    <div wire:key="sel-test-{{ $testId }}" class="list-group-item d-flex justify-content-between align-items-center py-2 px-3 border-bottom border-light bg-white hover-bg-light transition-all">
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 24px; height: 24px;">
                                                                <i class="feather-check fs-10 fw-bold"></i>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark fs-13">{{ $sTest['name'] }}</div>
                                                                <div class="fs-10 text-muted text-uppercase fw-bold opacity-75">{{ $sTest['department'] }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <span class="fw-bold text-dark fs-13 text-nowrap">₹{{ number_format($sTest['mrp'], 2) }}</span>
                                                            <button type="button" wire:click="removeTestFromPackage({{ $testId }})" class="btn btn-sm btn-icon btn-outline-danger border-0 rounded-circle shadow-none p-1" title="Remove">
                                                                <i class="feather-x fs-14"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="p-4 text-center border border-dashed rounded-4 bg-white bg-opacity-50">
                                                <i class="feather-package text-muted opacity-25 mb-2 d-block" style="font-size: 2.5rem;"></i>
                                                <h6 class="fw-bold text-dark opacity-75 mb-1 fs-13">Profile is currently empty</h6>
                                                <p class="text-muted fs-11 mb-0">Search above to add tests to this panel.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark mb-3 px-1 small-caps">Step 2: Profile Metadata & Pricing</h6>
                                <div class="row g-3 px-1 mb-2">
                                    <div class="col-12 col-md-5">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Package Name *</label>
                                        <input type="text" class="form-control" wire:model="name" placeholder="E.g. Comprehensive Lipid Panel">
                                        @error('name') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Internal Code</label>
                                        <input type="text" class="form-control" wire:model="test_code" placeholder="E.g. PR-001">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Classification</label>
                                        <input type="text" class="form-control bg-light text-muted fw-medium" wire:model="department" readonly>
                                    </div>

                                    <div class="col-6 col-md-4">
                                        <label class="form-label fs-11 fw-bold text-primary text-uppercase mb-1">Selling Price (MRP) *</label>
                                        <input type="number" step="0.01" class="form-control border-primary bg-soft-primary fw-bold text-primary fs-15 shadow-sm" wire:model="mrp" placeholder="0.00">
                                        @error('mrp') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">B2B Rate (₹)</label>
                                        <input type="number" step="0.01" class="form-control" wire:model="b2b_price" placeholder="Franchise cost">
                                    </div>
                                    <div class="col-6 col-md-4">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">TAT (Hours)</label>
                                        <input type="number" class="form-control" wire:model="tat_hours" placeholder="e.g. 12-24">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Clinical Notes / Preparation Instructions</label>
                                        <textarea class="form-control fs-12" wire:model="description" rows="2" placeholder="E.g. Requires 12 hours of fasting. Avoid fatty meals before the test..."></textarea>
                                    </div>
                                </div>
                                {{-- Vertical Spacer for better scrolling experience --}}
                                <div class="py-4"></div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2 px-4 shadow-sm">
                            <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm transition-all hover-lift" style="height: 42px;">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center transition-all hover-lift" style="height: 42px;">
                                <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Profile</div>
                                <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Updating...</div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Form Overrides */
        .form-select-sm, .form-control-sm { padding: 0.4rem 0.5rem; border-color: #e2e8f0; }
        
        /* Soft Backgrounds */
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-info { background-color: rgba(23, 162, 184, 0.08) !important; }
        .bg-soft-success { background-color: rgba(25, 135, 84, 0.12) !important; }
        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        
        /* Smooth Transitions */
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-lift:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        /* Focus Outline */
        .focus-ring-wrapper:focus-within { 
            border-color: #3b71ca !important; 
            box-shadow: 0 0 0 0.25rem rgba(59, 113, 202, 0.15) !important; 
        }
        
        /* Hover Effects */
        .hover-bg-light:hover { background-color: #f8fafc !important; }
        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        .hover-danger:hover { background-color: #dc3545 !important; color: #fff !important; border-color: #dc3545 !important; }

        /* Icon Button Alignment */
        .align-center-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px; height: 32px; padding: 0 !important;
        }
        
        /* Dashed Border for Empty States */
        .border-dashed { border-style: dashed !important; border-color: #cbd5e1 !important; border-width: 2px !important; }

        @media (max-width: 768px) { .modal-xl { max-width: 100%; margin: 0.5rem; } }
    </style>
</div>
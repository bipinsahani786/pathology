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
                    <div class="col-md-10">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search packages by name or code...">
                        </div>
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
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="feather-{{ $package_id ? 'edit' : 'layers' }} fs-5"></i>
                            </div>
                            {{ $package_id ? 'Edit Package Configuration' : 'Create New Test Package' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white">
                        
                        <div class="bg-light p-4 rounded-4 border mb-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Step 1: Build Your Package <span class="text-danger">*</span></h6>
                                    <p class="fs-12 text-muted mb-0">Search and add single tests to automatically calculate the total original value.</p>
                                </div>
                                @if(count($selectedTests) > 0)
                                    <div class="bg-white border px-4 py-2 rounded-pill shadow-sm text-end border-primary">
                                        <span class="fs-11 text-muted text-uppercase fw-bold me-2">Original Total:</span>
                                        <span class="fs-15 fw-bold text-primary">₹{{ number_format(array_sum(array_column($selectedTests, 'mrp')), 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            @error('selectedTests') 
                                <div class="alert alert-danger py-2 fs-12 mb-3 border-0 shadow-sm"><i class="feather-alert-circle me-1"></i>{{ $message }}</div> 
                            @enderror

                            <div class="position-relative mb-4">
                                <div class="input-group border border-primary rounded-pill bg-white shadow-sm px-3 py-2 focus-ring-wrapper" style="border-width: 2px !important;">
                                    <span class="input-group-text bg-transparent border-0 pe-2 text-primary">
                                        <div wire:loading.remove wire:target="testSearchTerm"><i class="feather-search fs-5"></i></div>
                                        <div wire:loading wire:target="testSearchTerm"><span class="spinner-border spinner-border-sm" role="status"></span></div>
                                    </span>
                                    <input type="text" wire:model.live.debounce.300ms="testSearchTerm" class="form-control border-0 bg-transparent shadow-none fw-medium text-dark fs-14" placeholder="Search test by name (e.g. CBC, Lipid Profile)...">
                                </div>

                                @if(strlen($testSearchTerm) > 1)
                                    <div class="position-absolute w-100 mt-2 bg-white border border-light rounded-3 shadow-lg overflow-hidden" style="z-index: 1055; max-height: 280px; overflow-y: auto;">
                                        <div class="list-group list-group-flush">
                                            @forelse($searchResultTests as $srt)
                                                <button type="button" wire:click="addTestToPackage({{ $srt->id }}, '{{ addslashes($srt->name) }}', '{{ addslashes($srt->department) }}', {{ $srt->mrp ?? 0 }})" 
                                                    class="list-group-item list-group-item-action py-3 px-4 border-bottom d-flex justify-content-between align-items-center hover-bg-light transition-all">
                                                    <div>
                                                        <span class="fw-bold text-dark d-block fs-14">{{ $srt->name }}</span>
                                                        <span class="fs-11 text-muted text-uppercase"><i class="feather-tag me-1"></i>{{ $srt->department }} | Code: {{ $srt->test_code ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="badge bg-soft-primary text-primary fs-12 px-2 py-1 border border-primary border-opacity-25">₹{{ number_format($srt->mrp, 0) }}</span>
                                                        <i class="feather-plus-circle text-primary fs-4"></i>
                                                    </div>
                                                </button>
                                            @empty
                                                <div class="p-4 text-center text-muted fs-13 bg-soft-warning">
                                                    <i class="feather-info fs-4 d-block mb-2 text-warning"></i>
                                                    No single test found. Ensure it exists in the Test Catalog first.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>

                          @if(count($selectedTests) > 0)
                                <div class="list-group shadow-sm border-0 rounded-4 overflow-hidden">
                                    @foreach($selectedTests as $testId => $sTest)
                                        <div wire:key="sel-test-{{ $testId }}" class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 border-bottom border-light bg-white">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 28px; height: 28px;">
                                                    <i class="feather-check fs-11 fw-bold"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark fs-14">{{ $sTest['name'] }}</div>
                                                    <div class="fs-11 text-muted text-uppercase">{{ $sTest['department'] }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-4">
                                                <span class="fw-bold text-dark fs-14">₹{{ number_format($sTest['mrp'], 2) }}</span>
                                                <button type="button" wire:click="removeTestFromPackage({{ $testId }})" class="btn btn-sm btn-icon btn-light text-danger rounded-circle shadow-sm border transition-all hover-danger p-2" title="Remove Test">
                                                    <i class="feather-x fs-14"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-5 text-center border border-dashed rounded-4 bg-white mt-3">
                                    <i class="feather-package text-muted opacity-50 mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold text-dark">Package is empty</h6>
                                    <p class="text-muted fs-13 mb-0">Use the search bar above to start adding tests to this profile.</p>
                                </div>
                            @endif
                        </div>

                        <h6 class="fw-bold text-dark mb-3 px-2">Step 2: Package Details & Final Pricing</h6>
                        <div class="row g-3 px-2 mb-2">
                            
                            <div class="col-md-5">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Package Name *</label>
                                <input type="text" class="form-control" wire:model="name" placeholder="E.g. Full Body Checkup">
                                @error('name') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Package Code</label>
                                <input type="text" class="form-control" wire:model="test_code" placeholder="E.g. FBC-01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Department</label>
                                <input type="text" class="form-control bg-light text-muted" wire:model="department" readonly>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-primary text-uppercase">Final Package MRP (₹) *</label>
                                <input type="number" step="0.01" class="form-control border-primary bg-soft-primary fw-bold text-primary fs-15" wire:model="mrp" placeholder="Enter Discounted Price">
                                <div class="form-text fs-11 text-muted">Set the final selling price for patients.</div>
                                @error('mrp') <span class="text-danger fs-11">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">B2B Price (₹)</label>
                                <input type="number" step="0.01" class="form-control" wire:model="b2b_price" placeholder="Franchise Price">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">TAT (Hours)</label>
                                <input type="number" class="form-control" wire:model="tat_hours" placeholder="e.g. 24">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Package Description / Special Instructions</label>
                                <textarea class="form-control" wire:model="description" rows="2" placeholder="E.g. Fasting required for 10-12 hours..."></textarea>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer bg-light border-top p-4 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center transition-all hover-lift">
                            <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Package Profile</div>
                            <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Processing...</div>
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
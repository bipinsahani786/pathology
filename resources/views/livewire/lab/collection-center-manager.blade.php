<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3 mb-4">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 text-dark fw-bold">Collection Centers</h5>
                <p class="fs-13 text-muted mb-0">Manage your sample pickup points, clinics, and franchises.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0 ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item active">Centers</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <button wire:click="create" class="btn btn-primary btn-sm shadow-sm d-flex align-items-center transition-all hover-lift">
                <i class="feather-plus me-1"></i> Add Collection Center
            </button>
        </div>
    </div>

    <div class="main-content">
        
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            
            <div class="card-header bg-white py-4 border-bottom border-light">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-8 col-lg-6" style="max-width: 600px;">
                        <div class="position-relative">
                            <span class="position-absolute top-50 translate-middle-y text-muted" style="left: 18px; z-index: 10;">
                                <div wire:loading.remove wire:target="searchTerm"><i class="feather-search fs-5"></i></div>
                                <div wire:loading wire:target="searchTerm"><span class="spinner-border spinner-border-sm text-primary" role="status"></span></div>
                            </span>
                            
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control rounded-pill border-light shadow-sm" 
                                placeholder="Search by center name or code..."
                                style="padding-left: 48px; height: 45px; font-size: 14px; background-color: #f8fafc; transition: all 0.2s;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Center Details</th>
                                <th class="py-3">Address</th>
                                <th class="py-3">Status</th>
                                <th class="text-center pe-4 py-3" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($centers as $center)
                                <tr wire:key="center-{{ $center->id }}" class="border-bottom border-light">
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="feather-map-pin fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14">{{ $center->name }}</div>
                                                <div class="fs-12 text-muted">Code: <span class="fw-medium text-dark">{{ $center->center_code ?? 'N/A' }}</span></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fs-13 text-muted">{{ Str::limit($center->address, 50) ?? 'No address provided' }}</span>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch" wire:click="toggleStatus({{ $center->id }})" {{ $center->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <span class="fs-12 ms-2 fw-medium {{ $center->is_active ? 'text-success' : 'text-danger' }}">
                                                {{ $center->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="edit({{ $center->id }})" class="btn btn-sm btn-light border text-primary shadow-sm rounded align-center-btn transition-all hover-primary" title="Edit">
                                                <i class="feather-edit-2 fs-14"></i>
                                            </button>
                                            <button wire:click="delete({{ $center->id }})" wire:confirm="Delete this collection center?" class="btn btn-sm btn-light border text-danger shadow-sm rounded align-center-btn transition-all hover-danger" title="Delete">
                                                <i class="feather-trash-2 fs-14"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-map" style="font-size: 3rem; opacity: 0.5;"></i></div>
                                        <h6 class="fw-bold text-dark">No Collection Centers Found</h6>
                                        <p class="text-muted fs-13">Add your pickup points, clinics, or franchise locations.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3">
                {{ $centers->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">

                    <div class="modal-header bg-light border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                <i class="feather-map-pin fs-5"></i>
                            </div>
                            {{ $center_id ? 'Edit Collection Center' : 'New Collection Center' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none"></button>
                    </div>

                    <div class="modal-body p-4 bg-white">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Center Name *</label>
                                <input type="text" class="form-control fw-medium text-dark" wire:model="name" placeholder="e.g., Kankarbagh Pickup Point">
                                @error('name') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-md-5">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Center Code</label>
                                <input type="text" class="form-control" wire:model="center_code" placeholder="e.g., KB-01">
                                @error('center_code') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Full Address</label>
                                <textarea class="form-control" wire:model="address" rows="2" placeholder="Enter complete center address..."></textarea>
                                @error('address') <span class="text-danger fs-11 fw-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                        <button type="button" wire:click="store" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center transition-all hover-lift">
                            <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> Save Center</div>
                            <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...</div>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        
        .transition-all { transition: all 0.2s ease-in-out; }
        .hover-lift:hover { transform: translateY(-1px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        
        input.form-control:focus, textarea.form-control:focus {
            background-color: #ffffff !important;
            border-color: #3b71ca !important;
            box-shadow: 0 4px 15px rgba(59, 113, 202, 0.08), 0 0 0 0.25rem rgba(59, 113, 202, 0.15) !important;
        }

        .hover-primary:hover { background-color: #3b71ca !important; color: #fff !important; border-color: #3b71ca !important; }
        .hover-danger:hover { background-color: #dc3545 !important; color: #fff !important; border-color: #dc3545 !important; }

        .align-center-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px; height: 32px; padding: 0 !important;
        }
    </style>
</div>
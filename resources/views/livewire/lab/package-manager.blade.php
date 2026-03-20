<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10 text-dark fw-bold">Test Packages & Profiles</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0 ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item active text-primary fw-medium">Packages</li>
            </ul>
        </div>
        <div class="page-header-right d-flex gap-2">
            <a href="{{ route('lab.packages.create') }}" wire:navigate class="btn btn-primary px-4">
                <i class="feather-plus me-2"></i>Create New Package
            </a>
        </div>
    </div>

    <div class="main-content mt-4">
        
        @if (session()->has('message'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
                <i class="feather-check-circle fs-4 me-2"></i>
                <strong>{{ session('message') }}</strong>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
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
                            class="btn btn-soft-danger h-100 w-100 d-flex align-items-center justify-content-center">
                            <i class="feather-refresh-ccw fs-12 me-2"></i>
                            <span class="fw-bold fs-12 uppercase">RESET</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Code</th>
                                <th class="py-3">Package Name</th>
                                <th class="py-3">Tests Included</th>
                                <th class="py-3">Pricing (₹)</th>
                                <th class="py-3">Status</th>
                                <th class="text-end pe-4" style="width: 120px;">Actions</th>
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
                                        <small class="text-muted fw-medium fs-11 text-uppercase">{{ $pkg->department }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar-text avatar-xs bg-soft-info text-info rounded-circle me-3 fw-bold text-center d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">{{ is_array($pkg->linked_test_ids) ? count($pkg->linked_test_ids) : 0 }}</span>
                                            <span class="fs-12 fw-medium text-dark">Included Tests</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-14">₹{{ number_format($pkg->mrp, 2) }}</div>
                                        <div class="fs-11 text-muted">B2B: ₹{{ number_format($pkg->b2b_price, 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input shadow-none" type="checkbox" role="switch" wire:click="toggleStatus({{ $pkg->id }})" {{ $pkg->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <a href="{{ route('lab.packages.edit', $pkg->id) }}" wire:navigate class="btn btn-icon btn-soft-info btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </a>
                                            <button wire:click="delete({{ $pkg->id }})" 
                                                wire:confirm="Are you sure you want to delete this package?" 
                                                class="btn btn-icon btn-soft-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-layers fs-1 opacity-25"></i></div>
                                        <h6 class="fw-bold text-dark">No Packages Found</h6>
                                        <p class="text-muted fs-13">Create profiles like 'Full Body Checkup' or 'Fever Panel'.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $packages->links() }}
            </div>
        </div>
    </div>

    <style>
        .fs-11 { font-size: 11px; }
        .fs-14 { font-size: 14px; }
    </style>
</div>
```html
<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Lab Network & Settlements</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Lab Network</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary px-4 disabled" title="Labs register themselves">
                <i class="feather-user-plus me-2"></i>Registration Active
            </button>
        </div>
    </div>

    <div class="main-content mt-4">
        @if (session()->has('message') || session()->has('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 mb-4">
                <i class="feather-check-circle fs-4 text-success me-3"></i>
                <div class="fw-bold">{{ session('message') ?? session('success') }}</div>
                <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filter Card -->
        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search lab name, email or mobile...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2 border">
                            <i class="feather-info me-2"></i>Total Labs: {{ $labs->total() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Lab / Institution</th>
                                <th>Contact Details</th>
                                <th>Subscription</th>
                                <th>Registered On</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($labs as $lab)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-text avatar-md bg-soft-primary text-primary me-3 rounded-circle fw-bold text-center" style="width: 42px; height: 42px; line-height: 42px;">
                                                {{ substr($lab->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14 mb-0">{{ $lab->name }}</div>
                                                <div class="text-muted fs-11">Lab ID: #{{ $lab->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-13 text-dark fw-medium">{{ $lab->email }}</div>
                                        <div class="text-muted fs-11">{{ $lab->mobile ?? 'No phone' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info px-2 py-1 fs-10 border">Trial Pack</span>
                                    </td>
                                    <td>
                                        <div class="fs-12 text-dark">{{ $lab->created_at->format('d M, Y') }}</div>
                                        <div class="text-muted fs-10">{{ $lab->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $lab->id }})" 
                                                {{ $lab->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button class="btn btn-sm btn-icon btn-soft-primary" data-bs-toggle="tooltip" title="View Lab Hisaab/Settlements">
                                                <i class="feather-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip" title="Deactivate">
                                                <i class="feather-slash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted"><i class="feather-user-x fs-1 d-block mb-2"></i> No labs found in the network.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $labs->links() }}
            </div>
        </div>
    </div>

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.1); }
        .bg-soft-info { background-color: rgba(63, 194, 255, 0.1); }
        .fs-10 { font-size: 10px; }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-13 { font-size: 13px; }
        .fs-14 { font-size: 14px; }
    </style>
</div>

<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Labs</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Lab Network</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary px-4 shadow-sm" wire:click="openRegistrationModal">
                <i class="feather-plus me-2"></i>Register New Lab
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
                    <div class="col-md-4">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="feather-search text-primary"></i>
                            </span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                class="form-control" 
                                placeholder="Search lab name, email or mobile...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="subscriptionFilter" class="form-select shadow-sm border-0" style="height: 48px; border-radius: 12px;">
                            <option value="all">All Subscriptions</option>
                            <option value="active">Active Plan</option>
                            <option value="expiring_soon">Expiring (15 Days)</option>
                            <option value="expired">Expired Plans</option>
                        </select>
                    </div>
                    <div class="col-md-5 text-md-end">
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
                                <th>Remaining Status</th>
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
                                        @if($lab->plan)
                                            <span class="badge bg-soft-info text-info px-2 py-1 fs-10 border border-info border-opacity-25">{{ $lab->plan->name }}</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary px-2 py-1 fs-10 border">No Plan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $days = $lab->days_left;
                                            $statusClass = 'success';
                                            $label = $days . ' Days';
                                            
                                            if ($days === null) { $label = 'N/A'; $statusClass = 'secondary'; }
                                            elseif ($days < 0) { $label = 'Expired'; $statusClass = 'danger'; }
                                            elseif ($days <= 15) { $label = $days . ' Days Left'; $statusClass = 'warning'; }
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="bg-{{ $statusClass }} rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                            <span class="fw-bold fs-11 text-{{ $statusClass }}">{{ $label }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-12 text-dark">{{ $lab->created_at->format('d M, Y') }}</div>
                                        <div class="text-muted fs-10">{{ $lab->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $lab->id }})" 
                                                {{ $lab->status === 'active' ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button class="btn btn-sm btn-icon btn-soft-primary" wire:click="viewDetails({{ $lab->id }})" title="View Details">
                                                <i class="feather-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-soft-info" wire:click="editLab({{ $lab->id }})" title="Edit Lab">
                                                <i class="feather-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-soft-danger" wire:click="toggleStatus({{ $lab->id }})" title="{{ $lab->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                                <i class="feather-{{ $lab->status === 'active' ? 'slash' : 'check' }}"></i>
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

    <!-- Registration Modal -->
    @if($isRegistrationModalOpen)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">{{ $editingLabId ? 'Update Lab Details' : 'Register New Lab/Tenant' }}</h5>
                    <button type="button" class="btn-close shadow-none" wire:click="closeRegistrationModal"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit.prevent="createLab">
                        <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Lab Details</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Lab Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="labName" placeholder="E.g. Apex Diagnostics">
                                @error('labName') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Primary Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model.defer="labEmail" placeholder="lab@example.com">
                                @error('labEmail') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="labPhone" placeholder="10-digit number">
                                @error('labPhone') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subscription Plan <span class="text-danger">*</span></label>
                                <select class="form-select" wire:model.defer="planId">
                                    <option value="">Select Plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }} (Rs. {{ $plan->price }})</option>
                                    @endforeach
                                </select>
                                @error('planId') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Lab Full Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" wire:model.defer="labAddress" rows="2" placeholder="Street, City, ZIP"></textarea>
                                @error('labAddress') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary">Admin User Credentials</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Admin Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="adminName" placeholder="Dr. John Doe">
                                @error('adminName') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Admin Login Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" wire:model.defer="adminEmail" placeholder="admin@apex.com">
                                @error('adminEmail') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" wire:model.defer="adminPassword" placeholder="Minimum 6 characters">
                                @error('adminPassword') <span class="text-danger fs-11 mt-1 d-block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-light shadow-sm" wire:click="closeRegistrationModal">Cancel</button>
                            <button type="submit" class="btn btn-primary shadow-sm px-4">
                                <span wire:loading wire:target="createLab" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="feather-save me-2" wire:loading.remove wire:target="createLab"></i> {{ $editingLabId ? 'Update Lab' : 'Register & Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- View Details Modal -->
    @if($isViewModalOpen && $selectedLab)
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); z-index: 1051;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light rounded-top-4 py-3">
                    <h5 class="modal-title fw-bold fs-15"><i class="feather-info me-2 text-primary"></i>Lab Summary</h5>
                    <button type="button" class="btn-close shadow-none" wire:click="$set('isViewModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar-text avatar-xl bg-soft-primary text-primary mx-auto rounded-circle mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 24px;">
                            {{ substr($selectedLab->name, 0, 1) }}
                        </div>
                        <h4 class="fw-bold text-dark mb-1">{{ $selectedLab->name }}</h4>
                        <span class="badge bg-soft-info text-info border border-info border-opacity-25 px-3 py-1">{{ $selectedLab->plan->name ?? 'No Plan' }}</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 border-bottom pb-2">
                            <label class="fs-10 text-muted text-uppercase fw-bold mb-1">Status</label>
                            <div class="d-flex align-items-center">
                                <span class="badge rounded-pill bg-{{ $selectedLab->status === 'active' ? 'success' : 'danger' }} me-2" style="width: 8px; height: 8px; padding: 0;"></span>
                                <span class="fw-bold fs-13 text-capitalize">{{ $selectedLab->status }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="fs-10 text-muted text-uppercase fw-bold mb-1">Email</label>
                            <div class="fs-13 text-dark fw-medium">{{ $selectedLab->email }}</div>
                        </div>
                        <div class="col-6">
                            <label class="fs-10 text-muted text-uppercase fw-bold mb-1">Phone</label>
                            <div class="fs-13 text-dark fw-medium">{{ $selectedLab->phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="fs-10 text-muted text-uppercase fw-bold mb-1">Address</label>
                            <div class="fs-12 text-muted">{{ $selectedLab->address }}</div>
                        </div>
                        
                        @if($selectedLab->admin)
                        <div class="col-12 mt-4">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="fs-11 fw-bold text-uppercase text-primary mb-2">Admin Account</h6>
                                <div class="fs-13 fw-bold text-dark">{{ $selectedLab->admin->name }}</div>
                                <div class="fs-12 text-muted">{{ $selectedLab->admin->email }}</div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-between align-items-center fs-11 text-muted border-top pt-2">
                                <span>Registered On: {{ $selectedLab->created_at->format('d M, Y') }}</span>
                                <span>Subscription Ends: {{ $selectedLab->trial_ends_at ? $selectedLab->trial_ends_at->format('d M, Y') : 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary w-100 py-2 rounded-3 shadow-none fw-bold" wire:click="$set('isViewModalOpen', false)">Close Overview</button>
                </div>
            </div>
        </div>
    </div>
    @endif

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

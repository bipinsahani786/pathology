<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Phlebotomists</h5>
                <p class="fs-13 text-muted mb-0">Manage home sample collection staff.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Phlebotomists</li>
            </ul>
        </div>
        <div class="page-header-right">
            @can('create phlebotomists')
            <button wire:click="create" class="btn btn-primary px-4 shadow-sm d-flex align-items-center">
                <i class="feather-user-plus me-2"></i> Add Phlebotomist
            </button>
            @endcan
        </div>
    </div>

    <div class="main-content">
        @if(session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
            <i class="feather-check-circle fs-4 me-2"></i> <strong>{{ session('message') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center py-3 alert-dismissible fade show">
            <i class="feather-alert-triangle fs-4 me-2"></i> <strong>{{ session('error') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="input-group search-group shadow-sm">
                            <span class="input-group-text"><i class="feather-search text-primary"></i></span>
                            <input type="text" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="Search by name or phone...">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light fs-11 fw-bold text-uppercase text-muted">
                            <tr>
                                <th class="ps-4 py-3">Phlebotomist</th>
                                <th class="py-3">Vehicle</th>
                                <th class="py-3">Working Hours</th>
                                <th class="py-3">Commission/Visit</th>
                                <th class="text-center py-3">Availability</th>
                                <th class="text-center pe-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($phlebotomists as $phlebo)
                            <tr wire:key="ph-{{ $phlebo->id }}" class="border-bottom border-light">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-sm" style="width:45px;height:45px;">
                                            {{ strtoupper(substr($phlebo->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $phlebo->name }}</div>
                                            <div class="fs-12 text-muted">{{ $phlebo->phone }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($phlebo->phlebotomistProfile?->vehicle_type)
                                    <span class="badge bg-soft-info text-info rounded-pill">
                                        <i class="feather-navigation me-1"></i>
                                        {{ $phlebo->phlebotomistProfile->vehicle_type }}
                                        @if($phlebo->phlebotomistProfile->vehicle_number)
                                        — {{ $phlebo->phlebotomistProfile->vehicle_number }}
                                        @endif
                                    </span>
                                    @else
                                    <span class="text-muted fs-12">Not set</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="text-dark fs-12">
                                        {{ $phlebo->phlebotomistProfile?->working_hours_start ?? '08:00' }}
                                        —
                                        {{ $phlebo->phlebotomistProfile?->working_hours_end ?? '18:00' }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="fw-semibold text-dark">₹{{ number_format($phlebo->phlebotomistProfile?->commission_per_visit ?? 0, 2) }}</span>
                                </td>
                                <td class="text-center py-3">
                                    @can('edit phlebotomists')
                                    <div wire:click="toggleAvailability({{ $phlebo->id }})" class="cursor-pointer" title="Click to toggle">
                                        @if($phlebo->phlebotomistProfile?->is_available)
                                        <span class="badge bg-soft-success text-success rounded-pill px-3 py-2">
                                            <i class="feather-check-circle me-1"></i> Available
                                        </span>
                                        @else
                                        <span class="badge bg-soft-danger text-danger rounded-pill px-3 py-2">
                                            <i class="feather-x-circle me-1"></i> On Leave
                                        </span>
                                        @endif
                                    </div>
                                    @else
                                        @if($phlebo->phlebotomistProfile?->is_available)
                                        <span class="badge bg-soft-success text-success rounded-pill">Available</span>
                                        @else
                                        <span class="badge bg-soft-danger text-danger rounded-pill">On Leave</span>
                                        @endif
                                    @endcan
                                </td>
                                <td class="text-center pe-4 py-3">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @can('edit phlebotomists')
                                        <button wire:click="edit({{ $phlebo->id }})" class="btn btn-sm btn-outline-primary rounded-3" title="Edit Phlebotomist">
                                            <i class="feather-edit-2"></i>
                                        </button>
                                        @endcan
                                        @if(config('features.impersonation', true) && (auth()->user()->hasAnyRole(['super_admin', 'lab_admin', 'branch_admin']) || auth()->user()->can('edit phlebotomists') || auth()->user()->can('view phlebotomists')))
                                        <a href="{{ route('impersonate.start', $phlebo->id) }}" class="btn btn-sm btn-outline-dark rounded-3" title="Login As {{ $phlebo->name }}">
                                            <i class="feather-user-check"></i>
                                        </a>
                                        @endif
                                        @can('delete phlebotomists')
                                        <button wire:click="delete({{ $phlebo->id }})" class="btn btn-sm btn-outline-danger rounded-3"
                                            wire:confirm="Are you sure you want to delete this phlebotomist?" title="Delete Phlebotomist">
                                            <i class="feather-trash-2"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="feather-user-x fs-1 d-block mb-2 opacity-25"></i>
                                    No phlebotomists found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3">{{ $phlebotomists->links() }}</div>
            </div>
        </div>
    </div>

    {{-- ADD / EDIT MODAL --}}
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,.5);">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="feather-user-plus me-2 text-primary"></i>
                        {{ $user_id ? 'Edit Phlebotomist' : 'Add New Phlebotomist' }}
                    </h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input wire:model="name" type="text" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Rajesh Kumar">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input wire:model="phone" type="text" class="form-control @error('phone') is-invalid @enderror" placeholder="10-digit mobile">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Password {{ $user_id ? '(leave blank to keep)' : '' }}</label>
                            <input wire:model="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="min 6 chars">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vehicle Type</label>
                            <select wire:model="vehicle_type" class="form-select">
                                <option value="">Select...</option>
                                @foreach(['Bike','Car','Scooty','Other'] as $vt)
                                <option value="{{ $vt }}">{{ $vt }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vehicle Number</label>
                            <input wire:model="vehicle_number" type="text" class="form-control" placeholder="e.g. MH12AB1234">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Commission / Visit (₹)</label>
                            <input wire:model="commission_per_visit" type="number" min="0" step="0.01" class="form-control @error('commission_per_visit') is-invalid @enderror">
                            @error('commission_per_visit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Work Start Time</label>
                            <input wire:model="working_hours_start" type="time" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Work End Time</label>
                            <input wire:model="working_hours_end" type="time" class="form-control">
                        </div>
                        @if($user_id)
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input wire:model="is_available" class="form-check-input" type="checkbox" role="switch" id="isAvailableSwitch">
                                <label class="form-check-label fw-semibold" for="isAvailableSwitch">
                                    Currently Available (uncheck for leave/off day)
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    @if($user_id && config('features.impersonation', true) && (auth()->user()->hasAnyRole(['super_admin', 'lab_admin', 'branch_admin']) || auth()->user()->can('edit phlebotomists')))
                    <a href="{{ route('impersonate.start', $user_id) }}" class="btn btn-outline-dark me-auto" title="Login As this Phlebotomist">
                        <i class="feather-user-check me-1"></i> Login As Phlebotomist
                    </a>
                    @endif
                    <button wire:click="closeModal" class="btn btn-outline-secondary px-4">Cancel</button>
                    <button wire:click="store" wire:loading.attr="disabled" class="btn btn-primary px-5">
                        <span wire:loading wire:target="store" class="spinner-border spinner-border-sm me-2"></span>
                        {{ $user_id ? 'Update' : 'Save' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

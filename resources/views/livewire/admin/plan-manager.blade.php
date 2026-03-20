<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Subscription Plans</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-md-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-muted">Business Setup</li>
                <li class="breadcrumb-item text-primary fw-medium">Manage Plans</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary px-4">
                <i class="feather-plus me-2"></i>Add New Plan
            </button>
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

        <div class="card stretch stretch-full border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">Pricing Tiers</h6>
                </div>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <div class="table-responsive border-top">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                            <tr>
                                <th class="ps-4">Plan Information</th>
                                <th>Price (₹)</th>
                                <th>Validity</th>
                                <th>Features & Limits</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-text avatar-md bg-soft-primary text-primary me-3 rounded-circle fw-bold text-center" style="width: 42px; height: 42px; line-height: 42px;">
                                                {{ substr($plan->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14 mb-0">{{ $plan->name }}</div>
                                                <div class="text-muted fs-11">Lab Subscription</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary fs-15">₹{{ number_format($plan->price, 2) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-info text-info rounded-pill px-3 fw-medium border">
                                            {{ $plan->duration_in_days }} Days
                                        </span>
                                    </td>
                                    <td>
                                        @if ($plan->features)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($plan->features as $key => $value)
                                                    <span class="badge bg-soft-secondary text-secondary rounded-pill px-2 fs-10 border">
                                                        {{ str_replace('_', ' ', $key) }}: {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fs-12">No limits set</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $plan->id }})" 
                                                {{ $plan->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button wire:click="edit({{ $plan->id }})" class="btn btn-icon btn-soft-info btn-sm" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </button>
                                            <button wire:click="delete({{ $plan->id }})" 
                                                wire:confirm="Permanent Delete this pricing plan?" 
                                                class="btn btn-icon btn-soft-danger btn-sm" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="feather-credit-card fs-1 d-block mb-2"></i> No plans found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $plans->links() }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-{{ $plan_id ? 'edit' : 'plus-circle' }} text-primary me-2"></i>
                            {{ $plan_id ? 'Edit Pricing Plan' : 'New Subscription Plan' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none" aria-label="Close"></button>
                    </div>

                    <form wire:submit.prevent="store" class="d-flex flex-column mb-0 overflow-hidden">
                        <div class="modal-body p-4 bg-white">
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="e.g. Starter Pack">
                                    @error('name') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" wire:model="price">
                                    @error('price') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Validity (Days) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('duration_in_days') is-invalid @enderror" wire:model="duration_in_days">
                                    @error('duration_in_days') <span class="text-danger fs-11 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3 d-flex justify-content-between align-items-center bg-light p-3 rounded-3 border">
                                <h6 class="fw-bold text-dark mb-0">Plan Limits & Features</h6>
                                <button type="button" wire:click="addFeature" class="btn btn-soft-primary btn-sm px-3 rounded-pill">
                                    <i class="feather-plus me-1"></i> Add Feature
                                </button>
                            </div>

                            <div class="border rounded-4 bg-white shadow-sm overflow-hidden mb-4">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr class="fs-10 text-uppercase text-muted fw-bold">
                                            <th class="ps-3 py-2">Feature Key</th>
                                            <th>Value/Limit</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($features as $index => $feature)
                                            <tr wire:key="feature-{{ $index }}" class="border-bottom border-light">
                                                <td class="ps-3 py-2">
                                                    <input type="text" class="form-control form-control-sm" wire:model="features.{{ $index }}.key" placeholder="max_branches">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" wire:model="features.{{ $index }}.value" placeholder="5">
                                                </td>
                                                <td class="text-end pe-3">
                                                    <button type="button" wire:click="removeFeature({{ $index }})" class="btn btn-icon btn-soft-danger btn-sm border-0">
                                                        <i class="feather-trash-2"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-0">
                                <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                                    <div class="me-auto fw-bold text-dark small">Plan Active</div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-end gap-2 shadow-sm">
                            <button type="button" wire:click="closeModal" class="btn btn-light px-4">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold">
                                <i class="feather-save me-2"></i>{{ $plan_id ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-14 { font-size: 14px; }
        .fs-15 { font-size: 15px; }
    </style>
</div>

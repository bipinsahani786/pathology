<div>
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="page-header-left d-flex align-items-center flex-wrap">
            <div class="page-header-title">
                <h5 class="m-b-10">Subscription Plans</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate>Home</a></li>
                <li class="breadcrumb-item">Admin Setup</li>
                <li class="breadcrumb-item">Manage Plans</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary w-100 w-md-auto shadow-sm">
                <i class="feather-plus me-2"></i>Add New Plan
            </button>
        </div>
    </div>
    <div class="main-content">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show border-success" role="alert">
                <div class="d-flex align-items-center">
                    <i class="feather-check-circle fs-4 text-success me-2"></i>
                    <strong>{{ session('message') }}</strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card stretch stretch-full border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0">Pricing Plans List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Plan Name</th>
                                <th>Price (₹)</th>
                                <th>Validity</th>
                                <th>Features & Limits</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($plans as $plan)
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">{{ $plan->name }}</td>
                                    <td class="fw-semibold text-primary">₹{{ number_format($plan->price, 2) }}</td>
                                    <td>{{ $plan->duration_in_days }} Days</td>
                                    <td>
                                        @if ($plan->features)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach ($plan->features as $key => $value)
                                                    <span
                                                        class="badge bg-soft-info text-info border border-info-subtle">
                                                        {{ str_replace('_', ' ', $key) }}: {{ $value }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted fs-12">No limits added</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input class="form-check-input" type="checkbox"
                                                wire:click="toggleStatus({{ $plan->id }})"
                                                {{ $plan->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button wire:click="edit({{ $plan->id }})"
                                                class="avatar-text avatar-md bg-soft-info text-info rounded border-0"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </button>
                                            <button wire:click="delete({{ $plan->id }})"
                                                onclick="confirm('Are you sure you want to delete this plan?') || event.stopImmediatePropagation()"
                                                class="avatar-text avatar-md bg-soft-danger text-danger rounded border-0"
                                                data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="feather-inbox fs-1 d-block mb-2"></i>
                                        No subscription plans found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
             <div class="card-footer">
                {{ $plans->links() }}
            </div>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg rounded-3">
                    <div class="modal-header bg-soft-primary">
                        <h5 class="modal-title fw-bold text-primary">
                            {{ $plan_id ? 'Update Pricing Plan' : 'Create New Plan' }}</h5>
                        <button type="button" wire:click="closeModal" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4">
                            <div class="row g-4">

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Plan Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="e.g. Starter Pack">
                                    @error('name')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Price (₹) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.01"
                                        class="form-control @error('price') is-invalid @enderror" wire:model="price"
                                        placeholder="0.00">
                                    @error('price')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Duration (Days) <span
                                            class="text-danger">*</span></label>
                                    <input type="number"
                                        class="form-control @error('duration_in_days') is-invalid @enderror"
                                        wire:model="duration_in_days" placeholder="30">
                                    @error('duration_in_days')
                                        <span class="text-danger fs-11 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-3 bg-light p-3 rounded border">
                                        <div>
                                            <h6 class="fw-bold mb-0">Plan Limits & Features</h6>
                                            <p class="fs-12 text-muted mb-0">Define key-value limits (e.g.
                                                max_branches: 5).</p>
                                        </div>
                                        <button type="button" wire:click="addFeature"
                                            class="btn btn-primary btn-sm">
                                            <i class="feather-plus me-1"></i> Add Feature
                                        </button>
                                    </div>

                                    <div class="border rounded">
                                        <table class="table align-middle mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th width="45%" class="text-muted fs-12 py-3 border-bottom">
                                                        FEATURE KEY</th>
                                                    <th width="40%" class="text-muted fs-12 py-3 border-bottom">
                                                        VALUE/LIMIT</th>
                                                    <th width="15%"
                                                        class="text-center text-muted fs-12 py-3 border-bottom">ACTION
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($features as $index => $feature)
                                                    <tr>
                                                        <td class="p-3">
                                                            <input type="text" class="form-control"
                                                                wire:model="features.{{ $index }}.key"
                                                                placeholder="e.g. max_branches">
                                                        </td>
                                                        <td class="p-3">
                                                            <input type="text" class="form-control"
                                                                wire:model="features.{{ $index }}.value"
                                                                placeholder="e.g. 5">
                                                        </td>
                                                        <td class="text-center p-3">
                                                            <button type="button"
                                                                wire:click="removeFeature({{ $index }})"
                                                                class="btn btn-sm btn-light border text-danger shadow-sm">
                                                                <i class="feather-trash-2"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="activeStatus"
                                            wire:model="is_active">
                                        <label class="form-check-label fw-semibold ms-2" for="activeStatus">Mark Plan
                                            as Active</label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer bg-light border-top p-3 d-flex justify-content-end gap-2">
                            <button type="button" wire:click="closeModal" class="btn btn-light border px-4 fw-medium shadow-sm">Cancel</button>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm d-flex align-items-center transition-all hover-lift">
                                <div wire:loading.remove wire:target="store"><i class="feather-save me-2"></i> {{ $plan_id ? 'Update Plan' : 'Save Plan' }}</div>
                                <div wire:loading wire:target="store"><span class="spinner-border spinner-border-sm me-2" role="status"></span> Saving...</div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-info {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }
    </style>
</div>

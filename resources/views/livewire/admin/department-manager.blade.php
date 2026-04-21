<div>
    <!-- Page Header -->
    <div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2 gap-md-3">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">System Departments</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium">Departments</li>
            </ul>
        </div>
        <div class="page-header-right">
            <button wire:click="create" class="btn btn-primary shadow-sm px-4">
                <i class="feather-plus me-2"></i>New Department
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
                                placeholder="Search system departments...">
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <span class="badge bg-soft-info text-info rounded-pill px-3 py-2 border">
                            <i class="feather-globe me-2"></i>System-wide departments managed by Admin.
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
                                <th class="ps-4">Department Name</th>
                                <th>Global Status</th>
                                <th>Tests Linked</th>
                                <th class="text-end pe-4" style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $dept)
                                <tr class="border-bottom border-light" wire:key="dept-{{ $dept->id }}">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-text avatar-md bg-soft-primary text-primary me-3 rounded-circle fw-bold text-center" style="width: 42px; height: 42px; line-height: 42px;">
                                                {{ substr($dept->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-14 mb-0">{{ $dept->name }}</div>
                                                <div class="text-muted fs-11">System Department</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                wire:click="toggleStatus({{ $dept->id }})" 
                                                {{ $dept->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <span class="fs-12 ms-2 fw-medium {{ $dept->is_active ? 'text-success' : 'text-danger' }}">
                                                {{ $dept->is_active ? 'Active' : 'Hidden' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-soft-secondary text-secondary rounded-pill px-3 fw-medium border">
                                            {{ $dept->globalTestsCount ?? 0 }} Tests
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button wire:click="edit({{ $dept->id }})" class="btn btn-sm btn-icon btn-soft-info" data-bs-toggle="tooltip" title="Edit">
                                                <i class="feather-edit-3"></i>
                                            </button>
                                            <button wire:click="delete({{ $dept->id }})" 
                                                wire:confirm="Permanent Delete this system department? Warning: This might break global tests linking." 
                                                class="btn btn-sm btn-icon btn-soft-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="feather-trash-2"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted"><i class="feather-inbox fs-1 d-block mb-2"></i> No system departments found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="card-footer bg-white border-top border-light py-3 px-4">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light border-bottom p-3 px-4">
                        <h5 class="modal-title fw-bold text-dark">
                            <i class="feather-{{ $department_id ? 'edit' : 'plus-circle' }} text-primary me-2"></i>
                            {{ $department_id ? 'Edit Department' : 'New System Department' }}
                        </h5>
                        <button type="button" wire:click="closeModal" class="btn-close shadow-none" aria-label="Close"></button>
                    </div>

                    <form wire:submit.prevent="store">
                        <div class="modal-body p-4 bg-white">
                            <div class="mb-4">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Department Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="E.G. haematology">
                                @error('name') <span class="invalid-feedback fs-11">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Status</label>
                                <div class="d-flex align-items-center bg-light p-3 rounded-3 border">
                                    <div class="me-auto">
                                        <div class="fw-bold text-dark fs-13">Active Status</div>
                                        <p class="fs-11 text-muted mb-0">When inactive, labs cannot use this department.</p>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" wire:model="is_active" style="cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light border-top p-3 px-4 d-flex justify-content-end gap-2 shadow-sm">
                            <button type="button" wire:click="closeModal" class="btn btn-light border px-4">Cancel</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                <i class="feather-save me-2"></i>{{ $department_id ? 'Update' : 'Create' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <style>
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.1); }
        .bg-soft-info { background-color: rgba(63, 194, 255, 0.1); }
        .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1); }
        .fs-11 { font-size: 11px; }
        .fs-12 { font-size: 12px; }
        .fs-13 { font-size: 13px; }
        .fs-14 { font-size: 14px; }
    </style>
</div>

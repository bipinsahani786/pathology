<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Manage Departments</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Lab Operations</li>
                    <li class="breadcrumb-item">Departments</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                @can('create departments')
                    <button wire:click="create" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                        <i class="feather-plus me-2"></i>New Lab Department
                    </button>
                @endcan
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-white py-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="feather-search text-muted"></i>
                                        </span>
                                        <input type="text" wire:model.live="searchTerm" class="form-control border-start-0 ps-0 shadow-none" placeholder="Search departments...">
                                    </div>
                                </div>
                                <div class="col-md-8 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-soft-info text-info rounded-pill px-3 py-2">
                                        <i class="feather-info me-2"></i>System departments are mandatory and read-only.
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Department Name</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($departments as $dept)
                                        <tr class="{{ $dept->is_system ? 'bg-soft-light cursor-not-allowed' : '' }}">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm {{ $dept->is_system ? 'bg-primary' : 'bg-soft-primary text-primary' }} me-3">
                                                        {{ substr($dept->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $dept->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($dept->is_system)
                                                    <span class="badge bg-primary rounded-pill px-3">System</span>
                                                @else
                                                    <span class="badge bg-soft-secondary text-secondary rounded-pill px-3 border">Custom</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($dept->is_system)
                                                    <span class="badge bg-soft-success text-success rounded-pill px-3"><i class="feather-check-circle me-1"></i>Active</span>
                                                @else
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" 
                                                            wire:click="toggleStatus({{ $dept->id }})" 
                                                            {{ $dept->is_active ? 'checked' : '' }}
                                                            @cannot('edit departments') disabled @endcannot>
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                @if(!$dept->is_system)
                                                    @can('edit departments')
                                                        <button wire:click="edit({{ $dept->id }})" class="btn btn-sm btn-light-primary me-2 shadow-none border" title="Edit">
                                                            <i class="feather-edit-3"></i>
                                                        </button>
                                                    @endcan
                                                    @can('delete departments')
                                                        <button wire:click="delete({{ $dept->id }})" 
                                                            onclick="confirm('Are you sure you want to delete this department?') || event.stopImmediatePropagation()" 
                                                            class="btn btn-sm btn-light-danger shadow-none border" title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    @endcan
                                                @else
                                                    <button class="btn btn-sm btn-light-secondary opacity-50 px-3 fs-10" disabled>
                                                        <i class="feather-lock me-1"></i>Protected
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="feather-alert-circle d-block fs-1 mx-auto mb-3"></i>
                                                    <p>No departments found matching your criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-4">
                            {{ $departments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">{{ $department_id ? 'Edit Custom Department' : 'New Custom Department' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-group mb-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">Department Name</label>
                        <input type="text" wire:model="name" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="e.g. Molecular Path">
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        <p class="text-muted fs-11 mt-2">Create your own departments for internal test categorization.</p>
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label text-muted small fw-bold text-uppercase">Status</label>
                        <div class="d-flex align-items-center bg-light p-3 rounded">
                            <span class="me-auto text-dark small">Active and Visible</span>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" wire:model="is_active">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button wire:click="closeModal" class="btn btn-secondary px-4 fw-bold">Cancel</button>
                    <button wire:click="store" class="btn btn-primary px-4 fw-bold shadow-sm">
                        {{ $department_id ? 'Update Department' : 'Create Department' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

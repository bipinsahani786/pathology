<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Inventory Suppliers</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Suppliers</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <button wire:click="create" class="btn btn-primary px-4 py-2 shadow-sm rounded-pill">
                    <i class="feather-plus me-2"></i>New Supplier
                </button>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-white py-3 border-bottom-0">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group search-group shadow-sm">
                                        <span class="input-group-text bg-white">
                                            <i class="feather-search text-primary"></i>
                                        </span>
                                        <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                                            class="form-control" 
                                            placeholder="Search suppliers...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Supplier Name</th>
                                            <th>Contact Person</th>
                                            <th>Phone/Email</th>
                                            <th>Status</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($suppliers as $supplier)
                                        <tr wire:key="supplier-{{ $supplier->id }}">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm bg-soft-primary text-primary me-3">
                                                        {{ substr($supplier->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $supplier->name }}</h6>
                                                        <small class="text-muted">{{ $supplier->gst_number }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $supplier->contact_person }}</td>
                                            <td>
                                                <div><i class="feather-phone fs-11 me-1"></i>{{ $supplier->phone }}</div>
                                                <div class="text-muted small"><i class="feather-mail fs-11 me-1"></i>{{ $supplier->email }}</div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                        wire:click="toggleStatus({{ $supplier->id }})" 
                                                        {{ $supplier->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button wire:click="edit({{ $supplier->id }})" class="btn btn-sm btn-light-primary me-2 shadow-none border" title="Edit">
                                                    <i class="feather-edit-3"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="feather-alert-circle d-block fs-1 mx-auto mb-3"></i>
                                                    <p>No suppliers found matching your criteria.</p>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-4">
                            {{ $suppliers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">{{ $supplier_id ? 'Edit Supplier' : 'New Supplier' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Supplier Name</label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g. Acme Medical Supplies">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Contact Person</label>
                            <input type="text" wire:model="contact_person" class="form-control" placeholder="John Doe">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Phone</label>
                            <input type="text" wire:model="phone" class="form-control" placeholder="+91 9876543210">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Email</label>
                            <input type="email" wire:model="email" class="form-control" placeholder="supplier@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">GST Number</label>
                            <input type="text" wire:model="gst_number" class="form-control" placeholder="27AAAAA0000A1Z5">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Address</label>
                            <textarea wire:model="address" class="form-control" rows="2" placeholder="Full address..."></textarea>
                        </div>
                    </div>
                    <div class="form-group mb-0 mt-2">
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
                        {{ $supplier_id ? 'Update Supplier' : 'Create Supplier' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

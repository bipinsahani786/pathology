<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Receive Stock (Purchase)</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Receive Stock</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-download me-2 text-primary"></i>Stock Entry Form</h6>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit.prevent="store">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Supplier</label>
                                        <select wire:model="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Item / Product</label>
                                        <select wire:model="item_id" class="form-select @error('item_id') is-invalid @enderror">
                                            <option value="">Select Item</option>
                                            @foreach($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                            @endforeach
                                        </select>
                                        @error('item_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Quantity Received</label>
                                        <input type="number" step="0.01" wire:model="quantity" class="form-control @error('quantity') is-invalid @enderror">
                                        @error('quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Batch Number</label>
                                        <input type="text" wire:model="batch_number" class="form-control" placeholder="e.g. B12345">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Expiry Date</label>
                                        <input type="date" wire:model="expiry_date" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Purchase Price (Per Unit)</label>
                                        <input type="number" step="0.01" wire:model="purchase_price" class="form-control">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small fw-bold text-uppercase">MRP (Per Unit)</label>
                                        <input type="number" step="0.01" wire:model="mrp" class="form-control">
                                    </div>

                                    <div class="col-12 mb-4">
                                        <label class="form-label text-muted small fw-bold text-uppercase">Remarks</label>
                                        <textarea wire:model="remarks" class="form-control" rows="2" placeholder="Any notes..."></textarea>
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm rounded-pill fw-bold">
                                            <i class="feather-check-circle me-2"></i>Receive Stock
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

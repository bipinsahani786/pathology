<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Current Stock</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Stock</li>
                </ul>
            </div>
            <div class="page-header-right ms-auto">
                <button wire:click="exportStock" class="btn btn-soft-success px-4 py-2 shadow-sm rounded-pill">
                    <i class="feather-file-text me-2"></i>Export CSV
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
                                            placeholder="Search stock...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Item Name</th>
                                            <th>Current Stock</th>
                                            <th>Unit</th>
                                            <th>Active Batches</th>
                                            <th class="text-end pe-4">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($stocks as $stock)
                                        <tr wire:key="stock-{{ $stock->id }}">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-text avatar-sm bg-soft-success text-success me-3">
                                                        {{ substr($stock->item->name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $stock->item->name }}</h6>
                                                        <small class="text-muted">{{ $stock->item->category->name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fs-5 fw-bold {{ $stock->quantity <= $stock->item->min_stock_level ? 'text-danger' : 'text-dark' }}">
                                                    {{ $stock->quantity }}
                                                </span>
                                                @if($stock->quantity <= $stock->item->min_stock_level)
                                                    <span class="badge bg-soft-danger text-danger ms-1 small">Low</span>
                                                @endif
                                            </td>
                                            <td>{{ $stock->item->unit }}</td>
                                            <td>
                                                @php $activeBatches = $stock->batches->where('quantity', '>', 0); @endphp
                                                <span class="badge bg-light text-dark">{{ $activeBatches->count() }} Batches</span>
                                                @if($activeBatches->where('expiry_date', '<=', now()->addDays(30))->count() > 0)
                                                    <i class="feather-alert-triangle text-warning ms-1" title="Near Expiry"></i>
                                                @endif
                                            </td>
                                            <td class="text-end pe-4">
                                                <button wire:click="openAdjustment({{ $stock->id }})" class="btn btn-sm btn-light-primary shadow-none border">
                                                    <i class="feather-settings me-1"></i>Adjust
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No stock found. Ensure items are added to master.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-4">
                            {{ $stocks->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Adjustment Modal --}}
    @if($isAdjustmentModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom p-4">
                    <h5 class="modal-title fw-bold">Adjust Stock: {{ $selectedItem->item->name }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('isAdjustmentModalOpen', false)"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="btn-group w-100 mb-4" role="group">
                        <input type="radio" class="btn-check" wire:model.live="adjustment_type" value="in" id="adj_in" autocomplete="off">
                        <label class="btn btn-outline-success" for="adj_in"><i class="feather-plus-circle me-1"></i>Stock In (Add)</label>

                        <input type="radio" class="btn-check" wire:model.live="adjustment_type" value="out" id="adj_out" autocomplete="off">
                        <label class="btn btn-outline-danger" for="adj_out"><i class="feather-minus-circle me-1"></i>Stock Out (Reduce)</label>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Quantity ({{ $selectedItem->item->unit }})</label>
                            <input type="number" step="0.01" wire:model="adjustment_quantity" class="form-control form-control-lg @error('adjustment_quantity') is-invalid @enderror">
                            @error('adjustment_quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        
                        @if($adjustment_type == 'in')
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Batch Number</label>
                            <input type="text" wire:model="adjustment_batch_number" class="form-control" placeholder="Optional">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted small fw-bold text-uppercase">Expiry Date</label>
                            <input type="date" wire:model="adjustment_expiry_date" class="form-control">
                        </div>
                        @endif

                        <div class="col-12">
                            <label class="form-label text-muted small fw-bold text-uppercase">Remarks / Reason</label>
                            <textarea wire:model="adjustment_remarks" class="form-control @error('adjustment_remarks') is-invalid @enderror" placeholder="e.g. Received from main store, or Wastage..."></textarea>
                            @error('adjustment_remarks') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 p-4">
                    <button wire:click="$set('isAdjustmentModalOpen', false)" class="btn btn-secondary px-4 fw-bold">Cancel</button>
                    <button wire:click="storeAdjustment" class="btn btn-primary px-4 fw-bold shadow-sm">Save Adjustment</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

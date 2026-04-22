<div>
    <div class="nxl-content">
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10">Issue Items to Staff</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">Staff Issuance</li>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-user-plus me-2 text-primary"></i>Issuance Form</h6>
                        </div>
                        <div class="card-body p-4">
                            <form wire:submit.prevent="store">
                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Issue To (Staff)</label>
                                    <select wire:model="issued_to_id" class="form-select @error('issued_to_id') is-invalid @enderror">
                                        <option value="">Select Staff Member</option>
                                        @foreach($staff as $person)
                                            <option value="{{ $person->id }}">{{ $person->name }} ({{ $person->roles->first()?->name ?? 'No Role' }})</option>
                                        @endforeach
                                    </select>
                                    @error('issued_to_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Item to Issue</label>
                                    <select wire:model="item_id" class="form-select @error('item_id') is-invalid @enderror">
                                        <option value="">Select Item</option>
                                        @foreach($stocks as $stk)
                                            <option value="{{ $stk->item_id }}">{{ $stk->item->name }} (Avail: {{ $stk->quantity }} {{ $stk->item->unit }})</option>
                                        @endforeach
                                    </select>
                                    @error('item_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Quantity to Issue</label>
                                    <input type="number" step="0.01" wire:model="quantity" class="form-control @error('quantity') is-invalid @enderror">
                                    @error('quantity') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold text-uppercase">Remarks</label>
                                    <textarea wire:model="remarks" class="form-control" rows="2" placeholder="e.g. For Daily Sample Collection"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm fw-bold">
                                    <i class="feather-external-link me-2"></i>Confirm Issuance
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-header bg-white py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-clock me-2 text-primary"></i>Recent Issuance History</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th class="ps-4">Date</th>
                                            <th>Staff</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Issued By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($history as $record)
                                        <tr wire:key="history-{{ $record->id }}">
                                            <td class="ps-4">
                                                <div class="small fw-bold">{{ $record->created_at->format('d M, Y') }}</div>
                                                <div class="text-muted fs-11">{{ $record->created_at->format('h:i A') }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $record->issuedTo->name ?? 'N/A' }}</div>
                                            </td>
                                            <td>{{ $record->item->name }}</td>
                                            <td><span class="badge bg-soft-danger text-danger">-{{ $record->quantity }} {{ $record->item->unit }}</span></td>
                                            <td><small>{{ $record->performedBy->name }}</small></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">No issuance records found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top-0 p-4">
                            {{ $history->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

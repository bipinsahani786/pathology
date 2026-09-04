<div>
    <div class="nxl-content">

        {{-- ═══════════════════════════════════════════════════════ --}}
        {{-- PAGE HEADER --}}
        {{-- ═══════════════════════════════════════════════════════ --}}
        <div class="page-header">
            <div class="page-header-left d-flex align-items-center">
                <div class="page-header-title">
                    <h5 class="m-b-10 fw-bold">
                        @if($view === 'list')
                            Purchase History
                        @elseif($view === 'form')
                            New Purchase Entry
                        @else
                            Purchase Details
                        @endif
                    </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">Inventory</li>
                    <li class="breadcrumb-item">
                        @if($view !== 'list')
                            <a href="#" wire:click.prevent="showList" class="text-muted">Purchase History</a>
                        @else
                            Purchase History
                        @endif
                    </li>
                    @if($view === 'form')
                        <li class="breadcrumb-item">New Purchase</li>
                    @elseif($view === 'detail')
                        <li class="breadcrumb-item">Details</li>
                    @endif
                </ul>
            </div>
            <div class="page-header-right d-flex gap-2">
                @if($view !== 'form')
                    <button wire:click="showForm" class="btn btn-primary px-4 fw-bold">
                        <i class="feather-plus me-2"></i>New Purchase
                    </button>
                @else
                    <button wire:click="showList" class="btn btn-light px-4">
                        <i class="feather-arrow-left me-2"></i>Back to List
                    </button>
                @endif
            </div>
        </div>

        <div class="main-content">

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- LIST VIEW --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            @if($view === 'list')
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0 fw-bold"><i class="feather-list me-2 text-primary"></i>All Purchases</h6>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width:220px;">
                                <span class="input-group-text"><i class="feather-search"></i></span>
                                <input type="text" wire:model.live.debounce.300ms="searchTerm" class="form-control" placeholder="Search supplier...">
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if($purchases->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="feather-inbox d-block fs-1 mb-3 opacity-30"></i>
                                <p class="mb-2 fw-bold">No purchase records found.</p>
                                <p class="fs-12">Click <strong>New Purchase</strong> to add stock.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fs-11 fw-bold text-muted ps-4">#</th>
                                            <th class="fs-11 fw-bold text-muted">Date & Time</th>
                                            <th class="fs-11 fw-bold text-muted">Supplier</th>
                                            <th class="fs-11 fw-bold text-muted">Items</th>
                                            <th class="fs-11 fw-bold text-muted">Total Qty</th>
                                            <th class="fs-11 fw-bold text-muted">Received By</th>
                                            <th class="fs-11 fw-bold text-muted">Remarks</th>
                                            <th class="fs-11 fw-bold text-muted text-end pe-4">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchases as $idx => $purchase)
                                            <tr>
                                                <td class="ps-4 text-muted fs-12">{{ ($idx + 1) }}</td>
                                                <td>
                                                    <div class="fw-bold fs-13">{{ $purchase['created_at']->format('d M Y') }}</div>
                                                    <div class="text-muted fs-11">{{ $purchase['created_at']->format('h:i A') }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-primary text-primary fs-12 px-2">
                                                        <i class="feather-truck me-1"></i>
                                                        {{ $purchase['supplier']?->name ?? 'Unknown' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-soft-info text-info">{{ $purchase['items_count'] }} item(s)</span>
                                                </td>
                                                <td class="fw-bold">{{ number_format($purchase['total_qty'], 2) }}</td>
                                                <td class="fs-12 text-muted">{{ $purchase['performed_by']?->name ?? '-' }}</td>
                                                <td class="fs-12 text-muted" style="max-width:150px;">
                                                    <span class="text-truncate d-block" title="{{ $purchase['remarks'] }}">
                                                        {{ $purchase['remarks'] ? Str::limit($purchase['remarks'], 40) : '-' }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <button wire:click="viewDetail('{{ $purchase['group_id'] }}')"
                                                        class="btn btn-sm btn-soft-primary px-3">
                                                        <i class="feather-eye me-1"></i>View / Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Simple pagination --}}
                            @if($totalPurchases > $perPage)
                                <div class="card-footer bg-transparent d-flex justify-content-between align-items-center py-3 px-4">
                                    <div class="fs-12 text-muted">Showing {{ $purchases->count() }} of {{ $totalPurchases }} purchases</div>
                                    {{ $this->paginationView() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- NEW PURCHASE FORM --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            @if($view === 'form')
                <form wire:submit.prevent="store">
                    {{-- Supplier + Remarks --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3">
                            <h6 class="mb-0 fw-bold"><i class="feather-truck me-2 text-primary"></i>Purchase Details</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Supplier <span class="text-danger">*</span></label>
                                    <select wire:model="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Remarks / Invoice Ref</label>
                                    <input type="text" wire:model="remarks" class="form-control" placeholder="e.g. Invoice #1234, Monthly stock...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Items Table --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="feather-package me-2 text-warning"></i>Items Received</h6>
                            <button type="button" wire:click="addLine" class="btn btn-sm btn-soft-warning px-3">
                                <i class="feather-plus me-1"></i>Add Item Row
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="fs-11 fw-bold text-muted ps-3" style="min-width:200px;">Item / Product <span class="text-danger">*</span></th>
                                            <th class="fs-11 fw-bold text-muted" style="width:110px;">Qty <span class="text-danger">*</span></th>
                                            <th class="fs-11 fw-bold text-muted" style="width:130px;">Batch No.</th>
                                            <th class="fs-11 fw-bold text-muted" style="width:145px;">Expiry Date</th>
                                            <th class="fs-11 fw-bold text-muted" style="width:120px;">Buy Price/Unit</th>
                                            <th class="fs-11 fw-bold text-muted" style="width:120px;">MRP/Unit</th>
                                            <th style="width:45px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lines as $i => $line)
                                            <tr>
                                                <td class="ps-3">
                                                    <select wire:model="lines.{{ $i }}.item_id"
                                                        class="form-select form-select-sm @error('lines.'.$i.'.item_id') is-invalid @enderror">
                                                        <option value="">-- Select Item --</option>
                                                        @foreach($items as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                                        @endforeach
                                                    </select>
                                                    @error('lines.'.$i.'.item_id') <span class="invalid-feedback fs-10">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" min="0.01"
                                                        wire:model="lines.{{ $i }}.quantity"
                                                        class="form-control form-control-sm @error('lines.'.$i.'.quantity') is-invalid @enderror" placeholder="0">
                                                    @error('lines.'.$i.'.quantity') <span class="invalid-feedback fs-10">{{ $message }}</span> @enderror
                                                </td>
                                                <td>
                                                    <input type="text" wire:model="lines.{{ $i }}.batch_number"
                                                        class="form-control form-control-sm" placeholder="e.g. B123">
                                                </td>
                                                <td>
                                                    <input type="date" wire:model="lines.{{ $i }}.expiry_date"
                                                        class="form-control form-control-sm">
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="number" step="0.01" min="0"
                                                            wire:model="lines.{{ $i }}.purchase_price"
                                                            class="form-control" placeholder="0">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">₹</span>
                                                        <input type="number" step="0.01" min="0"
                                                            wire:model="lines.{{ $i }}.mrp"
                                                            class="form-control" placeholder="0">
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if(count($lines) > 1)
                                                        <button type="button" wire:click="removeLine({{ $i }})"
                                                            class="btn btn-xs btn-outline-danger p-1" title="Remove row">
                                                            <i class="feather-trash-2 fs-11"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <button type="button" wire:click="addLine" class="btn btn-sm btn-outline-secondary">
                                <i class="feather-plus me-1"></i>Add Another Item
                            </button>
                            <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow-sm"
                                wire:loading.attr="disabled">
                                <span wire:loading wire:target="store" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="feather-check-circle me-2" wire:loading.remove wire:target="store"></i>
                                Save Purchase
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- DETAIL / EDIT VIEW --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            @if($view === 'detail' && $detailTransactions)
                @php $first = $detailTransactions->first(); @endphp

                {{-- Summary Card --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                            <div class="fs-11 text-muted fw-bold text-uppercase mb-1">Supplier</div>
                            <div class="fw-bold fs-14">{{ $first?->supplier?->name ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                            <div class="fs-11 text-muted fw-bold text-uppercase mb-1">Date</div>
                            <div class="fw-bold fs-14">{{ $first?->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                            <div class="fs-11 text-muted fw-bold text-uppercase mb-1">Items Count</div>
                            <div class="fw-bold fs-14">{{ $detailTransactions->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm rounded-4 text-center p-3">
                            <div class="fs-11 text-muted fw-bold text-uppercase mb-1">Total Qty</div>
                            <div class="fw-bold fs-14">{{ number_format($detailTransactions->sum('quantity'), 2) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Editable Fields --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="feather-edit-2 me-2 text-warning"></i>Edit Purchase Details</h6>
                        <span class="badge bg-soft-info text-info fs-11"><i class="feather-info me-1"></i>Quantity update karne par stock & batch automatically adjust honge</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fs-11 fw-bold text-muted text-uppercase mb-1">Remarks / Invoice Ref</label>
                                <input type="text" wire:model="editRemarks" class="form-control" placeholder="Remarks...">
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 fs-13"><i class="feather-package me-2 text-primary"></i>Batch Details (Editable)</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fs-11 fw-bold text-muted">Item</th>
                                        <th class="fs-11 fw-bold text-muted" style="width:130px;">Qty <span class="text-danger">*</span></th>
                                        <th class="fs-11 fw-bold text-muted" style="width:130px;">Batch No.</th>
                                        <th class="fs-11 fw-bold text-muted" style="width:145px;">Expiry Date</th>
                                        <th class="fs-11 fw-bold text-muted" style="width:120px;">Buy Price/Unit</th>
                                        <th class="fs-11 fw-bold text-muted" style="width:120px;">MRP/Unit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($editLines as $ei => $eline)
                                        <tr>
                                            <td>
                                                <div class="fw-bold fs-13">{{ $eline['item_name'] }}</div>
                                                <div class="text-muted fs-11">{{ $eline['unit'] }}</div>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0.01"
                                                    wire:model="editLines.{{ $ei }}.quantity"
                                                    class="form-control form-control-sm @error('editLines.'.$ei.'.quantity') is-invalid @enderror">
                                                @error('editLines.'.$ei.'.quantity') <span class="invalid-feedback fs-10 d-block">{{ $message }}</span> @enderror
                                            </td>
                                            <td>
                                                <input type="text" wire:model="editLines.{{ $ei }}.batch_number"
                                                    class="form-control form-control-sm" placeholder="Batch no.">
                                            </td>
                                            <td>
                                                <input type="date" wire:model="editLines.{{ $ei }}.expiry_date"
                                                    class="form-control form-control-sm">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" step="0.01" min="0"
                                                        wire:model="editLines.{{ $ei }}.purchase_price"
                                                        class="form-control" placeholder="0">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" step="0.01" min="0"
                                                        wire:model="editLines.{{ $ei }}.mrp"
                                                        class="form-control" placeholder="0">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent d-flex justify-content-end gap-2 py-3">
                        <button wire:click="showList" class="btn btn-light px-4">
                            <i class="feather-arrow-left me-2"></i>Back
                        </button>
                        <button wire:click="saveEdit" class="btn btn-primary px-5 fw-bold"
                            wire:loading.attr="disabled">
                            <span wire:loading wire:target="saveEdit" class="spinner-border spinner-border-sm me-2"></span>
                            <i class="feather-save me-2" wire:loading.remove wire:target="saveEdit"></i>
                            Save Changes
                        </button>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<div>
    <div class="page-header mb-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10 fw-bold text-dark">Payout History</h5>
                <p class="fs-13 text-muted mb-0 font-medium">Review your commission settlements and payouts.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted small">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium small">Settlements</li>
            </ul>
        </div>
        <div class="page-header-right">
            <div class="shadow-sm border rounded-3 bg-white px-2">
                <select wire:model.live="dateRange" class="form-select border-0 shadow-none fs-12 fw-bold text-dark cursor-pointer py-1">
                    <option value="all">📅 All Time</option>
                    <option value="today">📅 Today</option>
                    <option value="week">📅 This Week</option>
                    <option value="month">📅 This Month</option>
                </select>
            </div>
            @if(auth()->user()->hasRole('collection_center'))
                <button wire:click="openModal" class="btn btn-primary btn-sm rounded-3 px-3 fw-bold fs-11">
                    <i class="feather-plus me-1"></i>Record Payment to Lab
                </button>
            @endif
        </div>
    </div>

    <div class="main-content">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark"><i class="feather-credit-card me-2 text-primary"></i>Payout History</h6>
                <div class="ms-auto d-flex gap-2">
                    <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fs-11 fw-bold">{{ $settlements->total() }} Settlements</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 200px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-soft-light-gray fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3 border-0">Settlement Date</th>
                                <th class="py-3 border-0">Amount (₹)</th>
                                <th class="py-3 border-0">Payment Mode</th>
                                <th class="py-3 border-0">Reference No.</th>
                                <th class="text-center pe-4 py-3 border-0">Status</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($settlements as $s)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark fs-14">{{ $s->payment_date->format('d M, Y') }}</div>
                                        <div class="fs-10 text-muted">{{ $s->payment_date->format('h:i A') }}</div>
                                    </td>
                                    <td>
                                        <div class="fs-15 fw-bold text-success">₹{{ number_format($s->amount, 2) }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border border-opacity-10 py-1 px-2 fs-11 fw-medium text-uppercase ls-1">
                                            {{ $s->payment_mode }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-dark font-medium">{{ $s->reference_number ?? '---' }}</div>
                                    </td>
                                    <td class="text-center pe-4">
                                        @php
                                            $st = $s->status ?? 'Approved';
                                            $color = $st === 'Approved' ? 'success' : ($st === 'Pending' ? 'warning' : 'danger');
                                            $icon = $st === 'Approved' ? 'check-circle' : ($st === 'Pending' ? 'clock' : 'x-circle');
                                        @endphp
                                        <span class="badge bg-soft-{{ $color }} text-{{ $color }} rounded-pill px-3 py-1 fs-11 fw-bold">
                                            <i class="feather-{{ $icon }} me-1"></i>{{ $st }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-credit-card" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Settlements Found</h6>
                                        <p class="text-muted fs-13 mb-0">Your payout history will appear here once the lab processes your commissions.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($settlements->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $settlements->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Record Payment Modal --}}
    @if($isModalOpen)
        <div class="modal-backdrop fade show" style="z-index: 1040;"></div>
        <div class="modal fade show d-block" style="z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-light p-4 pb-0">
                        <h5 class="modal-title fw-bold text-dark"><i class="feather-plus-circle me-2 text-primary"></i>Record Payment to Lab</h5>
                        <button type="button" class="btn-close" wire:click="$set('isModalOpen', false)"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Amount Paid (₹) <span class="text-danger">*</span></label>
                                <input type="number" wire:model="amount" class="form-control form-control-lg fw-bold text-primary @error('amount') is-invalid @enderror" placeholder="0.00">
                                @error('amount') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" wire:model="payment_date" class="form-control @error('payment_date') is-invalid @enderror">
                                @error('payment_date') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Payment Mode <span class="text-danger">*</span></label>
                                <select wire:model="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror">
                                    <option value="UPI">UPI / QR Scan</option>
                                    <option value="Cash">Cash to Lab</option>
                                    <option value="Bank Transfer">Bank Transfer (NEFT/IMPS)</option>
                                    <option value="Check">Check</option>
                                </select>
                                @error('payment_mode') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Transaction Reference No / UTR</label>
                                <input type="text" wire:model="reference_no" class="form-control" placeholder="Optional reference number">
                            </div>
                            <div class="col-12">
                                <label class="form-label fs-12 fw-bold text-muted text-uppercase">Notes</label>
                                <textarea wire:model="notes" class="form-control" rows="2" placeholder="Any additional details..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-light p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-3 px-4 fw-bold fs-13" wire:click="$set('isModalOpen', false)">Cancel</button>
                        <button type="button" class="btn btn-primary rounded-3 px-4 fw-bold fs-13" wire:click="recordPayment">
                            <span wire:loading.remove wire:target="recordPayment"><i class="feather-save me-1"></i>Save Payment</span>
                            <span wire:loading wire:target="recordPayment"><span class="spinner-border spinner-border-sm me-1"></span>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
        .ls-1 { letter-spacing: 0.5px; }
        .font-medium { font-weight: 500; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; }
        .bg-soft-light-gray { background-color: #f8f9fa !important; }
        .text-success { color: #10b981 !important; }
        .stretch-full { height: 100%; }
    </style>
</div>

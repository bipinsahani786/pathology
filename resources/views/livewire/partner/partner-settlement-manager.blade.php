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
                                        <span class="badge bg-soft-success text-success rounded-pill px-3 py-1 fs-11 fw-bold">
                                            <i class="feather-check-circle me-1"></i>Settled
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

    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .font-medium { font-weight: 500; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; }
        .bg-soft-light-gray { background-color: #f8f9fa !important; }
        .text-success { color: #10b981 !important; }
        .stretch-full { height: 100%; }
    </style>
</div>

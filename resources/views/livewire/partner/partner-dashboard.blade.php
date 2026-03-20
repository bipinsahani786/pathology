<div class="row g-4">
    <div class="col-12">
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Partner Dashboard</h4>
                <p class="text-muted small mb-0">Welcome back! Here's an overview of your earnings and settlements.</p>
            </div>
            <div class="mt-3 mt-md-0">
                <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fs-12 fw-bold">
                    <i class="feather-user me-1"></i>{{ $role }} Mode
                </span>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #3b71ca 0%, #2a5298 100%);">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start text-white mb-3">
                    <div>
                        <p class="mb-1 opacity-75 small fw-bold text-uppercase ls-1">Total Earnings</p>
                        <h2 class="fw-bold mb-0">₹{{ number_format($stats['total_earnings'], 2) }}</h2>
                    </div>
                    <div class="avatar-text avatar-md bg-white bg-opacity-10 rounded-3">
                        <i class="feather-trending-up text-white fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-20" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: 100%;"></div>
                </div>
                <p class="text-white text-opacity-75 small mt-3 mb-0">Lifetime income generated</p>
                <i class="feather-dollar-sign position-absolute end-0 bottom-0 text-white opacity-5 mb-n3 me-n3" style="font-size: 8rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #198754 0%, #146c43 100%);">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start text-white mb-3">
                    <div>
                        <p class="mb-1 opacity-75 small fw-bold text-uppercase ls-1">Settled Amount</p>
                        <h2 class="fw-bold mb-0">₹{{ number_format($stats['settled_amount'], 2) }}</h2>
                    </div>
                    <div class="avatar-text avatar-md bg-white bg-opacity-10 rounded-3">
                        <i class="feather-check-circle text-white fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-20" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: {{ $stats['total_earnings'] > 0 ? ($stats['settled_amount'] / $stats['total_earnings'] * 100) : 0 }}%;"></div>
                </div>
                <p class="text-white text-opacity-75 small mt-3 mb-0">Total payments received</p>
                <i class="feather-check-square position-absolute end-0 bottom-0 text-white opacity-5 mb-n3 me-n3" style="font-size: 8rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start text-white mb-3">
                    <div>
                        <p class="mb-1 opacity-75 small fw-bold text-uppercase ls-1">Pending Balance</p>
                        <h2 class="fw-bold mb-0">₹{{ number_format($stats['pending_balance'], 2) }}</h2>
                    </div>
                    <div class="avatar-text avatar-md bg-white bg-opacity-10 rounded-3">
                        <i class="feather-clock text-white fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-20" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: {{ $stats['total_earnings'] > 0 ? ($stats['pending_balance'] / $stats['total_earnings'] * 100) : 0 }}%;"></div>
                </div>
                <p class="text-white text-opacity-75 small mt-3 mb-0">Amount to be settled</p>
                <i class="feather-alert-circle position-absolute end-0 bottom-0 text-white opacity-5 mb-n3 me-n3" style="font-size: 8rem;"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start text-white mb-3">
                    <div>
                        <p class="mb-1 opacity-75 small fw-bold text-uppercase ls-1">Total Bookings</p>
                        <h2 class="fw-bold mb-0">{{ number_format($stats['total_invoices']) }}</h2>
                    </div>
                    <div class="avatar-text avatar-md bg-white bg-opacity-10 rounded-3">
                        <i class="feather-file-text text-white fs-4"></i>
                    </div>
                </div>
                <div class="progress bg-white bg-opacity-20" style="height: 4px;">
                    <div class="progress-bar bg-white" style="width: 100%;"></div>
                </div>
                <p class="text-white text-opacity-75 small mt-3 mb-0">Number of tests referred</p>
                <i class="feather-layers position-absolute end-0 bottom-0 text-white opacity-5 mb-n3 me-n3" style="font-size: 8rem;"></i>
            </div>
        </div>
    </div>

    {{-- Main Content Space --}}
    <div class="col-lg-8">
        {{-- Recent Invoices / Commissions --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 fw-bold"><i class="feather-file-text me-2 text-primary"></i>Recent Invoices</h5>
                <button class="btn btn-sm btn-light rounded-pill px-3 fs-11">View All</button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="fs-11 text-uppercase text-muted fw-bold">
                            <th class="ps-4 py-3">Invoice #</th>
                            <th class="py-3">Patient</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-end pe-4">Earnings (₹)</th>
                        </tr>
                    </thead>
                    <tbody class="fs-13">
                        @forelse($recentInvoices as $inv)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $inv->invoice_number }}</div>
                                    <span class="badge {{ $inv->payment_status == 'Paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} fs-10 rounded-pill">
                                        {{ $inv->payment_status }}
                                    </span>
                                </td>
                                <td>{{ $inv->patient->name ?? 'N/A' }}</td>
                                <td>{{ $inv->invoice_date->format('d M, Y') }}</td>
                                <td class="text-end pe-4 fw-bold text-primary">
                                    @php
                                        $amt = 0;
                                        if($role === 'Doctor') $amt = $inv->doctor_commission_amount;
                                        elseif($role === 'Agent') $amt = $inv->agent_commission_amount;
                                        else $amt = $inv->total_amount;
                                    @endphp
                                    ₹{{ number_format($amt, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="feather-file-minus fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">No recent activity recorded.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Recent Settlements --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 border border-light">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="feather-credit-card me-2 text-success"></i>Settlement History</h5>
            </div>
            <div class="list-group list-group-flush">
                @forelse($recentSettlements as $s)
                    <div class="list-group-item p-3 border-bottom-0 border-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted fs-11 fw-bold">{{ $s->payment_date->format('d M, Y') }}</span>
                            <span class="fw-bold text-success fs-15">₹{{ number_format($s->amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fs-12 text-dark fw-bold">
                                <i class="feather-check-circle text-success me-1"></i>Settled
                            </div>
                            <div class="fs-11 text-muted">{{ $s->payment_mode }}</div>
                        </div>
                        @if($s->reference_no)
                            <div class="mt-2 text-end">
                                <span class="fs-10 text-muted border px-2 py-1 rounded bg-light fw-bold ls-1 text-uppercase">REF: {{ $s->reference_no }}</span>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-5 text-center">
                        <i class="feather-info text-muted fs-3 mb-2"></i>
                        <p class="text-muted small mb-0 font-italic">No payments received yet.</p>
                    </div>
                @endforelse
            </div>
            @if(count($recentSettlements) > 0)
                <div class="card-footer bg-light border-0 text-center py-2">
                    <a href="#" class="btn btn-link py-0 fs-11 fw-bold text-decoration-none text-muted">View Full Statement</a>
                </div>
            @endif
        </div>

        {{-- Help Card --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-soft-primary border border-primary border-opacity-10">
            <div class="card-body p-4 text-center">
                <div class="avatar-text avatar-lg bg-primary rounded-circle mx-auto mb-3">
                    <i class="feather-help-circle text-white fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Need Assistance?</h6>
                <p class="text-muted small mb-4">Have questions about your earnings or payment schedule? Contact our account manager.</p>
                <a href="#" class="btn btn-primary w-100 rounded-pill fw-bold fs-12">Support Center</a>
            </div>
        </div>
    </div>
</div>

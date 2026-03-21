<div>
    <div class="page-header mb-4">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="m-b-10 fw-bold text-dark">Billing History</h5>
                <p class="fs-13 text-muted mb-0 font-medium">Comprehensive log of referred patient invoices.</p>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}" wire:navigate class="text-muted small">Home</a></li>
                <li class="breadcrumb-item text-primary fw-medium small">Invoices</li>
            </ul>
        </div>
        <div class="page-header-right">
            <div class="input-group shadow-sm border rounded-3 overflow-hidden bg-white" style="width: 250px;">
                <span class="input-group-text bg-white border-0 pe-1">
                    <i class="feather-search text-primary fs-12"></i>
                </span>
                <input type="text" wire:model.live.debounce.300ms="searchTerm" 
                    class="form-control border-0 shadow-none fs-12 fw-medium" 
                    placeholder="Search invoices...">
            </div>
        </div>
    </div>

    <div class="main-content">
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center justify-content-between px-4">
                <h6 class="mb-0 fw-bold text-dark"><i class="feather-file-text me-2 text-primary"></i>Billing History</h6>
                <div class="ms-auto d-flex gap-2">
                    <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1 fs-11 fw-bold">{{ $invoices->total() }} Invoices</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="min-height: 200px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-soft-light-gray fs-11 fw-bold text-uppercase text-muted border-bottom">
                            <tr>
                                <th class="ps-4 py-3 border-0">Invoice #</th>
                                <th class="py-3 border-0">Patient Name</th>
                                <th class="py-3 border-0">Date</th>
                                <th class="py-3 text-end border-0">Net Amount</th>
                                <th class="py-3 text-end border-0">Commission</th>
                                <th class="text-center pe-4 py-3 border-0">Payment</th>
                            </tr>
                        </thead>
                        <tbody class="fs-13">
                            @forelse($invoices as $inv)
                                <tr class="border-bottom border-light">
                                    <td class="ps-4 py-3">
                                        <div class="fw-bold text-dark fs-14">{{ $inv->invoice_number }}</div>
                                        <div class="fs-11 text-muted">{{ $inv->invoice_date->format('d M, Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-text bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-11" style="width: 28px; height: 28px;">
                                                {{ strtoupper(substr($inv->patient->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <div class="text-dark fw-medium">{{ $inv->patient->name ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fs-12 text-muted fw-bold text-uppercase ls-1">{{ $inv->invoice_date->format('d M, Y') }}</div>
                                    </td>
                                    <td class="text-end fw-medium text-dark">
                                        ₹{{ number_format($inv->total_amount, 2) }}
                                    </td>
                                    <td class="text-end fw-bold text-primary">
                                        @php
                                            $role = auth()->user()->role;
                                            $amt = 0;
                                            if($role === 'Doctor') $amt = $inv->doctor_commission_amount;
                                            elseif($role === 'Agent') $amt = $inv->agent_commission_amount;
                                            else $amt = $inv->total_amount;
                                        @endphp
                                        ₹{{ number_format($amt, 2) }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge {{ $inv->payment_status == 'Paid' ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }} rounded-pill px-3 py-1 fs-11 fw-bold">
                                            {{ $inv->payment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="feather-file-text" style="font-size: 3.5rem; opacity: 0.15;"></i></div>
                                        <h6 class="fw-bold text-dark">No Invoices Found</h6>
                                        <p class="text-muted fs-13 mb-0">No billing records found for the given search.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($invoices->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .font-medium { font-weight: 500; }
        .bg-soft-primary { background-color: rgba(59, 113, 202, 0.08) !important; }
        .bg-soft-light-gray { background-color: #f8f9fa !important; }
        .bg-soft-success { background-color: rgba(16, 185, 129, 0.08) !important; }
        .bg-soft-warning { background-color: rgba(245, 158, 11, 0.08) !important; }
        .text-primary { color: #3b71ca !important; }
        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        .stretch-full { height: 100%; }
    </style>
</div>

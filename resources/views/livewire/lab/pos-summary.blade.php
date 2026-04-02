<div>
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title">
                <h5 class="text-dark fw-bold">Bill Summary</h5>
            </div>
            <ul class="breadcrumb d-none d-md-flex ms-3">
                <li class="breadcrumb-item"><a href="{{ route('lab.dashboard') }}" wire:navigate class="text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lab.pos') }}" wire:navigate class="text-muted">POS</a></li>
                <li class="breadcrumb-item text-primary fw-medium">{{ $invoice->invoice_number }}</li>
            </ul>
        </div>
        <div class="page-header-right">
            <a href="{{ route('lab.pos') }}" wire:navigate class="btn btn-primary px-4 fw-bold shadow-sm">
                <i class="feather-plus me-2"></i>New Bill
            </a>
        </div>
    </div>

    <!-- Secondary Action Bar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between p-3 mb-4 rounded-4 shadow-sm bg-white border border-light" style="border-radius: 12px; background: var(--bs-card-bg);">
        <div class="d-flex align-items-center">
            <div class="dropdown me-2">
                <button class="btn btn-outline-primary fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="feather-printer me-2"></i>Print Options
                </button>
                <ul class="dropdown-menu shadow-sm border-0">
                    <li><a class="dropdown-item py-2" href="{{ route('lab.invoice.pdf', $invoice->id) }}" target="_blank"><i class="feather-file-text me-2 text-primary"></i>Receipt (With Letterhead)</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('lab.invoice.pdf.plain', $invoice->id) }}" target="_blank"><i class="feather-file me-2 text-primary"></i>Receipt (Without Letterhead)</a></li>
                    @if($invoice->patientMembership)
                        <li><a class="dropdown-item py-2 fw-bold text-success" href="{{ route('lab.membership.card.print', $invoice->patientMembership->id) }}" target="_blank"><i class="feather-credit-card me-2"></i>Print Membership Card</a></li>
                    @endif
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2" href="{{ route('lab.reports.print', $invoice->id) }}" target="_blank"><i class="feather-book-open me-2 text-primary"></i>Print Report</a></li>
                </ul>
            </div>
            <a href="{{ route('lab.invoice.barcode.stickers', $invoice->id) }}" target="_blank" class="btn btn-outline-dark fw-bold me-2">
                <i class="feather-code me-2"></i>Print Barcode
            </a>
            <button class="btn btn-outline-success fw-bold" disabled title="WhatsApp feature coming soon">
                <i class="bi bi-whatsapp me-2"></i>WhatsApp
            </button>
            @if($invoice->status === 'Completed')
                <a href="{{ route('lab.reports.print', $invoice->id) }}" target="_blank" class="btn btn-primary fw-bold ms-2 px-4 shadow-sm">
                    <i class="feather-printer me-2"></i>View / Print Report
                </a>
            @endif
        </div>
        @if(!auth()->user()->hasRole('collection_center'))
        <div>
            <a href="{{ route('lab.reports.entry', $invoice->id) }}" wire:navigate class="btn btn-warning px-4 fw-bold text-dark shadow-sm">
                <i class="feather-edit-3 me-2"></i>Enter Results
            </a>
        </div>
        @endif
    </div>

    <!-- Main Content -->
    <div class="main-content mt-4">
        <div class="row g-4">
            
            <!-- Patient & Invoice Details Card -->
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header py-3 bg-light border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-file-text text-primary me-2"></i>Invoice Details (#{{ $invoice->invoice_number }})</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <!-- Patient Info -->
                            <div class="col-md-6">
                                <h6 class="fs-11 fw-bold text-muted text-uppercase mb-3">Patient Information</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar-text bg-soft-primary text-primary rounded-circle me-3">
                                        {{ strtoupper(substr($invoice->patient->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $invoice->patient->name ?? 'Walk-in Patient' }} <span class="badge bg-soft-info text-info ms-1">{{ $invoice->patient->formatted_id ?? '' }}</span></h6>
                                        <div class="text-muted fs-12">
                                            {{ $invoice->patient->patientProfile->age ?? 'N/A' }} YRS / {{ $invoice->patient->patientProfile->gender ?? 'N/A' }} 
                                            | 📞 {{ $invoice->patient->phone ?? 'No Phone' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Referral Info -->
                            <div class="col-md-6">
                                <h6 class="fs-11 fw-bold text-muted text-uppercase mb-3">Referring Details</h6>
                                @if($invoice->doctor)
                                    <div class="mb-2">
                                        <span class="fs-12 text-muted me-2">Doctor:</span>
                                        <span class="fw-bold text-dark fs-13">{{ $invoice->doctor->name }}</span>
                                    </div>
                                @else
                                    <div class="mb-2">
                                        <span class="fs-12 text-muted fw-bold">Self Walk-in</span>
                                    </div>
                                @endif
                                
                                <div class="mb-2">
                                    <span class="fs-12 text-muted me-2">Date:</span>
                                    <span class="text-dark fs-13">{{ $invoice->invoice_date->format('d M Y, h:i A') }}</span>
                                </div>
                                <div>
                                    <span class="fs-12 text-muted me-2">Branch:</span>
                                    <span class="text-dark fs-13">{{ $invoice->branch->name ?? 'Main Branch' }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 opacity-50">

                        <!-- Ordered Tests Table -->
                        <h6 class="fs-11 fw-bold text-muted text-uppercase mb-3">Tests Ordered</h6>
                        <div class="table-responsive border rounded-3">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light fs-11 text-uppercase text-muted fw-bold">
                                    <tr>
                                        <th class="ps-3 py-3">Service Code</th>
                                        <th>Test / Package Name</th>
                                        <th class="text-end pe-3">Amount (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoice->items as $item)
                                        <tr class="border-bottom border-light">
                                            <td class="ps-3">
                                                <span class="badge bg-soft-primary text-primary">{{ $item->test_code ?? 'TEST' }}</span>
                                            </td>
                                            <td class="text-dark fw-medium">
                                                {{ $item->test_name }}
                                                @if($item->is_package)
                                                    <span class="badge bg-soft-info text-info ms-2 fs-10">Package</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 text-dark fw-bold">
                                                {{ number_format($item->price, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">No tests found on this invoice.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Card -->
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header py-3 bg-light border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark"><i class="feather-credit-card text-primary me-2"></i>Payment Summary</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Subtotal</span>
                            <span class="text-dark fw-bold fs-13">₹{{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        
                        @if($invoice->discount_amount > 0 || $invoice->membership_discount_amount > 0 || $invoice->voucher_discount_amount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span class="fs-13">Total Discount</span>
                                <span class="fw-bold fs-13">- ₹{{ number_format($invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount, 2) }}</span>
                            </div>
                            @if($invoice->membership)
                                <div class="text-end mb-2 mt-n1">
                                    <small class="text-success fs-10 fw-bold">Applied: {{ $invoice->membership->name }} ({{ $invoice->membership->discount_percentage }}%)</small>
                                </div>
                            @endif
                        @endif

                        <hr class="my-3 opacity-50">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-dark fw-bold text-uppercase fs-12">Net Payable</span>
                            <span class="text-primary fw-bolder fs-16">₹{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted fs-13">Amount Paid</span>
                            <span class="text-success fw-bold fs-13">₹{{ number_format($invoice->paid_amount, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between p-3 rounded-3 mt-3 {{ $invoice->due_amount > 0 ? 'bg-soft-danger' : 'bg-soft-success' }}">
                            <span class="fw-bold text-dark text-uppercase fs-11">{{ $invoice->due_amount > 0 ? 'Balance Due' : 'Status' }}</span>
                            <span class="fw-bolder {{ $invoice->due_amount > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $invoice->due_amount > 0 ? '₹' . number_format($invoice->due_amount, 2) : 'Fully Paid' }}
                            </span>
                        </div>

                        <!-- Edit Invoice Button -->
                        <div class="mt-4">
                            <a href="{{ route('lab.invoice.edit', $invoice->id) }}" wire:navigate class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm">
                                <i class="feather-edit me-2"></i>Edit Amount / Invoice
                            </a>
                        </div>
                        
                    </div>
                </div>

                <!-- Payment History -->
                @if($invoice->payments->count() > 0)
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header py-3 bg-light border-bottom">
                            <h6 class="card-title mb-0 fw-bold text-dark fs-12 text-uppercase">Payment History</h6>
                        </div>
                        <div class="card-body p-3">
                            @foreach($invoice->payments as $payment)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-bold text-dark fs-13">{{ $payment->paymentMode->name ?? 'Cash' }}</div>
                                        <div class="text-muted fs-11">{{ $payment->created_at->format('d M, h:i A') }}</div>
                                    </div>
                                    <div class="fw-bold text-success fs-13">₹{{ number_format($payment->amount, 2) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title"><h5 class="m-b-10">Invoice #{{ $invoice->invoice_number }}</h5></div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Invoice</li>
                <li class="breadcrumb-item">Print</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <button onclick="window.print()" class="btn btn-primary"><i class="feather-printer me-1"></i>Print</button>
        </div>
    </div>

    <div class="main-content">
        <div class="card" id="printArea">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="row mb-4 pb-3 border-bottom">
                    <div class="col-6">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" style="max-height:60px;" class="mb-2">
                        @endif
                        <h4 class="fw-bold mb-1">{{ $company->name }}</h4>
                        @if($company->tagline)<p class="fs-11 text-muted mb-1">{{ $company->tagline }}</p>@endif
                        <p class="fs-11 text-muted mb-0">{{ $company->address }}</p>
                        <p class="fs-11 text-muted mb-0">
                            @if($company->phone)📞 {{ $company->phone }}@endif
                            @if($company->email) · ✉ {{ $company->email }}@endif
                        </p>
                        @if($company->gst_number)<p class="fs-11 text-muted mb-0">GST: {{ $company->gst_number }}</p>@endif
                    </div>
                    <div class="col-6 text-end">
                        <h5 class="fw-bold text-primary mb-2">INVOICE</h5>
                        <table class="ms-auto fs-11">
                            <tr><td class="pe-3 text-muted">Invoice #</td><td class="fw-bold">{{ $invoice->invoice_number }}</td></tr>
                            <tr><td class="pe-3 text-muted">Date</td><td class="fw-bold">{{ $invoice->invoice_date->format('d M Y, h:i A') }}</td></tr>
                            <tr><td class="pe-3 text-muted">Barcode</td><td class="fw-bold">{{ $invoice->barcode }}</td></tr>
                            <tr><td class="pe-3 text-muted">Status</td><td><span class="badge {{ $invoice->payment_status === 'Paid' ? 'bg-success' : ($invoice->payment_status === 'Partial' ? 'bg-warning' : 'bg-danger') }}">{{ $invoice->payment_status }}</span></td></tr>
                        </table>
                    </div>
                </div>

                {{-- Patient & Doctor Info --}}
                <div class="row mb-4">
                    <div class="col-6">
                        <h6 class="fw-bold text-uppercase fs-10 text-muted mb-2">Patient Details</h6>
                        <div class="fw-bold fs-13">{{ $invoice->patient->name ?? 'N/A' }}</div>
                        <div class="fs-11 text-muted">📞 {{ $invoice->patient->phone ?? '' }}</div>
                        @if($invoice->patient->patientProfile)
                            <div class="fs-11 text-muted">
                                {{ $invoice->patient->patientProfile->patient_id_string ?? '' }}
                                · {{ $invoice->patient->patientProfile->age ?? '' }} {{ $invoice->patient->patientProfile->age_type ?? 'Yrs' }}
                                · {{ $invoice->patient->patientProfile->gender ?? '' }}
                            </div>
                        @endif
                    </div>
                    <div class="col-6">
                        @if($invoice->doctor)
                            <h6 class="fw-bold text-uppercase fs-10 text-muted mb-2">Referred By</h6>
                            <div class="fw-bold fs-13">{{ $invoice->doctor->name }}</div>
                            @if($invoice->doctor->doctorProfile)
                                <div class="fs-11 text-muted">{{ $invoice->doctor->doctorProfile->specialization ?? '' }}</div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Items Table --}}
                <table class="table table-bordered mb-3 fs-12">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Test / Package</th>
                            <th class="text-center" style="width:80px">Type</th>
                            <th class="text-end" style="width:100px">Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-bold">{{ $item->test_name }}</td>
                                <td class="text-center">{{ $item->is_package ? 'Package' : 'Test' }}</td>
                                <td class="text-end fw-bold">₹{{ number_format($item->mrp, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Totals --}}
                <div class="row">
                    <div class="col-6">
                        @if($invoice->collectionCenter)
                            <div class="fs-11 text-muted"><strong>Collection Center:</strong> {{ $invoice->collectionCenter->name }}</div>
                        @endif
                        <div class="fs-11 text-muted"><strong>Collection Type:</strong> {{ $invoice->collection_type }}</div>
                    </div>
                    <div class="col-6">
                        <table class="ms-auto fs-12" style="min-width:250px;">
                            <tr><td class="pe-3 text-muted">Subtotal</td><td class="text-end fw-bold">₹{{ number_format($invoice->subtotal, 2) }}</td></tr>
                            @if($invoice->membership_discount_amount > 0)
                                <tr><td class="pe-3 text-muted">Membership Discount</td><td class="text-end" style="color:#198754;">- ₹{{ number_format($invoice->membership_discount_amount, 2) }}</td></tr>
                            @endif
                            @if($invoice->voucher_discount_amount > 0)
                                <tr><td class="pe-3 text-muted">Voucher Discount</td><td class="text-end" style="color:#198754;">- ₹{{ number_format($invoice->voucher_discount_amount, 2) }}</td></tr>
                            @endif
                            @if($invoice->discount_amount > 0)
                                <tr><td class="pe-3 text-muted">Manual Discount</td><td class="text-end" style="color:#198754;">- ₹{{ number_format($invoice->discount_amount, 2) }}</td></tr>
                            @endif
                            <tr class="border-top"><td class="pe-3 fw-bold fs-14 pt-2">Total</td><td class="text-end fw-bold fs-14 pt-2 text-primary">₹{{ number_format($invoice->total_amount, 2) }}</td></tr>
                            <tr><td class="pe-3 text-muted">Paid</td><td class="text-end fw-bold" style="color:#198754;">₹{{ number_format($invoice->paid_amount, 2) }}</td></tr>
                            @if($invoice->due_amount > 0)
                                <tr><td class="pe-3 text-muted">Due</td><td class="text-end fw-bold text-danger">₹{{ number_format($invoice->due_amount, 2) }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                {{-- Payment Details --}}
                @if($invoice->payments->count() > 0)
                    <hr>
                    <h6 class="fw-bold fs-11 text-muted text-uppercase mb-2">Payment Details</h6>
                    <table class="table table-sm fs-11 mb-0">
                        <tr class="text-muted"><th>Mode</th><th>Amount</th><th>Transaction ID</th></tr>
                        @foreach($invoice->payments as $p)
                            <tr><td>{{ $p->paymentMode->name ?? 'N/A' }}</td><td class="fw-bold">₹{{ number_format($p->amount, 2) }}</td><td>{{ $p->transaction_id ?? '—' }}</td></tr>
                        @endforeach
                    </table>
                @endif

                {{-- Footer --}}
                <hr>
                <div class="text-center fs-10 text-muted">
                    <p class="mb-1">This is a computer-generated invoice. No signature required.</p>
                    @if($company->website)<p class="mb-0">🌐 {{ $company->website }}</p>@endif
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .page-header, .nxl-navigation, .nxl-header, .customizer-toggle, .btn { display: none !important; }
            .nxl-container { padding: 0 !important; margin: 0 !important; }
            .main-content { padding: 0 !important; }
            #printArea { box-shadow: none !important; border: none !important; }
        }
    </style>
</div>

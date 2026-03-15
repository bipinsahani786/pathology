<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title"><h5 class="m-b-10">Invoice #{{ $invoice->invoice_number }}</h5></div>
        </div>
        <div class="page-header-right ms-auto">
            <button onclick="window.print()" class="btn btn-primary"><i class="feather-printer me-1"></i>Print</button>
        </div>
    </div>

    <div class="main-content">
        <div class="card border-0" id="printArea">
            <div class="card-body p-5">

                {{-- Modern Header with gradient accent --}}
                <div class="d-flex justify-content-between align-items-start mb-4 pb-4" style="border-bottom:3px solid #3b71ca;">
                    <div>
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" style="max-height:50px;" class="mb-2">
                        @endif
                        <h3 class="fw-bold mb-0" style="color:#1a1a2e;">{{ $company->name }}</h3>
                        @if($company->tagline)<p class="text-muted fs-12 mb-0">{{ $company->tagline }}</p>@endif
                    </div>
                    <div class="text-end">
                        <div class="px-4 py-2 rounded-3" style="background:linear-gradient(135deg,#3b71ca,#1a47a0);">
                            <div class="text-white fw-bold fs-16">INVOICE</div>
                            <div class="text-white fs-11" style="opacity:0.8;">#{{ $invoice->invoice_number }}</div>
                        </div>
                    </div>
                </div>

                {{-- Info Cards Row --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background:rgba(59,113,202,0.05);">
                            <div class="fs-10 fw-bold text-uppercase text-muted mb-2"><i class="feather-user me-1"></i>Patient</div>
                            <div class="fw-bold fs-13">{{ $invoice->patient->name ?? 'N/A' }}</div>
                            <div class="fs-11 text-muted">{{ $invoice->patient->phone ?? '' }}</div>
                            @if($invoice->patient->patientProfile)
                                <div class="fs-11 text-muted">{{ $invoice->patient->patientProfile->age ?? '' }}{{ $invoice->patient->patientProfile->age_type == 'Years' ? 'Y' : 'M' }}/{{ substr($invoice->patient->patientProfile->gender ?? '', 0, 1) }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background:rgba(25,135,84,0.05);">
                            <div class="fs-10 fw-bold text-uppercase text-muted mb-2"><i class="feather-calendar me-1"></i>Details</div>
                            <div class="fs-11"><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</div>
                            <div class="fs-11"><strong>Time:</strong> {{ $invoice->invoice_date->format('h:i A') }}</div>
                            <div class="fs-11"><strong>Barcode:</strong> {{ $invoice->barcode }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3 h-100" style="background:rgba(220,53,69,0.05);">
                            <div class="fs-10 fw-bold text-uppercase text-muted mb-2"><i class="feather-credit-card me-1"></i>Payment Status</div>
                            <div class="fs-2 fw-bold {{ $invoice->payment_status === 'Paid' ? 'text-success' : 'text-danger' }}">{{ $invoice->payment_status }}</div>
                        </div>
                    </div>
                </div>

                {{-- Items --}}
                <div class="table-responsive mb-4">
                    <table class="table mb-0 fs-12">
                        <thead>
                            <tr style="background:#1a1a2e;color:#fff;">
                                <th class="ps-3" style="width:40px;">#</th>
                                <th>Test / Package</th>
                                <th class="text-center" style="width:80px;">Type</th>
                                <th class="text-end pe-3" style="width:100px;">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $i => $item)
                                <tr class="{{ $i % 2 ? '' : 'bg-light' }}">
                                    <td class="ps-3">{{ $i + 1 }}</td>
                                    <td class="fw-bold">{{ $item->test_name }}</td>
                                    <td class="text-center"><span class="badge {{ $item->is_package ? 'bg-primary' : 'bg-success' }} rounded-pill fs-10">{{ $item->is_package ? 'PKG' : 'Test' }}</span></td>
                                    <td class="text-end pe-3 fw-bold">₹{{ number_format($item->mrp, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="row">
                    <div class="col-md-6">
                        @if($invoice->doctor)
                            <div class="fs-11"><strong>Referred By:</strong> {{ $invoice->doctor->name }}</div>
                        @endif
                        @if($invoice->collectionCenter)
                            <div class="fs-11"><strong>Collection Center:</strong> {{ $invoice->collectionCenter->name }}</div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3" style="background:#f8f9fa;">
                            <div class="d-flex justify-content-between fs-12 mb-1"><span>Subtotal</span><span class="fw-bold">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
                            @if($invoice->membership_discount_amount > 0)
                                <div class="d-flex justify-content-between fs-12 mb-1"><span>Membership Discount</span><span style="color:#198754;">- ₹{{ number_format($invoice->membership_discount_amount, 2) }}</span></div>
                            @endif
                            @if($invoice->voucher_discount_amount > 0)
                                <div class="d-flex justify-content-between fs-12 mb-1"><span>Voucher Discount</span><span style="color:#198754;">- ₹{{ number_format($invoice->voucher_discount_amount, 2) }}</span></div>
                            @endif
                            @if($invoice->discount_amount > 0)
                                <div class="d-flex justify-content-between fs-12 mb-1"><span>Manual Discount</span><span style="color:#198754;">- ₹{{ number_format($invoice->discount_amount, 2) }}</span></div>
                            @endif
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fs-14 fw-bold text-primary"><span>NET TOTAL</span><span>₹{{ number_format($invoice->total_amount, 2) }}</span></div>
                            <div class="d-flex justify-content-between fs-12 mt-1"><span>Paid</span><span class="fw-bold" style="color:#198754;">₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
                            @if($invoice->due_amount > 0)
                                <div class="d-flex justify-content-between fs-12"><span>Due</span><span class="fw-bold text-danger">₹{{ number_format($invoice->due_amount, 2) }}</span></div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="text-center mt-4 pt-3 border-top fs-10 text-muted">
                    <p class="mb-0">Thank you for choosing {{ $company->name }}! @if($company->website)· 🌐 {{ $company->website }}@endif</p>
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

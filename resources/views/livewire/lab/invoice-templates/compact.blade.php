<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title"><h5 class="m-b-10">Invoice #{{ $invoice->invoice_number }}</h5></div>
        </div>
        <div class="page-header-right ms-auto">
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="feather-printer me-1"></i>Print</button>
        </div>
    </div>

    <div class="main-content">
        <div class="card" id="printArea">
            <div class="card-body p-3">

                {{-- Compact Header --}}
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        @if($company->logo)
                            <img src="{{ asset('storage/' . $company->logo) }}" alt="" style="max-height:35px;">
                        @endif
                        <div>
                            <strong class="fs-13">{{ $company->name }}</strong>
                            <div class="fs-10 text-muted">{{ $company->phone ?? '' }} · {{ $company->email ?? '' }}</div>
                        </div>
                    </div>
                    <div class="text-end fs-10">
                        <strong>#{{ $invoice->invoice_number }}</strong> · {{ $invoice->invoice_date->format('d/m/Y h:iA') }}
                        <br><span class="badge {{ $invoice->payment_status === 'Paid' ? 'bg-success' : 'bg-danger' }} fs-9">{{ $invoice->payment_status }}</span>
                    </div>
                </div>

                {{-- Patient Row --}}
                <div class="d-flex justify-content-between fs-11 mb-2">
                    <div>
                        <strong>Patient:</strong> {{ $invoice->patient->name ?? 'N/A' }}
                        ({{ $invoice->patient->phone ?? '' }})
                        @if($invoice->patient->patientProfile)
                            · {{ $invoice->patient->patientProfile->age ?? '' }}{{ $invoice->patient->patientProfile->age_type == 'Years' ? 'Y' : 'M' }}/{{ substr($invoice->patient->patientProfile->gender ?? '', 0, 1) }}
                        @endif
                    </div>
                    @if($invoice->doctor)
                        <div><strong>Ref:</strong> {{ $invoice->doctor->name }}</div>
                    @endif
                </div>

                {{-- Items --}}
                <table class="table table-sm table-bordered mb-2 fs-11">
                    <thead class="table-light">
                        <tr><th style="width:30px">#</th><th>Test</th><th class="text-end" style="width:80px">₹</th></tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $i => $item)
                            <tr><td>{{ $i + 1 }}</td><td>{{ $item->test_name }} @if($item->is_package)<span class="badge bg-primary fs-9">PKG</span>@endif</td><td class="text-end fw-bold">{{ number_format($item->mrp, 0) }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light"><td colspan="2" class="text-end fw-bold">Subtotal</td><td class="text-end fw-bold">₹{{ number_format($invoice->subtotal, 0) }}</td></tr>
                        @if($invoice->membership_discount_amount + $invoice->voucher_discount_amount + $invoice->discount_amount > 0)
                            <tr><td colspan="2" class="text-end text-muted">Discount</td><td class="text-end" style="color:#198754;">- ₹{{ number_format($invoice->membership_discount_amount + $invoice->voucher_discount_amount + $invoice->discount_amount, 0) }}</td></tr>
                        @endif
                        <tr style="background:rgba(59,113,202,0.08);"><td colspan="2" class="text-end fw-bold fs-13">TOTAL</td><td class="text-end fw-bold fs-13 text-primary">₹{{ number_format($invoice->total_amount, 0) }}</td></tr>
                        <tr><td colspan="2" class="text-end">Paid</td><td class="text-end fw-bold" style="color:#198754;">₹{{ number_format($invoice->paid_amount, 0) }}</td></tr>
                        @if($invoice->due_amount > 0)
                            <tr><td colspan="2" class="text-end">Due</td><td class="text-end fw-bold text-danger">₹{{ number_format($invoice->due_amount, 0) }}</td></tr>
                        @endif
                    </tfoot>
                </table>

                <div class="text-center fs-9 text-muted border-top pt-1">Computer Generated · {{ $company->name }} @if($company->gst_number)· GST: {{ $company->gst_number }}@endif</div>
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

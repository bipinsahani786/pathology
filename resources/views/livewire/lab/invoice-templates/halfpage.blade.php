<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title"><h5 class="m-b-10">Invoice #{{ $invoice->invoice_number }}</h5></div>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item">Invoice</li>
                <li class="breadcrumb-item">Print (Half Page)</li>
            </ul>
        </div>
        <div class="page-header-right ms-auto">
            <button onclick="window.print()" class="btn btn-primary"><i class="feather-printer me-1"></i>Print</button>
        </div>
    </div>

    <div class="main-content">
        <div class="card" id="printArea" style="max-height: 140mm; overflow: hidden; margin-bottom: 20px;">
            <div class="card-body p-2 p-md-4">
                <div class="border border-dark p-3">
                    {{-- Header --}}
                    <div class="row mb-2 border-bottom border-dark pb-2 mx-0">
                        <div class="col-6 px-0">
                            @if($company->logo)
                                <img src="{{ secure_storage_url($company->logo) }}" alt="Logo" style="max-height:50px;" class="mb-2">
                            @endif
                            <h5 class="fw-bold mb-1 text-dark text-uppercase">{{ $company->name }}</h5>
                            @if($company->tagline)<p class="fs-10 text-dark mb-1">{{ $company->tagline }}</p>@endif
                            <p class="fs-10 text-dark mb-0">{{ $company->address }}</p>
                            <p class="fs-10 text-dark mb-0">
                                @if($company->phone)📞 {{ $company->phone }}@endif
                                @if($company->email) · ✉ {{ $company->email }}@endif
                            </p>
                            @if($company->gst_number)<p class="fs-10 text-dark mb-0">GST: {{ $company->gst_number }}</p>@endif
                        </div>
                        <div class="col-6 px-0 text-end">
                            <h6 class="fw-bold text-dark mb-2 text-uppercase" style="letter-spacing: 1px;">BILL CUM RECEIPT</h6>
                            <table class="ms-auto fs-10 text-dark">
                                <tr><td class="pe-2 text-end">Invoice # :</td><td class="fw-bold text-start">{{ $invoice->invoice_number }}</td></tr>
                                <tr><td class="pe-2 text-end">Date :</td><td class="fw-bold text-start">{{ $invoice->invoice_date->format('d M Y, h:i A') }}</td></tr>
                            </table>
                        </div>
                    </div>

                    {{-- Patient Information Grid --}}
                    <table class="table table-bordered border-dark table-sm fs-10 mb-2 text-dark">
                        <tr>
                            <td class="fw-bold" style="width: 15%;">Patient Name</td>
                            <td class="fw-bold text-uppercase" style="width: 35%;">{{ $invoice->patient->name ?? 'N/A' }}</td>
                            <td class="fw-bold" style="width: 15%;">Age / Gender</td>
                            <td class="fw-bold text-uppercase" style="width: 35%;">
                                {{ $invoice->patient->patientProfile->age ?? '-' }} {{ $invoice->patient->patientProfile->age_type ?? 'Yrs' }} / {{ $invoice->patient->patientProfile->gender ?? '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Patient ID</td>
                            <td class="fw-bold">{{ $invoice->patient->patientProfile->patient_id_string ?? 'N/A' }}</td>
                            <td class="fw-bold">Contact No</td>
                            <td class="fw-bold">{{ $invoice->patient->phone ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Referred By</td>
                            <td class="fw-bold">{{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
                            <td class="fw-bold">Center</td>
                            <td class="fw-bold">{{ $invoice->collectionCenter ? $invoice->collectionCenter->name : 'Main Center' }}</td>
                        </tr>
                    </table>

                    {{-- Items Table --}}
                    <table class="table table-sm table-bordered border-dark mb-2 fs-10 text-dark">
                        <thead>
                            <tr class="border-dark">
                                <th style="width:30px; padding: 4px;" class="border-dark text-dark">#</th>
                                <th style="padding: 4px;" class="border-dark text-dark">Investigation Description</th>
                                <th class="text-end" style="width:80px; padding: 4px;" class="border-dark text-dark">Amount (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $i => $item)
                                <tr class="border-dark">
                                    <td style="padding: 4px;" class="border-dark text-dark">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td class="fw-bold border-dark text-dark text-uppercase" style="padding: 4px;">{{ $item->test_name }}</td>
                                    <td class="text-end fw-bold border-dark text-dark" style="padding: 4px;">{{ number_format($item->mrp, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Totals --}}
                    <div class="row mb-1 mx-0">
                        <div class="col-7 px-0">
                            <div class="border border-dark p-2 mb-2 fs-10 text-dark">
                                <strong>Received Amount (in words):</strong><br>
                                Rupees {{ getIndianCurrency($invoice->paid_amount) }} Only
                            </div>
                            <div>
                                @if($invoice->payment_status === 'Paid')
                                    <span class="border border-dark fs-9 px-2 py-1 fw-bold text-dark">FULLY PAID</span>
                                @elseif($invoice->payment_status === 'Partial')
                                    <span class="border border-dark fs-9 px-2 py-1 fw-bold text-dark">PARTIAL PAID</span>
                                @else
                                    <span class="border border-dark fs-9 px-2 py-1 fw-bold text-dark">UNPAID</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-5 px-0">
                            <table class="table table-sm table-bordered border-dark fs-10 text-dark mb-0">
                                <tr><td class="px-2 py-1 border-dark">Subtotal</td><td class="text-end fw-bold border-dark px-2 py-1">{{ number_format($invoice->subtotal, 2) }}</td></tr>
                                @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
                                @if($totalDisc > 0)
                                    <tr><td class="px-2 py-1 border-dark">Discount</td><td class="text-end fw-bold border-dark px-2 py-1">- {{ number_format($totalDisc, 2) }}</td></tr>
                                @endif
                                <tr><td class="px-2 py-1 fw-bold border-dark text-uppercase">Net Payable</td><td class="text-end fw-bold border-dark px-2 py-1">{{ number_format($invoice->total_amount, 2) }}</td></tr>
                                <tr><td class="px-2 py-1 border-dark">Paid</td><td class="text-end fw-bold border-dark px-2 py-1">{{ number_format($invoice->paid_amount, 2) }}</td></tr>
                                @if($invoice->due_amount > 0)
                                    <tr><td class="px-2 py-1 fw-bold border-dark text-uppercase">Balance Due</td><td class="text-end fw-bold border-dark px-2 py-1">{{ number_format($invoice->due_amount, 2) }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="text-center fs-9 text-dark mt-2 border-top border-dark pt-2">
                        This is a computer-generated invoice and does not require a physical signature.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 5mm;
            }
            .page-header, .nxl-navigation, .nxl-header, .customizer-toggle, .btn { display: none !important; }
            .nxl-container { padding: 0 !important; margin: 0 !important; }
            .main-content { padding: 0 !important; }
            #printArea { max-height: 140mm !important; overflow: hidden !important; border: none !important; box-shadow: none !important; }
            .border-dark { border-color: #000 !important; }
            .text-dark { color: #000 !important; }
        }
    </style>
</div>

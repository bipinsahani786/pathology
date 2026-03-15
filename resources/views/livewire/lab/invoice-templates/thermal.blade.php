<div>
    <div class="page-header">
        <div class="page-header-left d-flex align-items-center">
            <div class="page-header-title"><h5 class="m-b-10">Receipt #{{ $invoice->invoice_number }}</h5></div>
        </div>
        <div class="page-header-right ms-auto">
            <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="feather-printer me-1"></i>Print</button>
        </div>
    </div>

    <div class="main-content d-flex justify-content-center">
        <div class="card" id="printArea" style="max-width:320px;width:100%;font-family:'Courier New', monospace;">
            <div class="card-body p-3">

                {{-- Thermal Header --}}
                <div class="text-center mb-2 pb-2" style="border-bottom:1px dashed #999;">
                    <strong class="fs-14 d-block">{{ strtoupper($company->name) }}</strong>
                    @if($company->tagline)<div class="fs-10">{{ $company->tagline }}</div>@endif
                    <div class="fs-10">{{ $company->address ?? '' }}</div>
                    <div class="fs-10">{{ $company->phone ?? '' }}</div>
                    @if($company->gst_number)<div class="fs-10">GST: {{ $company->gst_number }}</div>@endif
                </div>

                {{-- Invoice Info --}}
                <div class="fs-10 mb-2 pb-2" style="border-bottom:1px dashed #999;">
                    <div class="d-flex justify-content-between"><span>Bill #:</span><strong>{{ $invoice->invoice_number }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Date:</span><span>{{ $invoice->invoice_date->format('d/m/Y H:i') }}</span></div>
                    <div class="d-flex justify-content-between"><span>Patient:</span><strong>{{ Str::limit($invoice->patient->name ?? 'N/A', 18) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Phone:</span><span>{{ $invoice->patient->phone ?? '' }}</span></div>
                    @if($invoice->doctor)
                        <div class="d-flex justify-content-between"><span>Ref:</span><span>{{ Str::limit($invoice->doctor->name, 18) }}</span></div>
                    @endif
                </div>

                {{-- Items --}}
                <div class="fs-10 mb-2 pb-2" style="border-bottom:1px dashed #999;">
                    @foreach($invoice->items as $i => $item)
                        <div class="d-flex justify-content-between">
                            <span>{{ $i + 1 }}. {{ Str::limit($item->test_name, 22) }}</span>
                            <strong>{{ number_format($item->mrp, 0) }}</strong>
                        </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="fs-10 mb-2 pb-2" style="border-bottom:1px dashed #999;">
                    <div class="d-flex justify-content-between"><span>Subtotal:</span><strong>₹{{ number_format($invoice->subtotal, 0) }}</strong></div>
                    @php $totalDisc = $invoice->membership_discount_amount + $invoice->voucher_discount_amount + $invoice->discount_amount; @endphp
                    @if($totalDisc > 0)
                        <div class="d-flex justify-content-between"><span>Discount:</span><span>-₹{{ number_format($totalDisc, 0) }}</span></div>
                    @endif
                    <div class="d-flex justify-content-between fw-bold fs-13 mt-1 pt-1" style="border-top:1px solid #333;">
                        <span>TOTAL:</span><span>₹{{ number_format($invoice->total_amount, 0) }}</span>
                    </div>
                    <div class="d-flex justify-content-between"><span>Paid:</span><strong>₹{{ number_format($invoice->paid_amount, 0) }}</strong></div>
                    @if($invoice->due_amount > 0)
                        <div class="d-flex justify-content-between fw-bold"><span>DUE:</span><span>₹{{ number_format($invoice->due_amount, 0) }}</span></div>
                    @endif
                </div>

                {{-- Payment Modes --}}
                @if($invoice->payments->count() > 0)
                    <div class="fs-9 mb-2 pb-2" style="border-bottom:1px dashed #999;">
                        @foreach($invoice->payments as $p)
                            <div class="d-flex justify-content-between"><span>{{ $p->paymentMode->name ?? 'N/A' }}</span><span>₹{{ number_format($p->amount, 0) }}</span></div>
                        @endforeach
                    </div>
                @endif

                {{-- Footer --}}
                <div class="text-center fs-9">
                    <div>*** THANK YOU ***</div>
                    <div class="mt-1">{{ $invoice->barcode }}</div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            .page-header, .nxl-navigation, .nxl-header, .customizer-toggle, .btn { display: none !important; }
            .nxl-container { padding: 0 !important; margin: 0 !important; }
            .main-content { padding: 0 !important; }
            #printArea { box-shadow: none !important; border: none !important; max-width: 80mm !important; }
        }
    </style>
</div>

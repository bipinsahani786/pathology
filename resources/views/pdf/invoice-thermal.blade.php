<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', 'Courier New', monospace; font-size: 10px; color: #333; }
        .container { padding: 10px 15px; max-width: 300px; margin: 0 auto; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .divider { border-top: 1px dashed #999; margin: 6px 0; }
        .row { overflow: hidden; padding: 1px 0; }
        .row .left { float: left; }
        .row .right { float: right; font-weight: bold; }
        .grand { font-size: 13px; border-top: 1px solid #333; margin-top: 4px; padding-top: 4px; }
        .items td { padding: 2px 0; }
        .items { width: 100%; }
    </style>
</head>
<body>
<div class="container">

    @if($showHeader)
        @if($headerImage)
            <div class="center" style="margin-bottom:6px;"><img src="{{ public_path('storage/' . $headerImage) }}" style="max-width:280px;max-height:60px;"></div>
        @else
            <div class="center" style="margin-bottom:4px;">
                <div class="bold" style="font-size:14px;">{{ strtoupper($company->name) }}</div>
                @if($company->tagline)<div style="font-size:8px;">{{ $company->tagline }}</div>@endif
                <div style="font-size:8px;">{{ $company->address ?? '' }}</div>
                <div style="font-size:8px;">{{ $company->phone ?? '' }} @if($company->gst_number)· GST: {{ $company->gst_number }}@endif</div>
            </div>
        @endif
    @endif

    <div class="divider"></div>

    <div style="font-size:9px;">
        <div class="row"><span class="left">Bill #:</span><span class="right">{{ $invoice->invoice_number }}</span></div>
        <div class="row"><span class="left">Date:</span><span class="right">{{ $invoice->invoice_date->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span class="left">Patient:</span><span class="right">{{ Str::limit($invoice->patient->name ?? 'N/A', 18) }}</span></div>
        <div class="row"><span class="left">Phone:</span><span class="right">{{ $invoice->patient->phone ?? '' }}</span></div>
        @if($invoice->doctor)
            <div class="row"><span class="left">Ref:</span><span class="right">{{ Str::limit($invoice->doctor->name, 18) }}</span></div>
        @endif
    </div>

    <div class="divider"></div>

    <table class="items">
        @foreach($invoice->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}. {{ Str::limit($item->test_name, 22) }}</td>
                <td style="text-align:right;font-weight:bold;">{{ number_format($item->mrp, 0) }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div style="font-size:9px;">
        <div class="row"><span class="left">Subtotal:</span><span class="right">₹{{ number_format($invoice->subtotal, 0) }}</span></div>
        @php $totalDisc = $invoice->membership_discount_amount + $invoice->voucher_discount_amount + $invoice->discount_amount; @endphp
        @if($totalDisc > 0)
            <div class="row"><span class="left">Discount:</span><span class="right">-₹{{ number_format($totalDisc, 0) }}</span></div>
        @endif
        <div class="row grand"><span class="left bold">TOTAL:</span><span class="right">₹{{ number_format($invoice->total_amount, 0) }}</span></div>
        <div class="row"><span class="left">Paid:</span><span class="right">₹{{ number_format($invoice->paid_amount, 0) }}</span></div>
        @if($invoice->due_amount > 0)
            <div class="row"><span class="left bold">DUE:</span><span class="right" style="color:#c00;">₹{{ number_format($invoice->due_amount, 0) }}</span></div>
        @endif
    </div>

    <div class="divider"></div>

    @if($invoice->payments->count() > 0)
        <div style="font-size:8px;">
            @foreach($invoice->payments as $p)
                <div class="row"><span class="left">{{ $p->paymentMode->name ?? 'N/A' }}</span><span class="right">₹{{ number_format($p->amount, 0) }}</span></div>
            @endforeach
        </div>
        <div class="divider"></div>
    @endif

    @if($showFooter)
        @if($footerImage)
            <div class="center"><img src="{{ public_path('storage/' . $footerImage) }}" style="max-width:280px;max-height:40px;"></div>
        @else
            <div class="center" style="font-size:8px;">
                <div>*** THANK YOU ***</div>
                <div style="margin-top:2px;">{{ $invoice->barcode }}</div>
            </div>
        @endif
    @endif

</div>
</body>
</html>

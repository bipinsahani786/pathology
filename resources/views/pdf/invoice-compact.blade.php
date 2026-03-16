<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #333; line-height: 1.4; }
        .container { padding: 15px 20px; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 8px; margin-bottom: 10px; }
        .lab-name { font-size: 16px; font-weight: bold; color: #1a1a2e; }
        .inv-info { font-size: 9px; color: #555; text-align: right; }
        .patient-row { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 6px 10px; margin-bottom: 8px; font-size: 9px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .items-table th { background: #1e293b; color: #fff; padding: 5px 8px; font-size: 8px; text-transform: uppercase; }
        .items-table th:last-child { text-align: right; }
        .items-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 10px; }
        .items-table tr:nth-child(even) { background: #fafafa; }
        .amount { text-align: right; font-weight: bold; }
        .totals { float: right; width: 220px; font-size: 9px; }
        .totals .row { overflow: hidden; padding: 2px 0; }
        .totals .row .label { float: left; color: #666; }
        .totals .row .value { float: right; font-weight: bold; }
        .totals .grand { border-top: 2px solid #2563eb; margin-top: 4px; padding-top: 4px; font-size: 13px; color: #2563eb; }
        .discount { color: #16a34a; }
        .due { color: #dc2626; }
        .paid { color: #16a34a; }
        .clearfix::after { content: ""; display: table; clear: both; }
        .footer { border-top: 1px solid #e5e7eb; padding-top: 6px; margin-top: 12px; text-align: center; font-size: 8px; color: #94a3b8; clear: both; }
        .badge { padding: 1px 6px; border-radius: 8px; font-size: 7px; font-weight: bold; }
        .badge-test { background: #dbeafe; color: #2563eb; }
        .badge-pkg { background: #ede9fe; color: #7c3aed; }
        .payments { font-size: 9px; margin-top: 8px; clear: both; }
        .payments th { background: #f1f5f9; padding: 3px 6px; font-size: 8px; text-transform: uppercase; color: #666; }
        .payments td { padding: 3px 6px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>
<div class="container">

    @if($showHeader)
        @if($headerImage)
            <div style="margin-bottom:8px;"><img src="{{ public_path('storage/' . $headerImage) }}" alt="Header" style="width:100%;max-height:80px;"></div>
        @else
            <div class="header">
                <table width="100%"><tr>
                    <td valign="middle">
                        @if($company->logo)<img src="{{ public_path('storage/' . $company->logo) }}" style="max-height:30px;margin-right:8px;vertical-align:middle;">@endif
                        <span class="lab-name">{{ $company->name }}</span>
                        <span style="font-size:8px;color:#888;margin-left:4px;">{{ $company->phone ?? '' }} · {{ $company->email ?? '' }}</span>
                    </td>
                    <td class="inv-info" valign="middle">
                        <strong style="font-size:12px;">#{{ $invoice->invoice_number }}</strong><br>
                        {{ $invoice->invoice_date->format('d/m/Y h:iA') }} · {{ $invoice->barcode }}
                    </td>
                </tr></table>
            </div>
        @endif
    @else
        <div style="border-bottom:1px solid #ddd;padding-bottom:6px;margin-bottom:8px;">
            <table width="100%"><tr>
                <td><strong>#{{ $invoice->invoice_number }}</strong></td>
                <td style="text-align:right;font-size:9px;">{{ $invoice->invoice_date->format('d/m/Y h:iA') }}</td>
            </tr></table>
        </div>
    @endif

    {{-- Patient + Ref compact --}}
    <div class="patient-row">
        <strong>Patient:</strong> {{ $invoice->patient->name ?? 'N/A' }} ({{ $invoice->patient->phone ?? '' }})
        @if($invoice->patient->patientProfile) · {{ $invoice->patient->patientProfile->age ?? '' }}{{ $invoice->patient->patientProfile->age_type == 'Years' ? 'Y' : 'M' }}/{{ substr($invoice->patient->patientProfile->gender ?? '', 0, 1) }}@endif
        @if($invoice->doctor) · <strong>Ref:</strong> {{ $invoice->doctor->name }}@endif
        @if($invoice->collectionCenter) · <strong>Center:</strong> {{ $invoice->collectionCenter->name }}@endif
        · <strong>Type:</strong> {{ $invoice->collection_type ?? 'Center' }}
    </div>

    {{-- Items --}}
    <table class="items-table">
        <thead><tr><th style="width:25px;">#</th><th>Test / Package</th><th style="width:50px;text-align:center;">Type</th><th style="width:80px;text-align:right;">Amount (₹)</th></tr></thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:bold;">{{ $item->test_name }}</td>
                    <td style="text-align:center;"><span class="badge {{ $item->is_package ? 'badge-pkg' : 'badge-test' }}">{{ $item->is_package ? 'PKG' : 'TEST' }}</span></td>
                    <td class="amount">₹{{ number_format($item->mrp, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="clearfix">
        <div class="totals">
            <div class="row clearfix"><span class="label">Subtotal</span><span class="value">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
            @php $totalDisc = $invoice->membership_discount_amount + $invoice->voucher_discount_amount + $invoice->discount_amount; @endphp
            @if($totalDisc > 0)
                <div class="row clearfix"><span class="label">Total Discount</span><span class="value discount">- ₹{{ number_format($totalDisc, 2) }}</span></div>
            @endif
            <div class="row grand clearfix"><span class="label">TOTAL</span><span class="value">₹{{ number_format($invoice->total_amount, 2) }}</span></div>
            <div class="row clearfix"><span class="label">Paid</span><span class="value paid">₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
            @if($invoice->due_amount > 0)
                <div class="row clearfix"><span class="label">Due</span><span class="value due">₹{{ number_format($invoice->due_amount, 2) }}</span></div>
            @endif
        </div>
    </div>

    {{-- Payments --}}
    @if($invoice->payments->count() > 0)
        <div class="payments">
            <table width="100%"><thead><tr><th>Mode</th><th>Amount</th><th>Txn ID</th></tr></thead><tbody>
                @foreach($invoice->payments as $p)
                    <tr><td>{{ $p->paymentMode->name ?? 'N/A' }}</td><td style="font-weight:bold;">₹{{ number_format($p->amount, 2) }}</td><td>{{ $p->transaction_id ?? '—' }}</td></tr>
                @endforeach
            </tbody></table>
        </div>
    @endif

    @if($showFooter)
        <div class="footer">
            @if($footerImage)
                <img src="{{ public_path('storage/' . $footerImage) }}" alt="Footer" style="max-height:50px;">
            @else
                Computer Generated · {{ $company->name }} @if($company->gst_number)· GST: {{ $company->gst_number }}@endif
            @endif
        </div>
    @endif

</div>
</body>
</html>

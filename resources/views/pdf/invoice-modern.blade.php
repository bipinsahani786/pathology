<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { padding: 0; }

        /* Gradient Banner */
        .banner { background: linear-gradient(135deg, #0ea5e9, #2563eb, #7c3aed); padding: 25px 30px; color: #fff; }
        .banner .lab-name { font-size: 24px; font-weight: bold; }
        .banner .lab-tagline { font-size: 10px; opacity: 0.85; margin-top: 2px; }
        .banner .lab-contact { font-size: 9px; opacity: 0.7; margin-top: 4px; }
        .banner .inv-badge { background: rgba(255,255,255,0.2); border-radius: 8px; padding: 10px 16px; text-align: right; }
        .banner .inv-number { font-size: 20px; font-weight: bold; }
        .banner .inv-meta { font-size: 9px; opacity: 0.8; }

        .body-content { padding: 20px 30px; }

        /* Cards */
        .card-row { margin-bottom: 15px; }
        .card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px 14px; }
        .card-accent { border-left: 4px solid #2563eb; }
        .card-green { border-left: 4px solid #16a34a; }
        .card-orange { border-left: 4px solid #f59e0b; }
        .card-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 4px; }
        .card-value { font-size: 12px; font-weight: bold; color: #1e293b; }
        .card-sub { font-size: 9px; color: #64748b; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; border-radius: 8px; overflow: hidden; }
        .items-table thead th { background: linear-gradient(135deg, #1e293b, #334155); color: #fff; padding: 10px 12px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table thead th:last-child { text-align: right; }
        .items-table tbody td { padding: 9px 12px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .amount { text-align: right; font-weight: bold; color: #1e293b; }
        .badge { padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .badge-test { background: #dbeafe; color: #2563eb; }
        .badge-pkg { background: #ede9fe; color: #7c3aed; }

        /* Totals */
        .totals-box { background: linear-gradient(135deg, #f8fafc, #eef2ff); border: 1px solid #c7d2fe; border-radius: 8px; padding: 14px 16px; float: right; width: 280px; }
        .total-row { display: block; overflow: hidden; padding: 3px 0; font-size: 10px; }
        .total-label { float: left; color: #64748b; }
        .total-value { float: right; font-weight: bold; }
        .total-row.grand { border-top: 2px solid #2563eb; margin-top: 6px; padding-top: 8px; font-size: 15px; }
        .discount { color: #16a34a; }
        .due { color: #dc2626; }
        .paid { color: #16a34a; }
        .grand .total-label, .grand .total-value { color: #2563eb; }

        .clearfix::after { content: ""; display: table; clear: both; }

        .footer { border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 20px; text-align: center; font-size: 9px; color: #94a3b8; }
        .footer-image img { max-height: 60px; }

        .payments-table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 10px; }
        .payments-table th { background: #f1f5f9; padding: 5px 8px; text-align: left; font-size: 9px; text-transform: uppercase; color: #64748b; }
        .payments-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
    </style>
</head>
<body>
<div class="container">

    {{-- ═══════ BANNER HEADER ═══════ --}}
    @if($showHeader)
        @if($headerImage)
            <div style="margin-bottom:15px;"><img src="{{ public_path('storage/' . $headerImage) }}" alt="Header" style="width:100%;max-height:100px;"></div>
        @else
            <div class="banner">
                <table width="100%">
                    <tr>
                        <td width="60%" valign="middle">
                            @if($company->logo)
                                <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height:40px;margin-bottom:4px;"><br>
                            @endif
                            <span class="lab-name">{{ $company->name }}</span>
                            @if($company->tagline)<div class="lab-tagline">{{ $company->tagline }}</div>@endif
                            <div class="lab-contact">
                                {{ $company->address ?? '' }}
                                @if($company->phone) · 📞 {{ $company->phone }}@endif
                                @if($company->gst_number) · GST: {{ $company->gst_number }}@endif
                            </div>
                        </td>
                        <td width="40%" valign="middle">
                            <div class="inv-badge">
                                <div style="font-size:10px;opacity:0.7;text-transform:uppercase;letter-spacing:1px;">Invoice</div>
                                <div class="inv-number">#{{ $invoice->invoice_number }}</div>
                                <div class="inv-meta">{{ $invoice->invoice_date->format('d M Y, h:i A') }}</div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        @endif
    @else
        <div style="padding:15px 30px;border-bottom:1px solid #e5e7eb;">
            <table width="100%"><tr>
                <td><strong style="font-size:14px;">Invoice #{{ $invoice->invoice_number }}</strong></td>
                <td style="text-align:right;font-size:10px;">{{ $invoice->invoice_date->format('d M Y, h:i A') }}</td>
            </tr></table>
        </div>
    @endif

    <div class="body-content">

        {{-- ═══════ INFO CARDS ═══════ --}}
        <div class="card-row">
            <table width="100%" cellspacing="0">
                <tr>
                    <td width="32%" valign="top">
                        <div class="card card-accent">
                            <div class="card-label">👤 Patient</div>
                            <div class="card-value">{{ $invoice->patient->name ?? 'N/A' }}</div>
                            <div class="card-sub">📞 {{ $invoice->patient->phone ?? '—' }}</div>
                            @if($invoice->patient->patientProfile)
                                <div class="card-sub">{{ $invoice->patient->patientProfile->age ?? '' }} {{ $invoice->patient->patientProfile->age_type ?? 'Yrs' }} · {{ $invoice->patient->patientProfile->gender ?? '' }}</div>
                            @endif
                        </div>
                    </td>
                    <td width="2%"></td>
                    <td width="32%" valign="top">
                        <div class="card card-green">
                            <div class="card-label">📋 Invoice Info</div>
                            <div class="card-sub">Barcode: <strong>{{ $invoice->barcode }}</strong></div>
                            <div class="card-sub">Type: <strong>{{ $invoice->collection_type ?? 'Center' }}</strong></div>
                            @if($invoice->collectionCenter)<div class="card-sub">Center: <strong>{{ $invoice->collectionCenter->name }}</strong></div>@endif
                        </div>
                    </td>
                    <td width="2%"></td>
                    <td width="32%" valign="top">
                        <div class="card card-orange">
                            <div class="card-label">🩺 Referral</div>
                            @if($invoice->doctor)
                                <div class="card-value">{{ $invoice->doctor->name }}</div>
                                @if($invoice->doctor->doctorProfile)<div class="card-sub">{{ $invoice->doctor->doctorProfile->specialization ?? '' }}</div>@endif
                            @else
                                <div class="card-sub">No referral doctor</div>
                            @endif
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ═══════ ITEMS ═══════ --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:30px;">#</th>
                    <th>Test / Package</th>
                    <th style="width:70px;text-align:center;">Type</th>
                    <th style="width:90px;text-align:right;">Amount (₹)</th>
                </tr>
            </thead>
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

        {{-- ═══════ TOTALS ═══════ --}}
        <div class="clearfix">
            <div class="totals-box">
                <div class="total-row clearfix"><span class="total-label">Subtotal</span><span class="total-value">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
                @if($invoice->membership_discount_amount > 0)
                    <div class="total-row clearfix"><span class="total-label">Membership Disc.</span><span class="total-value discount">- ₹{{ number_format($invoice->membership_discount_amount, 2) }}</span></div>
                @endif
                @if($invoice->voucher_discount_amount > 0)
                    <div class="total-row clearfix"><span class="total-label">Voucher Disc.</span><span class="total-value discount">- ₹{{ number_format($invoice->voucher_discount_amount, 2) }}</span></div>
                @endif
                @if($invoice->discount_amount > 0)
                    <div class="total-row clearfix"><span class="total-label">Manual Disc.</span><span class="total-value discount">- ₹{{ number_format($invoice->discount_amount, 2) }}</span></div>
                @endif
                <div class="total-row grand clearfix"><span class="total-label">NET PAYABLE</span><span class="total-value">₹{{ number_format($invoice->total_amount, 2) }}</span></div>
                <div class="total-row clearfix"><span class="total-label">Paid</span><span class="total-value paid">₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
                @if($invoice->due_amount > 0)
                    <div class="total-row clearfix"><span class="total-label">Due</span><span class="total-value due">₹{{ number_format($invoice->due_amount, 2) }}</span></div>
                @endif
            </div>
        </div>

        {{-- ═══════ PAYMENTS ═══════ --}}
        @if($invoice->payments->count() > 0)
            <div style="clear:both;margin-top:10px;">
                <div class="card-label" style="margin-bottom:5px;">💳 Payment Details</div>
                <table class="payments-table">
                    <thead><tr><th>Mode</th><th>Amount</th><th>Txn ID</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $p)
                            <tr><td>{{ $p->paymentMode->name ?? 'N/A' }}</td><td style="font-weight:bold;">₹{{ number_format($p->amount, 2) }}</td><td>{{ $p->transaction_id ?? '—' }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ═══════ FOOTER ═══════ --}}
        @if($showFooter)
            <div class="footer">
                @if($footerImage)
                    <div class="footer-image"><img src="{{ public_path('storage/' . $footerImage) }}" alt="Footer"></div>
                @else
                    <p>Computer-generated invoice · No signature required</p>
                    <p>Thank you! · <strong>{{ $company->name }}</strong> @if($company->website) · {{ $company->website }}@endif</p>
                @endif
            </div>
        @endif

    </div>
</div>
</body>
</html>

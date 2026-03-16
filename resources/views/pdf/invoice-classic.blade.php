<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.5; }
        .container { padding: 20px 30px; }

        /* Header */
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 15px; }
        .header-image { width: 100%; margin-bottom: 10px; }
        .header-image img { max-height: 80px; }
        .lab-name { font-size: 22px; font-weight: bold; color: #1a1a2e; }
        .lab-tagline { font-size: 10px; color: #666; margin-top: 2px; }
        .lab-contact { font-size: 9px; color: #888; margin-top: 4px; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #2563eb; text-align: right; }
        .invoice-meta { text-align: right; font-size: 10px; color: #555; }
        .invoice-meta strong { color: #333; }

        /* Info Boxes */
        .info-section { margin-bottom: 15px; }
        .info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; }
        .info-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 4px; }
        .info-value { font-size: 12px; font-weight: bold; color: #1e293b; }
        .info-sub { font-size: 9px; color: #64748b; }

        /* Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .items-table thead th { background: #1e293b; color: #fff; padding: 8px 10px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; }
        .items-table thead th:first-child { border-radius: 6px 0 0 0; }
        .items-table thead th:last-child { border-radius: 0 6px 0 0; text-align: right; }
        .items-table tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }
        .items-table tbody tr:nth-child(even) { background: #f8fafc; }
        .items-table .amount { text-align: right; font-weight: bold; }
        .items-table .type-badge { background: #dbeafe; color: #2563eb; padding: 2px 8px; border-radius: 10px; font-size: 8px; font-weight: bold; }
        .items-table .pkg-badge { background: #ede9fe; color: #7c3aed; }

        /* Totals */
        .totals-section { margin-bottom: 15px; }
        .totals-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 14px; float: right; width: 280px; }
        .total-row { display: block; overflow: hidden; padding: 3px 0; font-size: 10px; }
        .total-label { float: left; color: #64748b; }
        .total-value { float: right; font-weight: bold; }
        .total-row.grand { border-top: 2px solid #2563eb; margin-top: 6px; padding-top: 8px; font-size: 14px; color: #2563eb; }
        .total-row .discount { color: #16a34a; }
        .total-row .due { color: #dc2626; }
        .total-row .paid { color: #16a34a; }

        /* Payments */
        .payments-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .payments-table th { background: #f1f5f9; padding: 5px 8px; text-align: left; font-size: 9px; text-transform: uppercase; color: #64748b; }
        .payments-table td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }

        /* Footer */
        .footer { border-top: 2px solid #e2e8f0; padding-top: 12px; margin-top: 20px; text-align: center; font-size: 9px; color: #94a3b8; }
        .footer-image { width: 100%; margin-top: 10px; }
        .footer-image img { max-height: 60px; }

        .clearfix::after { content: ""; display: table; clear: both; }

        .status-paid { background: #dcfce7; color: #16a34a; padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .status-partial { background: #fef3c7; color: #d97706; padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .status-unpaid { background: #fee2e2; color: #dc2626; padding: 3px 10px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">

    {{-- ═══════ HEADER ═══════ --}}
    @if($showHeader)
        <div class="header clearfix">
            @if($headerImage)
                <div class="header-image">
                    <img src="{{ public_path('storage/' . $headerImage) }}" alt="Header">
                </div>
            @else
                <table width="100%">
                    <tr>
                        <td width="60%" valign="top">
                            @if($company->logo)
                                <img src="{{ public_path('storage/' . $company->logo) }}" alt="Logo" style="max-height:50px;margin-bottom:6px;">
                                <br>
                            @endif
                            <span class="lab-name">{{ $company->name }}</span>
                            @if($company->tagline)<div class="lab-tagline">{{ $company->tagline }}</div>@endif
                            <div class="lab-contact">
                                {{ $company->address ?? '' }}<br>
                                @if($company->phone)📞 {{ $company->phone }}@endif
                                @if($company->email) · ✉ {{ $company->email }}@endif
                                @if($company->website)<br>🌐 {{ $company->website }}@endif
                                @if($company->gst_number)<br>GST: {{ $company->gst_number }}@endif
                            </div>
                        </td>
                        <td width="40%" valign="top" style="text-align:right;">
                            <div class="invoice-title">INVOICE</div>
                            <div class="invoice-meta">
                                <strong>#{{ $invoice->invoice_number }}</strong><br>
                                Date: {{ $invoice->invoice_date->format('d M Y, h:i A') }}<br>
                                Barcode: {{ $invoice->barcode }}<br>
                                <span class="status-{{ strtolower($invoice->payment_status) }}">{{ $invoice->payment_status }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            @endif
        </div>
    @else
        {{-- Minimal info when no header --}}
        <table width="100%" style="margin-bottom:15px;">
            <tr>
                <td><strong style="font-size:14px;">Invoice #{{ $invoice->invoice_number }}</strong></td>
                <td style="text-align:right;font-size:10px;">Date: {{ $invoice->invoice_date->format('d M Y, h:i A') }} · {{ $invoice->barcode }}</td>
            </tr>
        </table>
    @endif

    {{-- ═══════ PATIENT & DOCTOR INFO ═══════ --}}
    <div class="info-section">
        <table width="100%" cellspacing="0">
            <tr>
                <td width="48%" valign="top">
                    <div class="info-box">
                        <div class="info-label">👤 Patient Details</div>
                        <div class="info-value">{{ $invoice->patient->name ?? 'N/A' }}</div>
                        <div class="info-sub">📞 {{ $invoice->patient->phone ?? '—' }}</div>
                        @if($invoice->patient->patientProfile)
                            <div class="info-sub">
                                ID: {{ $invoice->patient->patientProfile->patient_id_string ?? '' }}
                                · {{ $invoice->patient->patientProfile->age ?? '' }} {{ $invoice->patient->patientProfile->age_type ?? 'Yrs' }}
                                · {{ $invoice->patient->patientProfile->gender ?? '' }}
                                @if($invoice->patient->patientProfile->blood_group) · {{ $invoice->patient->patientProfile->blood_group }}@endif
                            </div>
                        @endif
                    </div>
                </td>
                <td width="4%"></td>
                <td width="48%" valign="top">
                    <div class="info-box">
                        <div class="info-label">🏥 Collection Info</div>
                        @if($invoice->doctor)
                            <div class="info-value">Ref: {{ $invoice->doctor->name }}</div>
                            @if($invoice->doctor->doctorProfile)
                                <div class="info-sub">{{ $invoice->doctor->doctorProfile->specialization ?? '' }}</div>
                            @endif
                        @else
                            <div class="info-sub">No referral</div>
                        @endif
                        @if($invoice->collectionCenter)
                            <div class="info-sub">Center: {{ $invoice->collectionCenter->name }}</div>
                        @endif
                        <div class="info-sub">Type: {{ $invoice->collection_type ?? 'Center' }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════ ITEMS TABLE ═══════ --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Test / Package Name</th>
                <th style="width:70px;text-align:center;">Type</th>
                <th style="width:90px;text-align:right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:bold;">{{ $item->test_name }}</td>
                    <td style="text-align:center;"><span class="type-badge {{ $item->is_package ? 'pkg-badge' : '' }}">{{ $item->is_package ? 'PKG' : 'TEST' }}</span></td>
                    <td class="amount">₹{{ number_format($item->mrp, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ═══════ TOTALS ═══════ --}}
    <div class="totals-section clearfix">
        <div class="totals-box">
            <div class="total-row clearfix"><span class="total-label">Subtotal</span><span class="total-value">₹{{ number_format($invoice->subtotal, 2) }}</span></div>
            @if($invoice->membership_discount_amount > 0)
                <div class="total-row clearfix"><span class="total-label">Membership Discount</span><span class="total-value discount">- ₹{{ number_format($invoice->membership_discount_amount, 2) }}</span></div>
            @endif
            @if($invoice->voucher_discount_amount > 0)
                <div class="total-row clearfix"><span class="total-label">Voucher Discount</span><span class="total-value discount">- ₹{{ number_format($invoice->voucher_discount_amount, 2) }}</span></div>
            @endif
            @if($invoice->discount_amount > 0)
                <div class="total-row clearfix"><span class="total-label">Manual Discount</span><span class="total-value discount">- ₹{{ number_format($invoice->discount_amount, 2) }}</span></div>
            @endif
            <div class="total-row grand clearfix"><span class="total-label" style="font-weight:bold;">NET PAYABLE</span><span class="total-value">₹{{ number_format($invoice->total_amount, 2) }}</span></div>
            <div class="total-row clearfix"><span class="total-label">Paid Amount</span><span class="total-value paid">₹{{ number_format($invoice->paid_amount, 2) }}</span></div>
            @if($invoice->due_amount > 0)
                <div class="total-row clearfix"><span class="total-label">Due Amount</span><span class="total-value due">₹{{ number_format($invoice->due_amount, 2) }}</span></div>
            @endif
        </div>
    </div>

    {{-- ═══════ PAYMENT DETAILS ═══════ --}}
    @if($invoice->payments->count() > 0)
        <div style="clear:both;margin-top:10px;">
            <div class="info-label" style="margin-bottom:5px;">💳 Payment Details</div>
            <table class="payments-table">
                <thead><tr><th>Payment Mode</th><th>Amount</th><th>Transaction ID</th></tr></thead>
                <tbody>
                    @foreach($invoice->payments as $p)
                        <tr>
                            <td>{{ $p->paymentMode->name ?? 'N/A' }}</td>
                            <td style="font-weight:bold;">₹{{ number_format($p->amount, 2) }}</td>
                            <td>{{ $p->transaction_id ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ═══════ FOOTER ═══════ --}}
    @if($showFooter)
        <div class="footer">
            @if($footerImage)
                <div class="footer-image">
                    <img src="{{ public_path('storage/' . $footerImage) }}" alt="Footer">
                </div>
            @else
                <p>This is a computer-generated invoice. No signature is required.</p>
                <p style="margin-top:4px;">Thank you for choosing <strong>{{ $company->name }}</strong>!
                    @if($company->website) · 🌐 {{ $company->website }}@endif
                </p>
            @endif
        </div>
    @endif

</div>
</body>
</html>

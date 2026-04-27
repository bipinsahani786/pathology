<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bill Receipt - {{ $invoice->invoice_number }}</title>

    @php
        // ── Resolve image paths (Mirror of Report) ──
        $headerImgSrc = $settings['pdf_header_image'] ?? null;
        $footerImgSrc = $settings['pdf_footer_image'] ?? null;

        // ── Margins from Settings ──
        $marginTop    = ($settings['pdf_margin_top'] ?? 310) . 'px';
        $marginBottom = ($settings['pdf_margin_bottom'] ?? 255) . 'px';
        $headerHeight = ($settings['pdf_header_height'] ?? 200) . 'px';
        $footerHeight = ($settings['pdf_footer_height'] ?? 180) . 'px';
        
        $fontSize     = ($settings['pdf_font_size'] ?? 13) . 'px';
        $fontFamily   = $settings['pdf_font_family'] ?? 'Helvetica, Arial, sans-serif';
    @endphp

    <style>
        /* ── RESET ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: {{ $fontFamily }};
            font-size: {{ $fontSize }};
            color: #1a1a1a;
            background: #fff;
            line-height: 1.45;
            margin: {{ $marginTop }} 25px {{ $marginBottom }} 25px;
            padding-top: 10px; /* Extra safety gap */
        }

        /* ══════════════════════════════════════════════
           FIXED HEADER (Mirror of Report)
           ══════════════════════════════════════════════ */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: {{ (int)$headerHeight + 20 }}px;
            overflow: visible;
            width: 100%;
        }

        .header-banner {
            width: 100% !important;
            min-width: 100% !important;
            display: block;
        }

        /* ── PATIENT INFO BOX (Fixed in Header) ── */
        .patient-box {
            border: 1px solid #1a1a1a !important;
            margin: 4px 25px 0;
            padding: 8px 10px;
            font-size: 10.5px;
            display: block;
            border-radius: 2px;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-table td {
            padding: 0.5px 2px;
            vertical-align: top;
            line-height: 1.1;
        }

        .patient-table .lbl {
            font-weight: 700;
            color: #1a1a1a;
            width: 14%;
            white-space: nowrap;
        }

        .patient-table .val {
            color: #1a1a1a;
            width: 28%;
        }

        .patient-table .qr-cell {
            text-align: center;
            vertical-align: middle;
            width: 12%;
        }

        .qr-code {
            width: 55px;
            height: 55px;
            display: block;
            margin: 0 auto;
        }

        .barcode-img {
            max-width: 90px;
            height: 22px;
            margin-top: 5px;
        }

        /* ══════════════════════════════════════════════
           FIXED FOOTER
           ══════════════════════════════════════════════ */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: {{ $footerHeight }};
            width: 100%;
        }

        .footer-banner {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100% !important;
            min-width: 100% !important;
            display: block;
        }

        /* ── MAIN CONTENT ── */
        .bill-title-container {
            text-align: center;
            width: 100%;
            margin: 40px 0 30px; /* Significant top margin to move it below the fixed header info */
        }

        .bill-title {
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #1a1a1a;
            border-bottom: 2px solid #1a1a1a;
            display: inline-block;
            padding: 0 10px 3px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table th {
            text-align: left;
            padding: 12px 5px; /* Increased padding to prevent line overlap */
            border-top: 1.5px solid #1a1a1a;
            border-bottom: 1.5px solid #1a1a1a;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px 5px;
            border-bottom: 0.5px solid #ccc;
            font-size: 11px;
            vertical-align: middle;
        }

        .item-name { font-weight: 700; color: #1a1a1a; }
        .item-sub { font-size: 9px; color: #555; }

        /* ── Totals ── */
        .summary-wrapper {
            margin-top: 20px;
        }

        .status-badge {
            float: left;
            border: 2px solid #16a34a;
            color: #16a34a;
            padding: 5px 15px;
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            opacity: 0.3;
            transform: rotate(-4deg);
            margin-top: 20px;
        }

        .totals-table {
            float: right;
            width: 255px;
            border-collapse: collapse;
            border: 1px solid #1a1a1a;
            background: #fbfbfb;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 12px;
        }

        .grand-total {
            border-top: 1.5px solid #1a1a1a;
            font-weight: 900;
            font-size: 14px !important;
            color: #000;
        }

        .clearfix::after { content: ""; display: table; clear: both; }

        .end-note {
            text-align: center;
            font-size: 10px;
            margin-top: 40px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>

<body>

    {{-- ══════════════════ FIXED HEADER ══════════════════ --}}
    <header>
        <div style="height: {{ $headerHeight }}; width: 100%; overflow: hidden; margin-bottom: 12px; padding: 0;">
            @if($showHeader && $headerImgSrc)
                <img class="header-banner" src="{{ $headerImgSrc }}" alt="Header">
            @endif
        </div>

        {{-- Patient Info Box (Fixed in Header - Stays at top of every page) --}}
        <div class="patient-box">
            <table class="patient-table">
                <tr>
                    <td class="lbl">Name</td>
                    <td class="val">: {{ strtoupper($invoice->patient->name) }}</td>
                    <td class="lbl">Invoice No</td>
                    <td class="val">: {{ $invoice->invoice_number }}</td>
                    <td rowspan="4" class="qr-cell">
                        @if(isset($qrCodeUri))
                             <img src="{{ $qrCodeUri }}" class="qr-code">
                        @endif
                        @if(isset($barcodeUri))
                            <div class="barcode">
                                <img src="{{ $barcodeUri }}" class="barcode-img">
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Age/Gender</td>
                    <td class="val">: {{ $invoice->patient->patientProfile->age ?? '-' }} {{ $invoice->patient->patientProfile->age_type ?? 'Y' }} / {{ strtoupper($invoice->patient->patientProfile->gender ?? '-') }}</td>
                    <td class="lbl">Date</td>
                    <td class="val">: {{ $invoice->invoice_date->format('d/m/Y h:i A') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Referred By</td>
                    <td class="val">: {{ $invoice->doctor ? $invoice->doctor->name : 'SELF / DIRECT' }}</td>
                    <td class="lbl">Patient ID</td>
                    <td class="val">: {{ $invoice->patient->patientProfile->patient_id_string ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Contact No</td>
                    <td class="val">: {{ $invoice->patient->phone ?? 'N/A' }}</td>
                    <td class="lbl">Center</td>
                    <td class="val">: {{ $invoice->collectionCenter ? $invoice->collectionCenter->name : ($company->name ?? 'Main Center') }}</td>
                </tr>
            </table>
        </div>
    </header>

    {{-- ══════════════════ FIXED FOOTER ══════════════════ --}}
    @if($showFooter)
        <footer>
            <div style="height: {{ $footerHeight }}; width: 100%; overflow: hidden; padding: 0;">
                @if($footerImgSrc)
                    <img class="footer-banner" src="{{ $footerImgSrc }}" alt="Footer">
                @endif
            </div>
        </footer>
    @endif

    {{-- ══════════════════ MAIN CONTENT ══════════════════ --}}
    <div class="bill-title-container">
        <div class="bill-title">BILL CUM RECEIPT</div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="8%">#</th>
                <th width="72%">Investigation / Description</th>
                <th width="20%" style="text-align:right;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
                <tr>
                    <td style="color:#666;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td>
                        <div class="item-name">{{ strtoupper($item->test_name) }}</div>
                        <div class="item-sub">Category: {{ $item->is_package ? 'PACKAGE' : 'TEST' }}</div>
                    </td>
                    <td style="text-align:right; font-weight:700;">{{ number_format($item->mrp, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-wrapper clearfix">
        <div class="status-box">
             @if($invoice->payment_status === 'Paid')
                <div class="status-badge">FULLY PAID</div>
            @elseif($invoice->payment_status === 'Partial')
                <div class="status-badge" style="border-color:#d97706; color:#d97706;">PARTIAL</div>
            @else
                 <div class="status-badge" style="border-color:#dc2626; color:#dc2626;">UNPAID</div>
            @endif
        </div>

        <table class="totals-table">
            <tr>
                <td class="total-lbl">Sub-Total</td>
                <td class="total-val">Rs.{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
            @if($totalDisc > 0)
                <tr>
                    <td class="total-lbl">Discount (-)</td>
                    <td class="total-val" style="color:#16a34a;">- Rs.{{ number_format($totalDisc, 2) }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td>NET PAYABLE</td>
                <td class="total-val">Rs.{{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="total-lbl">Paid Amount</td>
                <td class="total-val" style="color:#16a34a;">Rs.{{ number_format($invoice->paid_amount, 2) }}</td>
            </tr>
            @if($invoice->due_amount > 0)
                <tr style="color:#dc2626; font-weight:700;">
                    <td>Balance Due</td>
                    <td class="total-val">Rs.{{ number_format($invoice->due_amount, 2) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="clearfix"></div>

    <div class="end-note">
        This is a computer-generated receipt and does not require a physical signature.
        <br>Thank you for choosing {{ $company->name }}.
    </div>

</body>
</html>

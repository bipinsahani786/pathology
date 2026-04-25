<!DOCTYPE html>
<html lang="en">
@php
if (!function_exists('getIndianCurrency')) {
    function getIndianCurrency(float $number) {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null; $digits_length = strlen($no); $i = 0; $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty', 40 => 'forty', 50 => 'fifty', 60 => 'sixty', 70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider); $no = floor($no / $divider); $i += $divider == 10 ? 1 : 2;
            if ($number) {
                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
            } else $str[] = null;
        }
        $Rupees = implode('', array_reverse($str));
        return ucwords($Rupees ? trim($Rupees) : "Zero");
    }
}
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Pro Invoice - {{ $invoice->invoice_number }}</title>

    @php
        $headerImgSrc = $settings['pdf_header_image'] ?? null;
        $footerImgSrc = $settings['pdf_footer_image'] ?? null;
        
        $marginTop    = ($settings['pdf_margin_top'] ?? 310) . 'px';
        $marginBottom = ($settings['pdf_margin_bottom'] ?? 255) . 'px';
        $headerHeight = ($settings['pdf_header_height'] ?? 200) . 'px';
        $footerHeight = ($settings['pdf_footer_height'] ?? 180) . 'px';
        $fontSize     = ($settings['pdf_font_size'] ?? 13) . 'px';
        $fontFamily   = $settings['pdf_font_family'] ?? 'Helvetica, Arial, sans-serif';
    @endphp

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: {{ $fontFamily }}; font-size: {{ $fontSize }}; color: #000; margin: {{ $marginTop }} 25px {{ $marginBottom }} 25px; line-height: 1.4; position: relative; padding-top: 10px; }

        /* Watermark */
        .watermark { position: absolute; top: 35%; left: 50%; width: 450px; margin-left: -225px; opacity: 0.06; z-index: -100; text-align: center; }
        .watermark img { width: 100%; filter: grayscale(100%); }

        /* Fixed Header */
        header { position: fixed; top: 0; left: 0; right: 0; height: {{ (int)$headerHeight + 20 }}px; overflow: visible; }
        .header-banner { width: 100%; display: block; }
        
        /* Patient Info (Standardized Grid) */
        .patient-box { border: 1.5px solid #1e293b !important; margin: 4px 25px 0; padding: 10px; border-radius: 2px; background: #fff; }
        .patient-table { width: 100%; border-collapse: collapse; }
        .patient-table td { padding: 1px 2px; vertical-align: top; font-size: 10.5px; }
        .patient-table .lbl { font-weight: 700; color: #4b5563; width: 14%; }
        .patient-table .val { font-weight: 700; color: #000; width: 28%; }
        .patient-table .qr-cell { width: 12%; text-align: center; border-left: 1px solid #000; }
        
        .qr-code { width: 55px; height: 55px; margin: 0 auto; }
        .barcode-img { max-width: 90px; height: 22px; margin-top: 5px; }

        /* Fixed Footer */
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: {{ $footerHeight }}; }
        .footer-banner { position: absolute; bottom: 0; width: 100%; }

        /* Main Content */
        .bill-title-container { text-align: center; margin: 40px 0 30px; }
        .bill-title { font-weight: 900; font-size: 15px; color: #000; border-bottom: 2.5px solid #000; display: inline-block; padding: 0 10px 4px; text-transform: uppercase; letter-spacing: 2px; }

        .items-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1.5px solid #1e293b; }
        .items-table th { background: #f3f4f6; color: #000; padding: 12px 10px; font-size: 11px; text-align: left; text-transform: uppercase; border: 1px solid #1e293b; }
        .items-table td { padding: 10px; border: 1px solid #e5e7eb; font-size: 11px; }
        
        /* Amount in Words Area */
        .words-area { margin-top: 15px; padding: 8px 12px; background: #f9fafb; border: 1px dashed #9ca3af; font-size: 11px; color: #374151; }

        /* Totals Area */
        .summary-wrapper { margin-top: 20px; }
        .totals-table { float: right; width: 260px; border-collapse: collapse; border: 1.5px solid #1e293b; }
        .totals-table td { padding: 8px 12px; font-size: 12px; border-bottom: 1px solid #f3f4f6; }
        .grand-total { background: #1e293b; color: #fff; font-weight: 900; font-size: 15px !important; }

        /* Payments History */
        .payment-history { margin-top: 25px; }
        .payment-title { font-weight: 700; font-size: 11px; text-transform: uppercase; margin-bottom: 5px; color: #4b5563; }
        .payments-table { width: 60%; border-collapse: collapse; font-size: 10px; }
        .payments-table th { text-align: left; background: #f9fafb; padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        .payments-table td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; }

        .clearfix::after { content: ""; display: table; clear: both; }
        .end-note { text-align: center; font-size: 10px; margin-top: 35px; color: #6b7280; font-style: italic; border-top: 1px solid #e5e7eb; padding-top: 15px; }
    </style>
</head>
<body>

    @if($company->logo)
        <div class="watermark"><img src="{{ storage_base64($company->logo) }}"></div>
    @endif

    <header>
        <div style="height: {{ $headerHeight }}; width: 100%; overflow: hidden; margin-bottom: 12px;">
            @if($showHeader && $headerImgSrc)
                <img class="header-banner" src="{{ $headerImgSrc }}" alt="Header" style="width: 100%;">
            @endif
        </div>
        <div class="patient-box">
            <table class="patient-table">
                <tr>
                    <td class="lbl">Patient Name</td>
                    <td class="val">: {{ strtoupper($invoice->patient->name) }}</td>
                    <td class="lbl">Invoice No</td>
                    <td class="val">: {{ $invoice->invoice_number }}</td>
                    <td rowspan="4" class="qr-cell">
                        @if(isset($qrCodeUri)) <img src="{{ $qrCodeUri }}" class="qr-code"> @endif
                        @if(isset($barcodeUri)) <img src="{{ $barcodeUri }}" class="barcode-img"> @endif
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

    @if($showFooter)
        <footer>
            <div style="height: {{ $footerHeight }}; width: 100%; overflow: hidden;">
                @if($footerImgSrc)
                    <img class="footer-banner" src="{{ $footerImgSrc }}" alt="Footer" style="width: 100%;">
                @endif
            </div>
        </footer>
    @endif

    <div class="bill-title-container">
        <div class="bill-title">OFFICIAL PAYMENT RECEIPT</div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="8%">#</th>
                <th width="72%">Particulars / Description</th>
                <th width="20%" style="text-align:right;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $idx => $item)
                <tr>
                    <td>{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                    <td style="font-weight:700;">{{ strtoupper($item->test_name) }}</td>
                    <td style="text-align:right;">{{ number_format($item->mrp, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="words-area">
        <strong>Received with Thanks:</strong> Rs. {{ getIndianCurrency($invoice->paid_amount) }} Only
    </div>

    <div class="summary-wrapper clearfix">
        <table class="totals-table">
            <tr><td>Gross Total</td><td style="text-align:right;font-weight:700;">Rs.{{ number_format($invoice->subtotal, 2) }}</td></tr>
            @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
            @if($totalDisc > 0)
                <tr><td>Taxable Discount (-)</td><td style="text-align:right;font-weight:700;">- Rs.{{ number_format($totalDisc, 2) }}</td></tr>
            @endif
            <tr class="grand-total"><td>NET PAYABLE</td><td style="text-align:right;">Rs.{{ number_format($invoice->total_amount, 2) }}</td></tr>
            <tr><td>Amount Received</td><td style="text-align:right;font-weight:700;">Rs.{{ number_format($invoice->paid_amount, 2) }}</td></tr>
            @if($invoice->due_amount > 0)
                <tr style="color:#dc2626; font-weight:700;"><td>Total Balance Due</td><td style="text-align:right;">Rs.{{ number_format($invoice->due_amount, 2) }}</td></tr>
            @endif
        </table>

        @if($invoice->payments->count() > 0)
            <div class="payment-history">
                <div class="payment-title">Payment Transaction History</div>
                <table class="payments-table">
                    <thead><tr><th>Date</th><th>Mode</th><th style="text-align:right;">Amount</th></tr></thead>
                    <tbody>
                        @foreach($invoice->payments as $p)
                            <tr>
                                <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                <td>{{ $p->paymentMode->name ?? 'N/A' }} {{ $p->transaction_id ? '(Txn: '.$p->transaction_id.')' : '' }}</td>
                                <td style="text-align:right;font-weight:700;">Rs.{{ number_format($p->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="clearfix"></div>
    <div class="end-note">
        This is an official document generated by {{ $company->name }}. Use the QR code for instant digital verification.
        <br>Thank you for your trust.
    </div>

</body>
</html>

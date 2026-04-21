<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Receipt - {{ $invoice->invoice_number }}</title>

    <style>
        /* Thermal 80mm Optimization */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Courier New', monospace;
            font-size: 10px;
            color: #000;
            width: 72mm;
            /* ~80mm roll minus typical margins */
            margin: 0 auto;
            padding: 5px;
            line-height: 1.4;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .header-section {
            margin-bottom: 10px;
        }

        .lab-name {
            font-size: 14px;
            font-weight: 900;
            color: #000;
        }

        .lab-info {
            font-size: 8.5px;
            margin-top: 2px;
        }

        .info-row {
            display: block;
            overflow: hidden;
            margin-bottom: 2px;
            font-size: 9px;
        }

        .info-row .lbl {
            float: left;
            width: 35%;
        }

        .info-row .val {
            float: right;
            width: 65%;
            text-align: right;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .items-table th {
            text-align: left;
            font-size: 9px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        .items-table td {
            padding: 5px 0;
            font-size: 9.5px;
            vertical-align: top;
            border-bottom: 0.2px solid #eee;
        }

        .totals-section {
            margin-top: 10px;
        }

        .total-row {
            display: block;
            overflow: hidden;
            padding: 2px 0;
            font-size: 10px;
        }

        .total-row .lbl {
            float: left;
        }

        .total-row .val {
            float: right;
            font-weight: bold;
        }

        .grand-total {
            border-top: 1px solid #000;
            margin-top: 5px;
            padding-top: 5px;
            font-size: 13px !important;
        }

        .qr-section {
            margin-top: 15px;
            text-align: center;
            border-top: 1px dashed #ccc;
            padding-top: 10px;
        }

        .qr-code {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: block;
        }

        .qr-help {
            font-size: 7px;
            color: #666;
            margin-top: 4px;
        }

        .footer-note {
            text-align: center;
            font-size: 8px;
            margin-top: 15px;
            border-top: 0.5px solid #ccc;
            padding-top: 8px;
        }
    </style>
</head>

<body>

    <div class="header-section center">
        <div class="lab-name">{{ strtoupper($company->name) }}</div>
        <div class="lab-info">
            {{ $company->address ?? '' }}<br>
            📞 {{ $company->phone ?? '' }} @if($company->gst_number) | GST: {{ $company->gst_number }} @endif
        </div>
    </div>

    <div class="divider"></div>

    <div class="info-row"><span class="lbl">Bill No:</span><span class="val">{{ $invoice->invoice_number }}</span></div>
    <div class="info-row"><span class="lbl">Date:</span><span
            class="val">{{ $invoice->invoice_date->format('d/m/Y h:i A') }}</span></div>
    <div class="info-row"><span class="lbl">Patient:</span><span
            class="val">{{ strtoupper($invoice->patient->name) }}</span></div>
    <div class="info-row"><span class="lbl">Patient ID:</span><span
            class="val">{{ $invoice->patient->patientProfile->patient_id_string ?? 'N/A' }}</span></div>
    @if($invoice->doctor)
        <div class="info-row"><span class="lbl">Ref By:</span><span class="val">{{ $invoice->doctor->name }}</span></div>
    @endif

    <div class="divider"></div>

    <table class="items-table">
        <thead>
            <tr>
                <th width="70%">Investigation</th>
                <th width="30%" style="text-align:right;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ strtoupper($item->test_name) }}</td>
                    <td style="text-align:right; font-weight:700;">{{ number_format($item->mrp, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="total-row"><span class="lbl">Subtotal:</span><span
                class="val">Rs.{{ number_format($invoice->subtotal, 2) }}</span></div>
        @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
        @if($totalDisc > 0)
            <div class="total-row"><span class="lbl">Total Savings:</span><span class="val">-
                    Rs.{{ number_format($totalDisc, 2) }}</span></div>
        @endif
        <div class="total-row grand-total"><span class="lbl bold">NET PAYABLE:</span><span
                class="val">Rs.{{ number_format($invoice->total_amount, 2) }}</span></div>
        <div class="total-row"><span class="lbl">Paid Amount:</span><span
                class="val">Rs.{{ number_format($invoice->paid_amount, 2) }}</span></div>
        @if($invoice->due_amount > 0)
            <div class="total-row" style="color:#dc2626;"><span class="lbl bold">DUE BALANCE:</span><span
                    class="val">Rs.{{ number_format($invoice->due_amount, 2) }}</span></div>
        @endif
    </div>

    @if($invoice->payments->count() > 0)
        <div class="divider"></div>
        <div style="font-size:8.5px;">
            <div class="bold center" style="margin-bottom: 5px; white-space: nowrap; font-size: 9px;">PAYMENT DETAILS</div>
            @foreach($invoice->payments as $p)
                <div class="total-row"><span class="lbl">{{ $p->paymentMode->name ?? 'Mode' }}</span><span
                        class="val">Rs.{{ number_format($p->amount, 2) }}</span></div>
            @endforeach
        </div>
    @endif

    <div class="qr-section">
        @if(isset($qrCodeUri))
            <img src="{{ $qrCodeUri }}" class="qr-code">
            <div class="qr-help">SCAN TO VERIFY RECEIPT ONLINE</div>
        @endif
    </div>

    <div class="footer-note center">
        *** THANK YOU FOR VISITING ***
        <br>{{ $company->name }}
        <br>{{ $invoice->barcode }}
    </div>

</body>

</html>
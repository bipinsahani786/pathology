<!DOCTYPE html>
<html lang="en">
@php
    if (!function_exists('getIndianCurrency')) {
        function getIndianCurrency(float $number)
        {
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $hundred = null;
            $digits_length = strlen($no);
            $i = 0;
            $str = array();
            $words = array(
                0 => '',
                1 => 'one',
                2 => 'two',
                3 => 'three',
                4 => 'four',
                5 => 'five',
                6 => 'six',
                7 => 'seven',
                8 => 'eight',
                9 => 'nine',
                10 => 'ten',
                11 => 'eleven',
                12 => 'twelve',
                13 => 'thirteen',
                14 => 'fourteen',
                15 => 'fifteen',
                16 => 'sixteen',
                17 => 'seventeen',
                18 => 'eighteen',
                19 => 'nineteen',
                20 => 'twenty',
                30 => 'thirty',
                40 => 'forty',
                50 => 'fifty',
                60 => 'sixty',
                70 => 'seventy',
                80 => 'eighty',
                90 => 'ninety'
            );
            $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
            while ($i < $digits_length) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += $divider == 10 ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' ' : null;
                    $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
                } else
                    $str[] = null;
            }
            $Rupees = implode('', array_reverse($str));
            return ucwords($Rupees ? trim($Rupees) : "Zero");
        }
    }

    // ── Resolve Logo ──
    $logoBase64 = null;
    if ($company->logo) {
        $logoBase64 = storage_base64($company->logo);
    }
    if (!$logoBase64) {
        $fallbackPath = public_path('assets/images/healthcare-logo.png');
        if (file_exists($fallbackPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($fallbackPath));
        }
    }
@endphp

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Bill Receipt - {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 20mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            background: #fff;
            padding: 0 10px;
        }
    </style>
</head>

<body>

    <!-- Upper Half Page Container -->
    <div style="height: 135mm; max-height: 135mm; overflow: hidden; position: relative; border: 1px solid #000; padding: 12px; margin-top: 4px; box-sizing: border-box;">

        <!-- ── CENTERED LAB DETAILS (HEADER) ── -->
        <div style="text-align: center; margin-bottom: 8px;">
            <div
                style="font-size: 18px; font-weight: 800; color: #000; text-transform: uppercase; letter-spacing: 0.5px;">
                @php
                    $displayLabName = ($invoice->branch && $invoice->branch->name) ? $invoice->branch->name : $company->name;
                    $displayAddress = ($invoice->branch && $invoice->branch->address) ? $invoice->branch->address : ($company->address ?? '');
                    $displayPhone = ($invoice->branch && $invoice->branch->contact_number) ? $invoice->branch->contact_number : ($company->phone ?? '');
                @endphp
                {{ $displayLabName }}</div>
            <div style="font-size: 10.5px; color: #000; margin-top: 2px; line-height: 1.35;">
                {{ $displayAddress }}<br>
                Phone: {{ $displayPhone }} @if($company->email) | Email: {{ $company->email }} @endif
                @if($company->gst_number) | GSTIN: {{ $company->gst_number }} @endif
            </div>
        </div>

        <!-- ── RECEIPT TITLE & META ── -->
        <div
            style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; margin-bottom: 8px; text-align: center;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 1px; color: #000;">BILL CUM
                RECEIPT</span>
            <div style="font-size: 10.5px; color: #000; margin-top: 1.5px;">
                <strong>Invoice No:</strong> {{ $invoice->invoice_number }} &nbsp;|&nbsp;
                <strong>Date:</strong> {{ $invoice->invoice_date->format('d-M-Y h:i A') }}
            </div>
        </div>

        <!-- ── PATIENT INFORMATION (GRID CARD) ── -->
        <div style="margin-bottom: 8px;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                <tr>
                    <td style="width: 13%; font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Patient Name</td>
                    <td style="width: 22%; font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000; text-transform: uppercase;">{{ $invoice->patient->name ?? 'N/A' }}</td>
                    <td style="width: 13%; font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Age / Gender</td>
                    <td style="width: 22%; font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">
                        {{ $invoice->patient->patientProfile->age ?? '-' }}
                        {{ ($invoice->patient->patientProfile->age_type ?? 'Yrs') == 'Years' ? 'Y' : (($invoice->patient->patientProfile->age_type ?? 'Yrs') == 'Months' ? 'M' : 'D') }}
                        / {{ strtoupper($invoice->patient->patientProfile->gender ?? '-') }}
                    </td>
                    <td rowspan="3" style="width: 20%; text-align: center; border: 1px solid #000; padding: 4px; vertical-align: middle;">
                        @if(isset($qrCodeUri))
                            <img src="{{ $qrCodeUri }}" style="width: 70px; height: 70px; display: inline-block; vertical-align: middle;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Patient ID</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">{{ $invoice->patient->formatted_id ?? 'N/A' }}</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Contact No</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">{{ $invoice->patient->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Referred By</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">{{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">Center</td>
                    <td style="font-size: 11px; color: #000; font-weight: bold; padding: 4.5px 8px; border: 1px solid #000;">{{ $invoice->collectionCenter ? $invoice->collectionCenter->name : 'Main Center' }}</td>
                </tr>
            </table>
        </div>

        <!-- ── INVESTIGATION ITEMS ── -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; border: 1px solid #000;">
            <thead>
                <tr>
                    <th style="width: 8%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: left; border: 1px solid #000;">#</th>
                    <th style="width: 72%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: left; border: 1px solid #000;">Investigation Description</th>
                    <th style="width: 20%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: right; border: 1px solid #000;">Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    <tr>
                        <td style="font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-size: 11px; font-weight: bold; padding: 5px 8px; color: #000; border: 1px solid #000;">
                            {{ strtoupper($item->test_name) }}
                            <span style="font-size: 8.5px; font-weight: normal; color: #000;">({{ $item->is_package ? 'Package' : 'Test' }})</span>
                        </td>
                        <td style="font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: right; color: #000; border: 1px solid #000;">{{ number_format($item->mrp, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ── SUMMARY SECTION ── -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Left: Received, Breakdown, Status -->
                <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                    <div style="border: 1px solid #000; padding: 6px 8px; font-size: 10.5px; color: #000; margin-bottom: 6px;">
                        <strong>Received Amount (in words):</strong><br>
                        Rs. {{ getIndianCurrency($invoice->paid_amount) }} Only
                    </div>
                    @if($invoice->payments->count() > 0)
                        <div style="font-size: 9.5px; color: #000; margin-bottom: 6px; line-height: 1.5; vertical-align: middle;">
                            <strong style="vertical-align: middle;">Payment Breakdown:</strong>
                            @foreach($invoice->payments as $p)
                                <span style="padding: 1.5px 5px; margin-left: 4px; display: inline-block; font-weight: bold; border: 1px solid #000; color: #000; vertical-align: middle;">
                                    {{ $p->paymentMode->name ?? 'Mode' }}: Rs.{{ number_format($p->amount, 0) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <div style="margin-top: 8px;">
                        @if($invoice->payment_status === 'Paid')
                            <span style="border: 1px solid #000; padding: 4px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000;">FULLY PAID</span>
                        @elseif($invoice->payment_status === 'Partial')
                            <span style="border: 1px solid #000; padding: 4px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000;">PARTIAL PAID</span>
                        @else
                            <span style="border: 1px solid #000; padding: 4px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #000;">UNPAID</span>
                        @endif
                    </div>
                </td>
                <!-- Right: Totals -->
                <td style="width: 45%; vertical-align: top;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <tr>
                            <td style="font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">Subtotal</td>
                            <td style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px; border: 1px solid #000;">Rs.{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
                        @if($totalDisc > 0)
                            <tr>
                                <td style="font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">Discount</td>
                                <td style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">- Rs.{{ number_format($totalDisc, 2) }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td style="font-size: 12px; padding: 6px 8px; color: #000; border: 1px solid #000; font-weight: bold; text-transform: uppercase;">Net Payable</td>
                            <td style="text-align: right; font-size: 12px; padding: 6px 8px; color: #000; border: 1px solid #000; font-weight: bold;">Rs.{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">Amount Paid</td>
                            <td style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000;">Rs.{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        @if($invoice->due_amount > 0)
                            <tr>
                                <td style="font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000; font-weight: bold;">Balance Due</td>
                                <td style="text-align: right; font-size: 11px; padding: 5px 8px; color: #000; border: 1px solid #000; font-weight: bold;">Rs.{{ number_format($invoice->due_amount, 2) }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- ── FOOTER DISCLAIMER ── -->
        <div style="text-align: center; font-size: 9px; color: #000; margin-top: 15px; border-top: 1px solid #000; padding-top: 5px;">
            This is a computer-generated billing receipt and does not require a physical signature.<br>
            Thank you for choosing {{ ($invoice->branch && $invoice->branch->name) ? $invoice->branch->name : $company->name }}.
        </div>
    </div>

</body>

</html>
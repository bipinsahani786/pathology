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
    <div style="height: 122mm; max-height: 122mm; overflow: hidden; position: relative;">

        <!-- ── CENTERED LAB DETAILS (HEADER) ── -->
        <div style="text-align: center; margin-bottom: 8px;">
            <div
                style="font-size: 18px; font-weight: 800; color: #000; text-transform: uppercase; letter-spacing: 0.5px;">
                {{ $company->name }}</div>
            <div style="font-size: 10.5px; color: #475569; margin-top: 2px; line-height: 1.35;">
                {{ $company->address ?? '' }}<br>
                Phone: {{ $company->phone ?? '' }} @if($company->email) | Email: {{ $company->email }} @endif
                @if($company->gst_number) | GSTIN: {{ $company->gst_number }} @endif
            </div>
        </div>

        <!-- ── RECEIPT TITLE & META ── -->
        <div
            style="border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1; padding: 4px 0; margin-bottom: 8px; text-align: center;">
            <span style="font-size: 13px; font-weight: 800; letter-spacing: 1px; color: #0f172a;">BILL CUM
                RECEIPT</span>
            <div style="font-size: 10.5px; color: #475569; margin-top: 1.5px;">
                <strong>Invoice No:</strong> {{ $invoice->invoice_number }} &nbsp;|&nbsp;
                <strong>Date:</strong> {{ $invoice->invoice_date->format('d-M-Y h:i A') }}
            </div>
        </div>

        <!-- ── PATIENT INFORMATION (GRID CARD) ── -->
        <div style="margin-bottom: 8px;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1;">
                <tr>
                    <td
                        style="width: 13%; font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Patient Name</td>
                    <td
                        style="width: 22%; font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ strtoupper($invoice->patient->name) }}</td>
                    <td
                        style="width: 13%; font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Age / Gender</td>
                    <td
                        style="width: 22%; font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ $invoice->patient->patientProfile->age ?? '-' }}
                        {{ $invoice->patient->patientProfile->age_type == 'Years' ? 'Y' : ($invoice->patient->patientProfile->age_type == 'Months' ? 'M' : 'D') }}
                        / {{ strtoupper($invoice->patient->patientProfile->gender ?? '-') }}</td>
                    <td rowspan="3"
                        style="width: 20%; text-align: center; border: 1px solid #cbd5e1; padding: 4px; vertical-align: middle; background: #ffffff;">
                        @if(isset($qrCodeUri))
                            <img src="{{ $qrCodeUri }}"
                                style="width: 75px; height: 75px; display: inline-block; vertical-align: middle;">
                        @endif
                    </td>
                </tr>
                <tr>
                    <td
                        style="font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Patient ID</td>
                    <td
                        style="font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ $invoice->patient->formatted_id ?? 'N/A' }}</td>
                    <td
                        style="font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Contact No</td>
                    <td
                        style="font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ $invoice->patient->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td
                        style="font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Referred By</td>
                    <td
                        style="font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
                    <td
                        style="font-size: 11px; color: #475569; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1; background: #f8fafc;">
                        Center</td>
                    <td
                        style="font-size: 11px; color: #0f172a; font-weight: bold; padding: 4.5px 8px; border: 1px solid #cbd5e1;">
                        {{ $invoice->collectionCenter ? $invoice->collectionCenter->name : 'Main Center' }}</td>
                </tr>
            </table>
        </div>

        <!-- ── INVESTIGATION ITEMS ── -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <thead>
                <tr style="background: #f1f5f9; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">
                    <th style="width: 8%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: left;">#
                    </th>
                    <th style="width: 72%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: left;">
                        Investigation Description</th>
                    <th style="width: 20%; font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: right;">
                        Amount (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $idx => $item)
                    <tr style="border-bottom: 1px solid #cbd5e1;">
                        <td style="font-size: 11px; padding: 5px 8px; color: #64748b;">
                            {{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}</td>
                        <td style="font-size: 11px; font-weight: bold; padding: 5px 8px; color: #0f172a;">
                            {{ strtoupper($item->test_name) }}
                            <span
                                style="font-size: 8.5px; font-weight: normal; color: #64748b;">({{ $item->is_package ? 'Package' : 'Test' }})</span>
                        </td>
                        <td
                            style="font-size: 11px; font-weight: bold; padding: 5px 8px; text-align: right; color: #0f172a;">
                            {{ number_format($item->mrp, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ── SUMMARY SECTION ── -->
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <!-- Left: Received, Breakdown, Status -->
                <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                    <div
                        style="border: 1px dashed #cbd5e1; background: #fafafa; padding: 6px 8px; border-radius: 4px; font-size: 10.5px; color: #475569; margin-bottom: 6px;">
                        <strong>Received Amount (in words):</strong><br>
                        Rs. {{ getIndianCurrency($invoice->paid_amount) }} Only
                    </div>
                    @if($invoice->payments->count() > 0)
                        <div style="font-size: 9.5px; color: #64748b; margin-bottom: 6px; line-height: 1.5; vertical-align: middle;">
                            <strong style="vertical-align: middle;">Payment Breakdown:</strong>
                            @foreach($invoice->payments as $p)
                                <span
                                    style="background: #edf2f7; padding: 1.5px 5px; border-radius: 2px; margin-left: 4px; display: inline-block; font-weight: bold; border: 1px solid #cbd5e1; color: #1e293b; vertical-align: middle;">
                                    {{ $p->paymentMode->name ?? 'Mode' }}: Rs.{{ number_format($p->amount, 0) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <div>
                        @if($invoice->payment_status === 'Paid')
                            <span
                                style="background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 3px 8px; font-size: 9.5px; font-weight: bold; border-radius: 3px; text-transform: uppercase;">FULLY
                                PAID</span>
                        @elseif($invoice->payment_status === 'Partial')
                            <span
                                style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 3px 8px; font-size: 9.5px; font-weight: bold; border-radius: 3px; text-transform: uppercase;">PARTIAL
                                PAID</span>
                        @else
                            <span
                                style="background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; padding: 3px 8px; font-size: 9.5px; font-weight: bold; border-radius: 3px; text-transform: uppercase;">UNPAID</span>
                        @endif
                    </div>
                </td>
                <!-- Right: Totals -->
                <td style="width: 45%; vertical-align: top;">
                    <table
                        style="width: 100%; border-collapse: collapse; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden;">
                        <tr style="border-bottom: 1px solid #cbd5e1;">
                            <td style="font-size: 11px; padding: 5px 8px; color: #475569;">Subtotal</td>
                            <td style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px;">
                                Rs.{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @php $totalDisc = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount; @endphp
                        @if($totalDisc > 0)
                            <tr style="border-bottom: 1px solid #cbd5e1;">
                                <td style="font-size: 11px; padding: 5px 8px; color: #475569;">Discount</td>
                                <td
                                    style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px; color: #16a34a;">
                                    - Rs.{{ number_format($totalDisc, 2) }}</td>
                            </tr>
                        @endif
                        <tr style="border-bottom: 1px solid #cbd5e1; background: #f8fafc; font-weight: bold;">
                            <td style="font-size: 12px; padding: 6px 8px; color: #0f172a;">NET PAYABLE</td>
                            <td style="text-align: right; font-size: 12px; padding: 6px 8px; color: #0f172a;">
                                Rs.{{ number_format($invoice->total_amount, 2) }}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid #cbd5e1;">
                            <td style="font-size: 11px; padding: 5px 8px; color: #475569;">Amount Paid</td>
                            <td
                                style="text-align: right; font-weight: bold; font-size: 11px; padding: 5px 8px; color: #16a34a;">
                                Rs.{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        @if($invoice->due_amount > 0)
                            <tr style="color: #dc2626; font-weight: bold;">
                                <td style="font-size: 11px; padding: 5px 8px;">Balance Due</td>
                                <td style="text-align: right; font-size: 11px; padding: 5px 8px;">
                                    Rs.{{ number_format($invoice->due_amount, 2) }}</td>
                            </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>

        <!-- ── FOOTER DISCLAIMER ── -->
        <div
            style="text-align: center; font-size: 9px; color: #94a3b8; margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 5px;">
            This is a computer-generated billing receipt and does not require a physical signature.<br>
            Thank you for choosing {{ $company->name }}.
        </div>
    </div>

    <!-- Dashed tear-off guide line -->
    <div
        style="border-top: 1.5px dashed #cbd5e1; margin-top: 12px; padding-top: 4px; text-align: center; font-size: 7.5px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1.5px;">
        ------------------------ Cut along this line to detach receipt ------------------------
    </div>

</body>

</html>
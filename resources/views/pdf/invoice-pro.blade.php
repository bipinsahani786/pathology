<!DOCTYPE html>
<html>
@php
if (!function_exists('getIndianCurrency')) {
    function getIndianCurrency(float $number)
    {
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $hundred = null;
        $digits_length = strlen($no);
        $i = 0;
        $str = array();
        $words = array(0 => '', 1 => 'one', 2 => 'two',
            3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six',
            7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve',
            13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen',
            16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen',
            19 => 'nineteen', 20 => 'twenty', 30 => 'thirty',
            40 => 'forty', 50 => 'fifty', 60 => 'sixty',
            70 => 'seventy', 80 => 'eighty', 90 => 'ninety');
        $digits = array('', 'hundred','thousand','lakh', 'crore');
        while( $i < $digits_length ) {
            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += $divider == 10 ? 1 : 2;
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
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Helvetica', Arial, sans-serif; 
            font-size: 13px; 
            color: #000; 
            line-height: 1.4;
            background: #fff;
        }
        @page { margin: 0; }
        .container {
            width: 210mm;
            min-height: 297mm;
            padding: 0;
            margin: 0;
            position: relative;
        }
        
        .watermark {
            position: absolute;
            top: 35%;
            left: 50%;
            width: 500px;
            margin-left: -250px;
            opacity: 0.08;
            z-index: -1;
            text-align: center;
        }
        .watermark img {
            width: 100%;
            filter: grayscale(100%);
        }

        .header-section {
            width: 100%;
            height: 40mm; /* Reserve approx 150px space */
            overflow: hidden;
        }
        .header-content {
            padding: 20px 30px 10px 30px;
        }
        .footer-section {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 25mm; /* Reserve approx 90px space */
        }
        .main-content {
            padding: 0 15mm;
            padding-bottom: 25mm; /* Avoid overlapping footer */
        }

        /* Accent Elements */
        .blue-border-top {
            border-top: 15px solid #285fac;
            padding-top: 15px;
            margin-bottom: 15px;
        }
        .blue-strip {
            background-color: #285fac;
            width: 100%;
            padding: 2px 30px;
            height: 20px;
            margin-bottom: 15px;
            color: #fff;
            font-size: 11px;
        }
        
        /* Grid */
        .info-grid {
            width: 100%;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .info-grid td { vertical-align: top; }
        
        .section-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 15px 0;
        }

        /* Tables */
        .table-bordered {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #c2c2c2;
            margin-bottom: 20px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #c2c2c2;
            padding: 8px 10px;
        }
        .table-bordered th {
            font-weight: bold;
            text-align: left;
            background-color: #fcfcfc;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .lbl { color: #555; }
        .val { font-weight: bold; }
        
        /* Totals Area */
        .totals-label { font-weight: bold; text-align: right; padding-right: 15px; }
        .totals-val { font-weight: bold; text-align: right; }
        
        /* Signatures */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-table td {
            vertical-align: bottom;
        }
        .sig-block {
            display: inline-block;
            text-align: center;
        }
        .sig-img {
            max-height: 40px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    
    {{-- Background Logo Watermark --}}
    @if($company->logo)
        <div class="watermark"><img src="{{ public_path('storage/' . $company->logo) }}"></div>
    @endif

    {{-- HEADER AREA --}}
    <div class="header-section">
        @if($showHeader)
            <div class="header-content">
                <table width="100%">
                    <tr>
                        <td width="12%" valign="middle">
                            @if($company->logo)
                                <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height: 70px;">
                            @else
                                <div style="height: 70px; width: 70px; background: #285fac; color: #fff; border-radius: 50%; text-align: center; line-height: 70px; font-size: 30px; font-weight: bold;">+</div>
                            @endif
                        </td>
                        <td width="88%" valign="middle" align="left">
                            <h1 style="font-size: 28px; color: #285fac; text-transform: uppercase;">{{ $company->name }}</h1>
                            <div style="font-size: 13px; font-weight: bold; margin-top: 5px;">
                                <span style="color: #285fac;">&#10004; {{ $company->tagline ?? 'Accurate | Caring | Instant' }}</span>
                                @if($company->phone)
                                    <span style="margin-left: 15px; color: #444;">&#9742; {{ $company->phone }}</span>
                                @endif
                                @if($company->email)
                                    <span style="margin-left: 15px; color: #444;">&#9993; {{ $company->email }}</span>
                                @endif
                            </div>
                            <div style="font-size: 11px; margin-top: 5px; color: #666;">
                                {{ $company->address }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="blue-strip">
                @if($company->website)
                    <div style="text-align: right;">{{ $company->website }}</div>
                @endif
            </div>
        @endif
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        
        {{-- For cases where we don't have header but want the layout spacing, use border-top: 5px --}}
        @if(!$showHeader)
            <div style="padding-top: 20px;"></div>
        @endif
        
        <!-- 3-Column Patient & Booking Info -->
        <table class="info-grid">
            <tr>
                <td style="width: 40%; border-right: 1px solid #ccc; padding-right: 15px;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 8px;">{{ $invoice->patient->name }}</div>
                    <div style="margin-bottom: 3px;"><span class="lbl">Age : </span><span class="val">{{ $invoice->patient->patientProfile->age ?? '--' }} {{ $invoice->patient->patientProfile->age_type ?? 'Years' }}</span></div>
                    <div style="margin-bottom: 3px;"><span class="lbl">Sex : </span><span class="val">{{ $invoice->patient->patientProfile->gender ?? 'Unspecified' }}</span></div>
                    <div><span class="lbl">UHID : </span><span class="val">{{ $invoice->patient->formatted_id }}</span></div>
                </td>
                
                <td style="width: 20%; text-align: center; border-right: 1px solid #ccc; vertical-align: middle;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(url('/')) }}" style="width: 80px; height: 80px;">
                </td>
                
                <td style="width: 40%; padding-left: 20px;">
                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 8px;">Booking</div>
                    <div style="margin-bottom: 3px;"><span class="lbl">Booking Date : </span><span class="val">{{ $invoice->invoice_date->format('d.m.Y') }}</span></div>
                    <div style="margin-bottom: 3px;"><span class="lbl">Booking No. : </span><span class="val">{{ $invoice->invoice_number }}</span></div>
                    <div><span class="lbl">Reference Doctor: </span><span class="val">{{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</span></div>
                </td>
            </tr>
        </table>

        <!-- Invoice Title -->
        <h2 class="section-title">Invoice</h2>

        <!-- Items/Particulars Table -->
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>Particulars</th>
                    <th class="text-center">Invoice Date</th>
                    <th class="text-right">Rate (Rs)</th>
                    <th class="text-center">QTY</th>
                    <th class="text-right">Amount (Rs)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->test_name }}</td>
                    <td class="text-center">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                    <td class="text-center">1.00</td>
                    <td class="text-right">{{ number_format($item->mrp, 2) }}</td>
                </tr>
                @endforeach
                
                <!-- Totals inside the table structure -->
                <tr>
                    <td colspan="2" class="totals-label">Total</td>
                    <td class="totals-val">{{ number_format($invoice->subtotal, 2) }}</td>
                    <td class="text-center" style="font-weight: bold;">{{ number_format($invoice->items->count(), 2) }}</td>
                    <td class="totals-val">{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                
                <tr>
                    <td colspan="4" class="totals-label">Bill Amount:</td>
                    <td class="totals-val">{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                
                @php
                    $discount = $invoice->discount_amount + $invoice->membership_discount_amount + $invoice->voucher_discount_amount;
                @endphp
                @if($discount > 0)
                <tr>
                    <td colspan="4" class="totals-label">Discount Amount:</td>
                    <td class="totals-val">{{ number_format($discount, 2) }}</td>
                </tr>
                @endif
                
                <tr>
                    <td colspan="4" class="totals-label">Final Bill Amount:</td>
                    <td class="totals-val">{{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="totals-label">Paid Amount:</td>
                    <td class="totals-val">{{ number_format($invoice->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="totals-label">Due Amount:</td>
                    <td class="totals-val">{{ number_format($invoice->due_amount, 2) }}</td>
                </tr>
                
                <tr>
                    <td colspan="5" style="padding: 10px;">
                        <strong>Received with Thanks:</strong> Rs. {{ getIndianCurrency($invoice->paid_amount) }} Only
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Payment History Table -->
        @if($invoice->payments->count() > 0)
        <h2 class="section-title">Payment</h2>
        <table class="table-bordered">
            <thead>
                <tr>
                    <th class="text-center">SN</th>
                    <th>Receipt No</th>
                    <th>Date</th>
                    <th>Invoice No.</th>
                    <th class="text-right">Amount (Rs)</th>
                    <th>Paymode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $idx => $p)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>RCPT {{ str_pad($p->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td class="text-right">{{ number_format($p->amount, 2) }}</td>
                    <td>{{ $p->paymentMode->name ?? 'N/A' }}</td>
                </tr>
                @endforeach
                
                <tr>
                    <td colspan="6" style="padding: 10px;">
                        <strong>Received with Thanks:</strong> Rs. {{ getIndianCurrency($invoice->payments->sum('amount')) }} Only
                    </td>
                </tr>
            </tbody>
        </table>
        @endif

        {{-- Signatures Logic --}}
        @php
            $sig1Name = \App\Models\Configuration::getFor('authorized_signatory_name', 'Authorized Signatory');
            $sig1Desig = \App\Models\Configuration::getFor('authorized_signatory_designation', 'Pathologist');
            $sig1Path = \App\Models\Configuration::getFor('signature_image', null);
            
            $sig2Name = \App\Models\Configuration::getFor('global_sig_2_name', 'Accountant');
            $sig2Desig = \App\Models\Configuration::getFor('global_sig_2_desig', '');
            $sig2Path = \App\Models\Configuration::getFor('global_sig_2_path', null);
        @endphp

        <table class="signature-table">
            <tr>
                <td style="width: 50%; text-align: left; padding-left: 10px;">
                    <div class="sig-block">
                        @if($sig1Path)
                            <img src="{{ public_path('storage/' . $sig1Path) }}" class="sig-img"><br>
                        @else
                            <div style="height: 40px;"></div>
                        @endif
                        <strong>{{ $sig1Name }}</strong><br>
                        <span style="font-size: 11px;">({{ $sig1Desig }})</span>
                    </div>
                </td>
                <td style="width: 50%; text-align: right; padding-right: 10px;">
                    <div class="sig-block" style="text-align: center;">
                        @if($sig2Path)
                            <img src="{{ public_path('storage/' . $sig2Path) }}" class="sig-img"><br>
                        @else
                            <div style="font-family: 'Brush Script MT', cursive; font-size: 24px; color: #444; height: 35px; line-height: 35px; margin-bottom: 5px;">{{ $sig2Name }}</div>
                        @endif
                        <strong>{{ $sig2Name }}</strong>
                        @if($sig2Desig)
                            <br><span style="font-size: 11px;">({{ $sig2Desig }})</span>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER AREA --}}
    <div class="footer-section">
        @if($showFooter)
            <div class="blue-strip" style="position: absolute; bottom: 0; margin-bottom: 0;">
                @if($company->website)
                    <div style="float: left;">{{ $company->website }}</div>
                @endif
                <div style="float: right;">Generated on: {{ now()->format('d/m/Y H:i') }}</div>
                <div style="clear: both;"></div>
            </div>
        @endif
    </div>

</div>

</body>
</html>

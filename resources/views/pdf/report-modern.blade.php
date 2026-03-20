<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lab Report - {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: {{ $settings['pdf_show_header'] ? '130px' : '30px' }} 30px {{ $settings['pdf_show_footer'] ? '100px' : '30px' }} 30px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #111;
            line-height: 1.4;
        }

        /* Header logic */
        header {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            height: 100px;
            border-bottom: 2px solid #14b8a6;
            padding-bottom: 5px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-logo {
            display: table-cell;
            width: 25%;
            vertical-align: middle;
        }

        .header-text {
            display: table-cell;
            width: 75%;
            text-align: right;
            vertical-align: middle;
        }

        /* Footer logic */
        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 60px;
            font-size: 9px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* Custom Header/Footer Images */
        .custom-header-img { width: 100%; max-height: 100px; object-fit: contain; }
        .custom-footer-img { width: 100%; max-height: 60px; object-fit: contain; }

        /* Patient Demographics Box */
        .patient-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
        }

        .patient-box td {
            padding: 5px 10px;
            border: 1px solid #eee;
        }
        
        .patient-box .lbl {
            font-weight: bold;
            color: #555;
            width: 15%;
            background: #fafafa;
        }
        .patient-box .val {
            width: 35%;
            font-weight: bold;
        }

        /* Results Table */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .results-table th {
            background-color: #f3f4f6;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            font-weight: bold;
            color: #333;
        }

        .results-table td {
            padding: 6px 8px;
            border-bottom: 1px dashed #eee;
        }

        /* Department Header */
        .dept-header {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            background: #14b8a6;
            color: white;
            padding: 4px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-radius: 3px;
            letter-spacing: 1px;
        }

        .test-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            padding-top: 10px;
            padding-bottom: 5px;
        }

        /* Abnormal Flags */
        .text-danger {
            color: #d90429;
            font-weight: bold;
        }
        
        .bg-abnormal {
            background-color: #ffe5e5;
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
        }

        /* QR / Barcode Placeholder */
        .barcode {
            text-align: center;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10px;
            padding: 5px;
            border: 1px dashed #ccc;
            background: #fafafa;
        }

        /* Signatory */
        .signature-box {
            width: 300px;
            float: right;
            text-align: right;
            margin-top: 40px;
        }
        .signature-img {
            max-height: 50px;
            max-width: 150px;
            margin-bottom: 5px;
        }
        
        .end-of-report {
            text-align: center;
            font-weight: bold;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px dashed #333;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Interpretation Tables */
        .interpretation-block table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 10px;
        }
        .interpretation-block table th {
            background-color: #f3f4f6;
            padding: 4px 8px;
            text-align: left;
            border: 1px solid #ccc;
            font-weight: bold;
            font-size: 10px;
            color: #333;
        }
        .interpretation-block table td {
            padding: 3px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        .interpretation-block table tr:nth-child(even) {
            background-color: #fafafa;
        }
        
        /* Clearfix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    @if($settings['pdf_show_header'])
        <header>
            @if($settings['pdf_header_image'])
                <img src="{{ public_path('storage/' . $settings['pdf_header_image']) }}" class="custom-header-img" alt="Header">
            @else
                <div class="header-content">
                    <div class="header-logo">
                        <h2>{{ $company->name }}</h2>
                    </div>
                    <div class="header-text">
                        <h3 style="margin: 0; color: #14b8a6;">LABORATORY REPORT</h3>
                        <div>{{ $company->address }}</div>
                        <div>Phone: {{ $company->phone }} | Email: {{ $company->email }}</div>
                    </div>
                </div>
            @endif
        </header>
    @endif

    {{-- FOOTER --}}
    @if($settings['pdf_show_footer'])
        <footer>
            @if($settings['pdf_footer_image'])
                <img src="{{ public_path('storage/' . $settings['pdf_footer_image']) }}" class="custom-footer-img" alt="Footer">
            @else
                <div style="text-align: center;">
                    <strong>{{ $company->name }}</strong> - {{ $company->tagline }}<br>
                    <span style="color: #777;">This is a computer-generated report. Interpretations should be correlated with clinical findings.</span>
                </div>
            @endif
        </footer>
    @endif

    {{-- DEMOGRAPHICS --}}
    <table class="patient-box">
        <tr>
            <td class="lbl">Patient Name</td>
            <td class="val" style="font-size:14px;">{{ $patient->name }}</td>
            <td class="lbl">Registered</td>
            <td class="val">{{ $invoice->created_at->format('d M, Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="lbl">Age / Gender</td>
            <td class="val">{{ $profile->age ?? '--' }} {{ $profile->age_type ?? 'Y' }} / {{ $profile->gender ?? '--' }}</td>
            <td class="lbl">Reported</td>
            <td class="val">{{ $report->approved_at ? $report->approved_at->format('d M, Y h:i A') : 'Pending' }}</td>
        </tr>
        <tr>
            <td class="lbl">Referred By</td>
            <td class="val">{{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
            <td class="lbl">Barcode / SID</td>
            <td class="val">{{ $invoice->barcode ?? $invoice->invoice_number }}</td>
        </tr>
    </table>

    {{-- RESULTS Engine --}}
    
    @foreach($groupedResults as $dept => $tests)
        <div class="dept-header">{{ strtoupper($dept) }}</div>
        
        <table class="results-table">
            <thead>
                <tr>
                    <th style="width: 35%">Investigation</th>
                    <th style="width: 20%">Result</th>
                    <th style="width: 15%">Unit</th>
                    <th style="width: 30%">Reference Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tests as $testName => $results)
                    <tr>
                        <td colspan="4" class="test-title">{{ $testName }}</td>
                    </tr>
                    @foreach($results as $r)
                        <tr>
                            <td style="padding-left: 15px;">{{ $r->parameter_name }}</td>
                            <td>
                                @if($r->is_highlighted)
                                    @php 
                                        $flag = substr($r->status, 0, 1);
                                        $flagText = in_array($flag, ['H', 'L']) ? $flag : '*';
                                    @endphp
                                    <span class="text-danger bg-abnormal">{{ $r->result_value }}</span>
                                    <span class="text-danger" style="font-size: 9px; margin-left: 2px;">{{ $flagText }}</span>
                                @else
                                    <span style="font-weight:bold;">{{ $r->result_value }}</span>
                                @endif
                            </td>
                            <td>{{ $r->unit }}</td>
                            <td><span style="white-space: pre-line;">{{ $r->reference_range }}</span></td>
                        </tr>
                    @endforeach
                    @if($results->first()->labTest->description)
                        <tr>
                            <td colspan="4" style="padding-left: 15px; padding-top: 5px; padding-bottom: 5px; font-size: 10px; color: #555;">
                                <strong>Note:</strong> <br>
                                {!! nl2br(e($results->first()->labTest->description)) !!}
                            </td>
                        </tr>
                    @endif
                    @if($results->first()->labTest->interpretation)
                        <tr>
                            <td colspan="4" class="interpretation-block" style="padding-left: 15px; padding-top: 5px; padding-bottom: 15px; font-size: 11px; color: #333;">
                                <strong>Interpretation:</strong> <br>
                                {!! $results->first()->labTest->interpretation !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- REPORT COMMENTS --}}
    @if($report->comments)
        <div style="margin-top: 20px; padding: 10px; border: 1px dashed #ccc; background-color: #fcfcfc;">
            <strong>Doctor's Comments / Interpretation:</strong><br>
            <span style="font-size: 11px; color: #333;">{!! nl2br(e($report->comments)) !!}</span>
        </div>
    @endif

    {{-- SIGNATURE BLOCK --}}
    <div class="clearfix">
        <div class="signature-box">
            @if($settings['signature_image'])
                <img src="{{ public_path('storage/' . $settings['signature_image']) }}" class="signature-img">
            @endif
            <br>
            <strong>{{ $settings['authorized_signatory_name'] }}</strong><br>
            <span style="color:#555">{{ $settings['authorized_signatory_designation'] }}</span>
        </div>
    </div>

    {{-- END OF REPORT --}}
    <div class="end-of-report">
        *** End Of Report ***
    </div>

</body>
</html>

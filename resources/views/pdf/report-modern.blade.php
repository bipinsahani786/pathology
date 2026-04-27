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
            max-height: 45px;
            max-width: 140px;
            margin-bottom: 3px;
        }

        .signature-row {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            font-size: 10px;
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
        
        /* Watermark */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1000;
            opacity: 0.08;
            text-align: center;
        }

        .watermark img {
            width: 300px;
            filter: grayscale(100%);
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
    {{-- Watermark --}}
    @if(isset($company->logo) && $company->logo)
        <div class="watermark">
            <img src="{{ storage_base64($company->logo) }}">
        </div>
    @elseif(file_exists(public_path('assets/images/healthcare-logo.png')))
        <div class="watermark">
            <img src="{{ public_path('assets/images/healthcare-logo.png') }}">
        </div>
    @endif

    {{-- HEADER --}}
    @if($settings['pdf_show_header'])
        <header>
            @if($settings['pdf_header_image'])
                <img src="{{ $settings['pdf_header_image'] }}" class="custom-header-img" alt="Header">
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
                <img src="{{ $settings['pdf_footer_image'] }}" class="custom-footer-img" alt="Footer">
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
            <td class="val" style="font-size:14px;">{{ $patient->name }} <small style="font-weight:normal;opacity:0.7;">({{ $patient->formatted_id }})</small></td>
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
    
    @foreach($groupedResults as $deptId => $data)
        @php 
            $dept = $data['department'];
            $tests = $data['tests'];
            $deptName = $dept ? $dept->name : 'General';
        @endphp
        <div class="dept-header">{{ strtoupper($deptName) }}</div>
        
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
                @foreach($tests as $testKey => $testData)
                    @php
                        $testName = $testData['name'];
                        $results = $testData['results'];
                        $labTest = $testData['labTest'];
                        $remark = $testData['remark'] ?? '';
                    @endphp
                    <tr>
                        <td colspan="4" class="test-title">
                            {{ $testName }}
                            @if($labTest->method)
                                <span style="font-size: 10px; font-weight: normal; margin-left: 10px; color: #666;">(Method: {{ $labTest->method }})</span>
                            @endif
                        </td>
                    </tr>
                    @foreach($results as $r)
                        <tr>
                            <td style="padding-left: 15px;">
                                <div>{{ $r->parameter_name }}</div>
                                @if($r->method)
                                    <div style="font-size: 8px; color: #777; font-style: italic;">Method: {{ $r->method }}</div>
                                @endif
                            </td>
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
                    @if($labTest->description)
                        <tr>
                            <td colspan="4" style="padding-left: 15px; padding-top: 5px; padding-bottom: 5px; font-size: 10px; color: #555;">
                                <strong>Note:</strong> <br>
                                {!! nl2br(e($labTest->description)) !!}
                            </td>
                        </tr>
                    @endif
                    @if($labTest->interpretation)
                        <tr>
                            <td colspan="4" class="interpretation-block" style="padding-left: 15px; padding-top: 5px; padding-bottom: 15px; font-size: 11px; color: #333;">
                                <strong>Interpretation:</strong> <br>
                                {!! $labTest->interpretation !!}
                            </td>
                        </tr>
                    @endif

                    <!-- Display Test specific remarks (Granular) -->
                    @if(!empty($remark))
                        <tr>
                            <td colspan="4" class="interpretation-block" style="padding-left: 15px; padding-top: 5px; padding-bottom: 15px; font-size: 11px; color: #333; background: #fafafa; border: 1px dotted #ccc;">
                                <strong>Feedback / Remarks:</strong> <br>
                                {!! $remark !!}
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- Per-Department Signatures --}}
        @if($settings['report_signature_mode'] == 'per_department' && $dept)
            <div class="signature-row" style="margin-top: 10px; margin-bottom: 20px;">
                @if($dept->sig_1_path)
                    <div class="signature-col">
                        <img src="{{ public_path('storage/' . $dept->sig_1_path) }}" class="signature-img"><br>
                        <strong>{{ $dept->sig_1_name }}</strong><br>
                        {{ $dept->sig_1_desig }}
                    </div>
                @endif
                @if($dept->sig_2_path)
                    <div class="signature-col">
                        <img src="{{ public_path('storage/' . $dept->sig_2_path) }}" class="signature-img"><br>
                        <strong>{{ $dept->sig_2_name }}</strong><br>
                        {{ $dept->sig_2_desig }}
                    </div>
                @endif
                @if($dept->sig_3_path)
                    <div class="signature-col">
                        <img src="{{ public_path('storage/' . $dept->sig_3_path) }}" class="signature-img"><br>
                        <strong>{{ $dept->sig_3_name }}</strong><br>
                        {{ $dept->sig_3_desig }}
                    </div>
                @endif
            </div>
        @endif
    @endforeach

    {{-- REPORT COMMENTS --}}
    @if($report->comments)
        <div style="margin-top: 20px; padding: 10px; border: 1px dashed #ccc; background-color: #fcfcfc;">
            <strong>Doctor's Comments / Interpretation:</strong><br>
            <span style="font-size: 11px; color: #333;">{!! $report->comments !!}</span>
        </div>
    @endif

    {{-- SIGNATURE BLOCK (Global Bottom) --}}
    @if($settings['report_signature_mode'] == 'global_bottom')
        <div class="signature-row">
            @if($settings['global_sig_1_path'])
                <div class="signature-col">
                    <img src="{{ $settings['global_sig_1_path'] }}" class="signature-img"><br>
                    <strong>{{ $settings['global_sig_1_name'] }}</strong><br>
                    {{ $settings['global_sig_1_desig'] }}
                </div>
            @endif

            @if($settings['global_sig_2_path'])
                <div class="signature-col">
                    <img src="{{ $settings['global_sig_2_path'] }}" class="signature-img"><br>
                    <strong>{{ $settings['global_sig_2_name'] }}</strong><br>
                    {{ $settings['global_sig_2_desig'] }}
                </div>
            @endif

            @if($settings['global_sig_3_path'])
                <div class="signature-col">
                    <img src="{{ $settings['global_sig_3_path'] }}" class="signature-img"><br>
                    <strong>{{ $settings['global_sig_3_name'] }}</strong><br>
                    {{ $settings['global_sig_3_desig'] }}
                </div>
            @endif
        </div>
    @endif

    {{-- END OF REPORT --}}
    <div class="end-of-report">
        *** End Of Report ***
    </div>

</body>
</html>

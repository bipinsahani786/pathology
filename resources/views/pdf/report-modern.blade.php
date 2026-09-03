<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Lab Report - {{ $invoice->invoice_number }}</title>
    @php
        $sigMode = $settings['report_signature_mode'] ?? 'global_bottom';
        $showSignaturesEveryPage = ($settings['pdf_show_signatures'] ?? true) && ($sigMode === 'every_page');
        $letterheadImgSrc = $settings['pdf_letterhead_image'] ?? null;
        $letterheadMode = $settings['pdf_letterhead_mode'] ?? 'separate';
        
        $footerMargin = '30px';
        if ($settings['pdf_show_footer']) {
            $footerMargin = $showSignaturesEveryPage ? '190px' : '100px';
        } elseif ($showSignaturesEveryPage) {
            $footerMargin = '130px';
        }

        // ── Vertical Spacing from Settings (supports negative & positive) ──
        $verticalSpacing = (int) ($settings['report_vertical_spacing'] ?? 0);
        $headingSpacing = (int) ($settings['report_heading_spacing'] ?? 0);
        $patientInfoSpacing = (int) ($settings['report_patient_info_spacing'] ?? 0);

        $patientBoxMarginBottom = max(4, 20 + $headingSpacing);
        $patientTdPadY = max(1, 5 + round($patientInfoSpacing * 0.5));
        $patientTdPadX = max(4, 10 + round($patientInfoSpacing * 0.5));

        $tdPadY = max(0, 6 + $verticalSpacing);
        $thPadY = max(2, 8 + round($verticalSpacing * 0.4));
        $rowLineHeight = $verticalSpacing < 0 
            ? max(0.85, round(1.35 + ($verticalSpacing * 0.035), 2)) 
            : round(1.35 + ($verticalSpacing * 0.02), 2);
        $deptMarginTop = max(3, 20 + $headingSpacing);
        $deptMarginBottom = max(2, 10 + round($headingSpacing * 0.5));
        $testTitlePadTop = max(2, 10 + round($headingSpacing * 0.5));
        $testTitlePadBottom = max(1, 5 + round($headingSpacing * 0.3));
    @endphp
    <style>
        @page {
            margin: {{ $settings['pdf_show_header'] ? '130px' : '30px' }} 30px {{ $footerMargin }} 30px;
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
            bottom: -{{ $showSignaturesEveryPage ? ($settings['pdf_show_footer'] ? '170px' : '110px') : '80px' }};
            left: 0;
            right: 0;
            height: {{ $showSignaturesEveryPage ? ($settings['pdf_show_footer'] ? '150px' : '90px') : '60px' }};
            font-size: 9px;
            color: #555;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        /* Custom Header/Footer Images */
        .custom-header-img { width: 100% !important; min-width: 100% !important; display: block; }
        .custom-footer-img { width: 100% !important; min-width: 100% !important; display: block; }

        /* Patient Demographics Box */
        .patient-box {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
            margin-bottom: {{ $patientBoxMarginBottom }}px;
            border: 1px solid #ccc;
        }

        .patient-box td {
            padding: {{ $patientTdPadY }}px {{ $patientTdPadX }}px;
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
            padding: {{ $thPadY }}px 8px;
            text-align: left;
            border-bottom: 1px solid #ccc;
            font-weight: bold;
            color: #333;
            line-height: {{ $rowLineHeight }};
        }

        .results-table td {
            padding: {{ $tdPadY }}px 8px;
            border-bottom: 1px dashed #eee;
            line-height: {{ $rowLineHeight }};
        }

        /* Department Header */
        .test-header-group {
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }

        .keep-together {
            page-break-inside: avoid !important;
        }

        .dept-header {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            background: #14b8a6;
            color: white;
            padding: 4px;
            margin-top: {{ $deptMarginTop }}px;
            margin-bottom: {{ $deptMarginBottom }}px;
            border-radius: 3px;
            letter-spacing: 1px;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }

        .test-title {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
            padding-top: {{ $testTitlePadTop }}px;
            padding-bottom: {{ $testTitlePadBottom }}px;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
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

        .page-num:before {
            content: counter(page);
        }
    </style>
</head>
<body>
    {{-- ══════════════════ LETTERHEAD BACKGROUND ══════════════════ --}}
    @if($letterheadMode === 'full_background' && $letterheadImgSrc && $showHeader)
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1001;">
            <img src="{{ $letterheadImgSrc }}" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
    @endif

    {{-- Watermark --}}
    @if($settings['pdf_show_watermark'] ?? true)
        @if(isset($company->logo) && $company->logo)
            <div class="watermark">
                <img src="{{ storage_base64($company->logo) }}">
            </div>
        @elseif(file_exists(public_path('assets/images/healthcare-logo.png')))
            <div class="watermark">
                <img src="{{ public_path('assets/images/healthcare-logo.png') }}">
            </div>
        @endif
    @endif

    {{-- HEADER --}}
    @if($settings['pdf_show_header'])
        <header>
            @if($letterheadMode === 'separate' && $settings['pdf_header_image'] && $showHeader)
                <img src="{{ $settings['pdf_header_image'] }}" class="custom-header-img" alt="Header">
            @elseif($showHeader)
                @php
                    $displayLabName = ($invoice->branch && $invoice->branch->name) ? $invoice->branch->name : $company->name;
                    $displayAddress = ($invoice->branch && $invoice->branch->address) ? $invoice->branch->address : ($company->address ?? '');
                    $displayPhone = ($invoice->branch && $invoice->branch->contact_number) ? $invoice->branch->contact_number : ($company->phone ?? '');
                @endphp
                <div class="header-content">
                    <div class="header-logo">
                        <h2>{{ $displayLabName }}</h2>
                    </div>
                    <div class="header-text">
                        <h3 style="margin: 0; color: #14b8a6;">LABORATORY REPORT</h3>
                        <div>{{ $displayAddress }}</div>
                        <div>Phone: {{ $displayPhone }} | Email: {{ $company->email }}</div>
                    </div>
                </div>
            @endif
        </header>
    @endif

    {{-- FOOTER --}}
    @if($settings['pdf_show_footer'] || $showSignaturesEveryPage)
        <footer>
            @if($showSignaturesEveryPage)
                @php
                    $leftSig = null;
                    $centerSig = null;
                    $rightSig = null;

                    if ($settings['sig_1_enabled'] ?? true) {
                        $pos = $settings['sig_1_position'] ?? 'right';
                        $sigData = ['name' => $settings['global_sig_1_name'], 'desig' => $settings['global_sig_1_desig'], 'img' => $settings['global_sig_1_path']];
                        if ($pos === 'left') $leftSig = $sigData;
                        elseif ($pos === 'center') $centerSig = $sigData;
                        else $rightSig = $sigData;
                    }
                    if (($settings['sig_2_enabled'] ?? true) && $settings['global_sig_2_name']) {
                        $pos = $settings['sig_2_position'] ?? 'left';
                        $sigData = ['name' => $settings['global_sig_2_name'], 'desig' => $settings['global_sig_2_desig'], 'img' => $settings['global_sig_2_path']];
                        if ($pos === 'left') $leftSig = $sigData;
                        elseif ($pos === 'center') $centerSig = $sigData;
                        else $rightSig = $sigData;
                    }
                    if (($settings['sig_3_enabled'] ?? true) && $settings['global_sig_3_name']) {
                        $pos = $settings['sig_3_position'] ?? 'center';
                        $sigData = ['name' => $settings['global_sig_3_name'], 'desig' => $settings['global_sig_3_desig'], 'img' => $settings['global_sig_3_path']];
                        if ($pos === 'left') $leftSig = $sigData;
                        elseif ($pos === 'center') $centerSig = $sigData;
                        else $rightSig = $sigData;
                    }
                @endphp
                <div class="signature-row" style="margin-top: 0px; margin-bottom: 5px;">
                    <!-- Left -->
                    <div class="signature-col" style="text-align: left; padding-left: 20px;">
                        @if($leftSig)
                            @if($leftSig['img'])
                                <img src="{{ $leftSig['img'] }}" class="signature-img" style="max-height: 35px;"><br>
                            @else
                                <div style="height: 35px;"></div>
                            @endif
                            <strong>{!! nl2br(e($leftSig['name'])) !!}</strong><br>
                            <span style="font-size: 8px; color: #555;">{!! nl2br(e($leftSig['desig'])) !!}</span>
                        @endif
                    </div>
                    <!-- Center -->
                    <div class="signature-col" style="text-align: center;">
                        @if($centerSig)
                            @if($centerSig['img'])
                                <img src="{{ $centerSig['img'] }}" class="signature-img" style="max-height: 35px;"><br>
                            @else
                                <div style="height: 35px;"></div>
                            @endif
                            <strong>{!! nl2br(e($centerSig['name'])) !!}</strong><br>
                            <span style="font-size: 8px; color: #555;">{!! nl2br(e($centerSig['desig'])) !!}</span>
                        @endif
                    </div>
                    <!-- Right -->
                    <div class="signature-col" style="text-align: right; padding-right: 20px;">
                        @if($rightSig)
                            @if($rightSig['img'])
                                <img src="{{ $rightSig['img'] }}" class="signature-img" style="max-height: 35px;"><br>
                            @else
                                <div style="height: 35px;"></div>
                            @endif
                            <strong>{!! nl2br(e($rightSig['name'])) !!}</strong><br>
                            <span style="font-size: 8px; color: #555;">{!! nl2br(e($rightSig['desig'])) !!}</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($showFooter)
                @if($letterheadMode === 'separate' && $settings['pdf_footer_image'])
                    <img src="{{ $settings['pdf_footer_image'] }}" class="custom-footer-img" alt="Footer">
                @else
                    <div style="text-align: center;">
                        <strong>{{ ($invoice->branch && $invoice->branch->name) ? $invoice->branch->name : $company->name }}</strong> - {{ $company->tagline }}<br>
                        <span style="color: #777;">This is a computer-generated report. Interpretations should be correlated with clinical findings.</span>
                    </div>
                @endif
            @endif
        </footer>
    @endif

    {{-- DEMOGRAPHICS --}}
    <table class="patient-box">
        <tr>
            <td class="lbl">Patient Name</td>
            <td class="val" style="font-size:14px;">{{ $patient->name }} <small style="font-weight:normal;opacity:0.7;">({{ $patient->formatted_id }})</small></td>
            <td class="lbl">Registered</td>
            <td class="val">{{ $invoice->created_at->format(($settings['pdf_show_time'] ?? true) ? 'd M, Y h:i A' : 'd M, Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Age / Gender</td>
            <td class="val">{{ $profile->age ?? '--' }} {{ $profile->age_type == 'Years' ? 'Y' : ($profile->age_type == 'Months' ? 'M' : 'D') }} / {{ $profile->gender ?? '--' }}</td>
            <td class="lbl">Reported</td>
            <td class="val">{{ $report->approved_at ? $report->approved_at->format(($settings['pdf_show_time'] ?? true) ? 'd M, Y h:i A' : 'd M, Y') : 'Pending' }}</td>
        </tr>
        <tr>
            <td class="lbl">Referred By</td>
            <td class="val">{{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
            <td class="lbl">Barcode / SID</td>
            <td class="val">{{ $invoice->barcode ?? $invoice->invoice_number }}</td>
        </tr>
    </table>

    {{-- RESULTS Engine --}}
    
    @php $testIndex = 0; @endphp
    @foreach($groupedResults as $deptId => $data)
        @php 
            $dept = $data['department'];
            $tests = $data['tests'];
            $deptName = $dept ? $dept->name : 'General';
            $style = $settings['report_page_break_style'] ?? 'continuous';
            $showDeptAlways = $settings['report_show_dept_header_always'] ?? true;
            $isFirstInDept = $loop->first;
        @endphp

        @foreach($tests as $testKey => $testData)
            @php
                $testName = $testData['name'];
                $results = $testData['results'];
                $labTest = $testData['labTest'];
                $remark = $testData['remark'] ?? '';
                $isFirstTestInDept = $loop->first;
            @endphp

            @if($testIndex > 0)
                @if($style === 'test_per_page')
                    <div style="page-break-after: always;"></div>
                @elseif($style === 'department_per_page' && $isFirstTestInDept)
                    <div style="page-break-after: always;"></div>
                @endif
            @endif

            @php
                $testParamCount = $results->count();
                $keepEntireTestTogether = $testParamCount <= 15;
            @endphp

            <div class="test-block-wrapper {{ $keepEntireTestTogether ? 'keep-together' : '' }}" style="margin-bottom: 30px; clear: both; {{ $keepEntireTestTogether ? 'page-break-inside: avoid !important;' : '' }}">
                <div class="test-header-group">
                    @if($showDeptAlways || $isFirstTestInDept)
                        <div class="dept-header">{{ strtoupper($deptName) }}</div>
                    @endif
                </div>
            
            <table class="results-table">
                @php
                    $isCultureOnly = $results->every(function ($r) {
                        return !empty($r->culture_data);
                    });
                @endphp
                @if(!$isCultureOnly)
                    <thead>
                        <tr>
                            <th style="width: 35%">Investigation</th>
                            <th style="width: 20%">Result</th>
                            <th style="width: 15%">Unit</th>
                            <th style="width: 30%">Reference Value</th>
                        </tr>
                    </thead>
                @endif
                <tbody>
                    <tr>
                        <td colspan="4" class="test-title">
                            {{ $testName }}
                            @if(($settings['pdf_show_test_method'] ?? true) && $labTest->method)
                                <span style="font-size: 10px; font-weight: normal; margin-left: 10px; color: #666;">(Method: {{ $labTest->method }})</span>
                            @endif
                        </td>
                    </tr>
                    @php $inGroup = false; @endphp
                    @foreach($results as $r)
                        @php
                            // Detect sub-header/heading: empty result_value AND empty reference_range
                            $isSubHeader = (is_null($r->result_value) || trim($r->result_value) === '')
                                && (is_null($r->reference_range) || trim($r->reference_range) === '')
                                && empty($r->culture_data);
                            $isEmptyHeading = $isSubHeader && trim($r->parameter_name) === '';
                        @endphp
                        
                        @if($isEmptyHeading)
                            @php $inGroup = false; @endphp
                            {{-- Do not render anything for Group End --}}
                        @elseif($isSubHeader)
                            @php $inGroup = true; @endphp
                            {{-- ── Group Heading / Sub-Header Row ── --}}
                            <tr>
                                <td colspan="4" 
                                    style="padding: 5px 8px 3px 10px; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #1a1a1a; border-bottom: 1px solid #ccc; background: #f5f5f5;">
                                    {{ strtoupper($r->parameter_name) }}
                                </td>
                            </tr>
                        @elseif(!empty($r->culture_data))
                            {{-- ── Culture & Sensitivity Spanned Row ── --}}
                            <tr>
                                <td colspan="4" style="padding: 12px 8px; border-bottom: 1px dashed #eee;">
                                    <div>
                                        <div style="font-weight: bold; font-size: 12px; color: #000; margin-bottom: 8px; text-transform: uppercase;">
                                            {{ $r->parameter_name }}
                                        </div>
                                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11px;">
                                            <tr>
                                                <td style="width: 30%; font-weight: bold; border: none; padding: 3px 0; color: #555;">Growth Status:</td>
                                                <td style="width: 70%; border: none; padding: 3px 0; color: #000; font-weight: bold;">
                                                    @if(($r->culture_data['growth_status'] ?? '') === 'No Growth')
                                                        No Growth Isolated
                                                    @elseif(($r->culture_data['growth_status'] ?? '') === 'Contamination')
                                                        Mixed Growth (Contamination)
                                                    @else
                                                        Significant Growth Isolated
                                                    @endif
                                                </td>
                                            </tr>
                                            @if(($r->culture_data['growth_status'] ?? 'Growth') !== 'No Growth')
                                                <tr>
                                                    <td style="font-weight: bold; border: none; padding: 3px 0; color: #555;">Organism Isolated:</td>
                                                    <td style="color: #000; font-weight: bold; font-style: italic; border: none; padding: 3px 0;">
                                                        {{ $r->culture_data['organism_name'] ?? 'Not Specified' }}
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="font-weight: bold; border: none; padding: 3px 0; color: #555;">Colony Count:</td>
                                                    <td style="border: none; padding: 3px 0; color: #000;">
                                                        {{ $r->culture_data['colony_count'] ?? 'Not Specified' }}
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>

                                        @if(($r->culture_data['growth_status'] ?? 'Growth') !== 'No Growth' && !empty($r->culture_data['antibiotics']))
                                            <div style="margin-top: 15px;">
                                                <div style="font-weight: bold; font-size: 11px; border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; color: #000;">
                                                    Antibiotic Susceptibility Profile
                                                </div>
                                                <table style="width: 100%; border-collapse: collapse; font-size: 10px; text-align: left;">
                                                    <thead>
                                                        <tr style="border-bottom: 1.5px solid #000; font-weight: bold; color: #000;">
                                                            <th style="padding: 6px 4px; width: 45%; border: none;">Antibiotic Name</th>
                                                            <th style="padding: 6px 4px; width: 35%; text-align: center; border: none;">Susceptibility</th>
                                                            <th style="padding: 6px 4px; width: 20%; text-align: center; border: none;">MIC Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php
                                                            $filledAntibiotics = collect($r->culture_data['antibiotics'] ?? [])->filter(function($ab) {
                                                                return !empty($ab['sensitivity']) || !empty($ab['mic']);
                                                            });
                                                        @endphp
                                                        @foreach($filledAntibiotics as $ab)
                                                            @php
                                                                $sens = strtoupper($ab['sensitivity'] ?? 'S');
                                                                $text = 'Sensitive';
                                                                if ($sens === 'R') {
                                                                    $text = 'Resistant';
                                                                } elseif ($sens === 'I') {
                                                                    $text = 'Intermediate';
                                                                }
                                                            @endphp
                                                            <tr style="border-bottom: 0.5px solid #eee;">
                                                                <td style="padding: 6px 4px; font-weight: bold; border: none; color: #000;">{{ $ab['name'] }}</td>
                                                                <td style="padding: 6px 4px; text-align: center; font-weight: bold; border: none; color: #000;">
                                                                    {{ $text }}
                                                                </td>
                                                                <td style="padding: 6px 4px; text-align: center; font-family: monospace; border: none; color: #000;">
                                                                    {{ $ab['mic'] ?: '--' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td style="padding-left: {{ $inGroup ? '25px' : '10px' }};">
                                    <div>{{ $r->parameter_name }}</div>
                                    @if(($settings['pdf_show_test_method'] ?? true) && $r->method)
                                        <div style="font-size: 8px; color: #777; font-style: italic;">Method: {{ $r->method }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($r->is_highlighted)
                                        @php 
                                            $flag = strtoupper(trim(substr($r->status ?? '', 0, 1)));
                                            $flagText = in_array($flag, ['H', 'L']) ? $flag : ($settings['report_abnormal_indicator'] ?? '*');
                                            $flagColor = $flag === 'H' ? ($settings['report_flag_high_color'] ?? '#cc0000') : ($flag === 'L' ? ($settings['report_flag_low_color'] ?? '#0055aa') : ($settings['report_abnormal_color'] ?? '#d32f2f'));
                                        @endphp
                                        <span class="bg-abnormal" style="color: {{ $flagColor }}; font-weight: bold;">{{ $r->result_value }}</span>
                                        <span style="color: {{ $flagColor }}; font-weight: bold; font-size: 9px; margin-left: 2px;">{{ $flagText }}</span>
                                    @else
                                        <span style="font-weight:bold;">{{ $r->result_value }}</span>
                                    @endif
                                </td>
                                <td>{{ $r->unit }}</td>
                                <td><span style="white-space: pre-line;">{{ $r->reference_range }}</span></td>
                            </tr>
                        @endif
                    @endforeach
                    @if(($settings['report_show_note'] ?? true) && $labTest->description)
                        <tr style="page-break-inside: avoid;">
                            <td colspan="4" style="padding-left: 15px; padding-top: 5px; padding-bottom: 5px; font-size: 10px; color: #555;">
                                <strong>Note:</strong> <br>
                                {!! nl2br(e($labTest->description)) !!}
                            </td>
                        </tr>
                    @endif
                    @if(($settings['report_show_interpretation'] ?? true) && $labTest->interpretation)
                        <tr style="page-break-inside: avoid;">
                            <td colspan="4" class="interpretation-block" style="padding-left: 15px; padding-top: 5px; padding-bottom: 15px; font-size: 11px; color: #333;">
                                <strong>Interpretation:</strong> <br>
                                {!! $labTest->interpretation !!}
                            </td>
                        </tr>
                    @endif
                    @if(!empty($remark))
                        <tr style="page-break-inside: avoid;">
                            <td colspan="4" class="interpretation-block" style="padding-left: 15px; padding-top: 5px; padding-bottom: 15px; font-size: 11px; color: #333; background: #fafafa; border: 1px dotted #ccc;">
                                <strong>Feedback / Remarks:</strong> <br>
                                {!! $remark !!}
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            </div>
            @php $testIndex++; @endphp
        @endforeach

        {{-- Per-Department Signatures --}}
        @if($settings['report_signature_mode'] == 'per_department' && $dept)
            <div class="signature-row" style="margin-top: 10px; margin-bottom: 20px;">
                @if($dept->sig_1_name || $dept->sig_1_path)
                    <div class="signature-col">
                        @if($dept->sig_1_path)
                            <img src="{{ public_path('storage/' . $dept->sig_1_path) }}" class="signature-img"><br>
                        @else
                            <div style="height: 50px;"></div>
                        @endif
                        <strong>{!! nl2br(e($dept->sig_1_name)) !!}</strong><br>
                        {!! nl2br(e($dept->sig_1_desig)) !!}
                    </div>
                @endif
                @if($dept->sig_2_name || $dept->sig_2_path)
                    <div class="signature-col">
                        @if($dept->sig_2_path)
                            <img src="{{ public_path('storage/' . $dept->sig_2_path) }}" class="signature-img"><br>
                        @else
                            <div style="height: 50px;"></div>
                        @endif
                        <strong>{!! nl2br(e($dept->sig_2_name)) !!}</strong><br>
                        {!! nl2br(e($dept->sig_2_desig)) !!}
                    </div>
                @endif
                @if($dept->sig_3_name || $dept->sig_3_path)
                    <div class="signature-col">
                        @if($dept->sig_3_path)
                            <img src="{{ public_path('storage/' . $dept->sig_3_path) }}" class="signature-img"><br>
                        @else
                            <div style="height: 50px;"></div>
                        @endif
                        <strong>{!! nl2br(e($dept->sig_3_name)) !!}</strong><br>
                        {!! nl2br(e($dept->sig_3_desig)) !!}
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

    {{-- SIGNATURE BLOCK (Global Bottom / Last Page Only) --}}
    @if($sigMode == 'last_page' || $sigMode == 'global_bottom')
        @php
            $leftSig = null;
            $centerSig = null;
            $rightSig = null;

            if ($settings['sig_1_enabled'] ?? true) {
                $pos = $settings['sig_1_position'] ?? 'right';
                $sigData = ['name' => $settings['global_sig_1_name'], 'desig' => $settings['global_sig_1_desig'], 'img' => $settings['global_sig_1_path']];
                if ($pos === 'left') $leftSig = $sigData;
                elseif ($pos === 'center') $centerSig = $sigData;
                else $rightSig = $sigData;
            }
            if (($settings['sig_2_enabled'] ?? true) && $settings['global_sig_2_name']) {
                $pos = $settings['sig_2_position'] ?? 'left';
                $sigData = ['name' => $settings['global_sig_2_name'], 'desig' => $settings['global_sig_2_desig'], 'img' => $settings['global_sig_2_path']];
                if ($pos === 'left') $leftSig = $sigData;
                elseif ($pos === 'center') $centerSig = $sigData;
                else $rightSig = $sigData;
            }
            if (($settings['sig_3_enabled'] ?? true) && $settings['global_sig_3_name']) {
                $pos = $settings['sig_3_position'] ?? 'center';
                $sigData = ['name' => $settings['global_sig_3_name'], 'desig' => $settings['global_sig_3_desig'], 'img' => $settings['global_sig_3_path']];
                if ($pos === 'left') $leftSig = $sigData;
                elseif ($pos === 'center') $centerSig = $sigData;
                else $rightSig = $sigData;
            }
        @endphp
        <div class="signature-row" style="page-break-inside: avoid;">
            <!-- Left -->
            <div class="signature-col" style="text-align: left; padding-left: 20px;">
                @if($leftSig)
                    @if($leftSig['img'])
                        <img src="{{ $leftSig['img'] }}" class="signature-img"><br>
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <strong>{!! nl2br(e($leftSig['name'])) !!}</strong><br>
                    {!! nl2br(e($leftSig['desig'])) !!}
                @endif
            </div>
            <!-- Center -->
            <div class="signature-col" style="text-align: center;">
                @if($centerSig)
                    @if($centerSig['img'])
                        <img src="{{ $centerSig['img'] }}" class="signature-img"><br>
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <strong>{!! nl2br(e($centerSig['name'])) !!}</strong><br>
                    {!! nl2br(e($centerSig['desig'])) !!}
                @endif
            </div>
            <!-- Right -->
            <div class="signature-col" style="text-align: right; padding-right: 20px;">
                @if($rightSig)
                    @if($rightSig['img'])
                        <img src="{{ $rightSig['img'] }}" class="signature-img"><br>
                    @else
                        <div style="height: 50px;"></div>
                    @endif
                    <strong>{!! nl2br(e($rightSig['name'])) !!}</strong><br>
                    {!! nl2br(e($rightSig['desig'])) !!}
                @endif
            </div>
        </div>
    @endif

    {{-- END OF REPORT --}}
    <div class="end-of-report">
        *** End Of Report ***
    </div>

    @if($settings['pdf_show_page_number'] ?? true)
        <div style="position: fixed; bottom: -70px; right: 30px; font-size: 10px; color: #333; font-family: sans-serif; font-weight: bold; z-index: 10000; background-color: {{ $settings['pdf_page_number_bg_color'] ?? 'rgba(255, 255, 255, 0.85)' }}; padding: 3px 8px; border-radius: 4px; border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            Page <span class="page-num"></span>
        </div>
    @endif

</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lab Report - {{ $invoice->invoice_number }}</title>

    @php
        // ── Resolve image paths ──
        $headerImgSrc = $settings['pdf_header_image'] ?? null;
        $footerImgSrc = $settings['pdf_footer_image'] ?? null;
        $letterheadImgSrc = $settings['pdf_letterhead_image'] ?? null;
        $letterheadMode = $settings['pdf_letterhead_mode'] ?? 'separate';

        $sigImgSrc = $settings['global_sig_1_path'] ?? null;

        // ── Margins from Settings ──
        $marginTopVal = (int) ($settings['pdf_margin_top'] ?? 310);
        $marginBottomVal = (int) ($settings['pdf_margin_bottom'] ?? 255);
        $marginTop = $marginTopVal . 'px';
        $marginBottom = $marginBottomVal . 'px';
        $headerHeight = ($settings['pdf_header_height'] ?? 200) . 'px';
        $footerHeight = ($settings['pdf_footer_height'] ?? 180) . 'px';

        // DomPDF A4 page height is ~1122px. Calculate max content height.
        // Subtract 80px extra buffer to prevent the image from pushing itself to the next page
        $maxImgHeight = (1122 - $marginTopVal - $marginBottomVal - 80) . 'px';

        $baseFontSize = (int) ($settings['pdf_font_size'] ?? 13);
        $fontSize = $baseFontSize . 'px';
        $fontFamily = $settings['pdf_font_family'] ?? 'Helvetica, Arial, sans-serif';

        // Scale factor: all child font-sizes scale proportionally to the user's chosen size
        // Default base is 13px, so if user picks 16px, scale = 16/13 ≈ 1.23
        $scale = $baseFontSize / 13;

        // Pre-compute scaled sizes for CSS (rounded to 1 decimal)
        $sz8 = round(8 * $scale, 1) . 'px';
        $sz8_5 = round(8.5 * $scale, 1) . 'px';
        $sz9 = round(9 * $scale, 1) . 'px';
        $sz10 = round(10 * $scale, 1) . 'px';
        $sz10_5 = round(10.5 * $scale, 1) . 'px';
        $sz11 = round(11 * $scale, 1) . 'px';
        $sz11_5 = round(11.5 * $scale, 1) . 'px';
        $sz12 = round(12 * $scale, 1) . 'px';
    @endphp

    <style>
        /* ── RESET ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family:
                {{ $fontFamily }}
            ;
            font-size:
                {{ $fontSize }}
            ;
            color: #1a1a1a;
            background: #fff;
            line-height: 1.45;
            margin:
                {{ $marginTop }}
                25px
                {{ $marginBottom }}
                25px;
        }

        /* ══════════════════════════════════════════════
           FIXED HEADER
           ══════════════════════════════════════════════ */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height:
                {{ $marginTop }}
            ;
            overflow: hidden;
        }

        .header-logo-container {
            width: 100%;
            height:
                {{ $headerHeight }}
            ;
            display: block;
            overflow: hidden;
            text-align: center;
            padding: 0;
        }

        .header-banner {
            width: 100% !important;
            min-width: 100% !important;
            display: block;
        }

        /* ── PATIENT INFO BOX ── */
        .patient-box {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border: 1px solid #1a1a1a !important;
            margin: 0 25px 0;
            padding: 8px 10px;
            font-size:
                {{ $sz10_5 }}
            ;
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

        .barcode {
            margin-top: 5px;
            text-align: center;
        }

        .barcode-img {
            width: 100px;
            height: 30px;
        }

        /* ══════════════════════════════════════════════
           FIXED FOOTER
           ══════════════════════════════════════════════ */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height:
                {{ $footerHeight }}
            ;
        }

        /* Signature Table */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .sig-checked {
            width: 55%;
            text-align: left;
            vertical-align: bottom;
            font-weight: 700;
            font-size:
                {{ $sz11 }}
            ;
            padding-left: 35px;
            padding-bottom: 8px;
        }

        .sig-doctor {
            width: 45%;
            text-align: center;
            vertical-align: bottom;
            padding-right: 35px;
            padding-bottom: 2px;
            line-height: 1.2;
            /* Tighten line height to prevent overlap */
        }

        .sign-img {
            max-height: 55px;
            margin-bottom: 2px;
        }

        .doc-name {
            font-weight: 700;
            font-size:
                {{ $sz11 }}
            ;
            display: block;
            margin-bottom: 0px;
        }

        .doc-desig {
            font-size:
                {{ $sz10 }}
            ;
            color: #333;
            display: block;
            margin-top: 0px;
        }

        .footer-banner {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100% !important;
            min-width: 100% !important;
            display: block;
        }

        /* ── Multi-Signature Row ── */
        .sig-container {
            position: absolute;
            bottom: calc({{ $footerHeight }} + 5px);
            /* Dynamically positioned just above the footer banner */
            left: 0;
            width: 100%;
        }

        .multi-sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .multi-sig-table td {
            text-align: center;
            vertical-align: bottom;
            font-size:
                {{ $sz10 }}
            ;
            padding: 0 15px 5px;
        }

        /* ══════════════════════════════════════════════
           SECTION TITLES
           ══════════════════════════════════════════════ */
        .dept-title {
            text-align: center;
            font-weight: 700;
            font-size:
                {{ $sz12 }}
            ;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin: 10px 0 2px;
            color: #1a1a1a;
        }

        .test-title {
            text-align: center;
            font-weight: 700;
            font-size:
                {{ $sz11 }}
            ;
            text-transform: uppercase;
            margin-bottom: 2px;
            color: #1a1a1a;
        }

        .method-line {
            text-align: center;
            font-size:
                {{ $sz9 }}
            ;
            color: #555;
            font-style: italic;
            margin-bottom: 5px;
        }

        /* ── Barcode Area ── */
        .barcode-area {
            text-align: right;
            margin-bottom: 4px;
            position: relative;
        }

        .barcode-area img {
            height: 45px;
        }

        /* ══════════════════════════════════════════════
           MINIMALIST RESULT TABLE
           ══════════════════════════════════════════════ */
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size:
                {{ $sz10 }}
            ;
        }

        .result-table tr {
            page-break-inside: avoid;
        }

        .result-table thead th {
            border-top: 1.5px solid #333;
            border-bottom: 1.5px solid #333;
            padding: 6px 6px;
            text-align: left;
            font-weight: 700;
            font-size:
                {{ $sz10_5 }}
            ;
            text-transform: uppercase;
            color: #000;
            background: #fbfbfb;
        }

        .result-table tbody td {
            padding: 5px 6px;
            vertical-align: top;
            border-bottom: 0.5px solid #eee;
        }

        /* Explicitly remove vertical lines */
        .result-table,
        .result-table th,
        .result-table td {
            border-left: none !important;
            border-right: none !important;
        }

        .result-table tr:last-child td {
            border-bottom: 1.5px solid #333;
        }

        /* Sub-header rows (section dividers like "TOTAL COUNT") */
        .result-table .sub-hdr td {
            font-weight: 700;
            font-size:
                {{ $sz10 }}
            ;
            text-transform: uppercase;
            padding: 3px 6px 1px;
            color: #1a1a1a;
            border-bottom: none;
        }

        /* Indented parameter rows under sub-headers */
        .result-table .param-indent td:first-child {
            padding-left: 28px;
        }

        /* ── Flag & Abnormal Colors ── */
        .flag-H {
            color:
                {{ $settings['report_flag_high_color'] ?? '#cc0000' }}
            ;
            font-weight: 700;
        }

        .flag-L {
            color:
                {{ $settings['report_flag_low_color'] ?? '#0055aa' }}
            ;
            font-weight: 700;
        }

        .flag-abnormal {
            color:
                {{ $settings['report_abnormal_color'] ?? '#d32f2f' }}
            ;
            font-weight: 700;
        }

        .result-bold {
            font-weight: 700;
        }

        /* ══════════════════════════════════════════════
           INTERPRETATION & REMARKS BLOCKS
           ══════════════════════════════════════════════ */
        .interp-block {
            margin: 15px 0 10px;
            padding: 4px 0;
            font-size:
                {{ $sz10 }}
            ;
            line-height: 1.5;
            page-break-inside: avoid;
        }

        .interp-label {
            font-weight: 700;
            font-size:
                {{ $sz10 }}
            ;
            margin-bottom: 3px;
            color: #1a1a1a;
        }

        .interp-content {
            margin-left: 0;
            padding-left: 0;
        }

        /* Render HTML interpretation tables cleanly */
        .interp-content table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size:
                {{ $sz10 }}
            ;
        }

        .interp-content table th {
            background: #f0f0f0;
            border: 1px solid #bbb;
            padding: 3px 6px;
            font-weight: 700;
            text-align: left;
            font-size:
                {{ $sz10 }}
            ;
        }

        .interp-content table td {
            border: 1px solid #bbb;
            padding: 3px 6px;
            font-size:
                {{ $sz10 }}
            ;
        }

        .interp-content table tr {
            page-break-inside: avoid;
        }

        .interp-content table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .interp-content p {
            margin: 3px 0;
            font-size:
                {{ $sz10 }}
            ;
            color: #444;
        }

        .interp-content ul,
        .interp-content ol {
            margin: 3px 0 3px 15px;
            font-size:
                {{ $sz10 }}
            ;
        }

        .interp-content li {
            margin-bottom: 2px;
        }

        .interp-content strong {
            color: #1a1a1a;
        }

        /* Remarks block (from result entry) */
        .remarks-block {
            margin: 20px 0 10px;
            padding: 10px 0;
            font-size:
                {{ $sz10 }}
            ;
            line-height: 1.5;
            border-top: 1px dashed #ccc;
            page-break-inside: avoid;
        }

        .remarks-block table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size:
                {{ $sz10 }}
            ;
        }

        .remarks-block table th {
            border: 1px solid #bbb;
            padding: 3px 6px;
            font-weight: 700;
            text-align: left;
        }

        .remarks-block table td {
            border: 1px solid #bbb;
            padding: 3px 6px;
        }

        .doctor-comments {
            margin: 25px 0 10px;
            padding: 10px 0;
            border-top: 1.5px solid #eee;
        }

        /* ══════════════════════════════════════════════
           MISC
           ══════════════════════════════════════════════ */
        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1000;
            opacity: 0.08;
        }

        .watermark img {
            width: 280px;
        }

        .page-break {
            page-break-after: always;
        }

        .end-of-report {
            text-align: center;
            font-weight: 700;
            font-size:
                {{ $sz10 }}
            ;
            margin-top: 20px;
            padding-top: 8px;
            border-top: 1px dashed #333;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
            color: #555;
        }

        /* Doctor Comments Block */
        .doctor-comments {
            margin-top: 15px;
            padding: 6px 0;
            border-top: 1px dashed #ccc;
            font-size:
                {{ $sz10 }}
            ;
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

    {{-- ══════════════════ WATERMARK ══════════════════ --}}
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

    {{-- ══════════════════ FIXED HEADER ══════════════════ --}}
    <header>
        <div class="header-logo-container">
            @if($letterheadMode === 'separate' && $headerImgSrc && $showHeader)
                <img class="header-banner" src="{{ $headerImgSrc }}" alt="Header">
            @endif
        </div>

        {{-- Patient Info Box — always visible --}}
        <div class="patient-box" style="margin-top: 0; clear: both;">
            <table class="patient-table">
                <tr>
                    <td class="lbl">Name</td>
                    <td class="val">: {{ $patient->name }}</td>
                    <td class="lbl">Report ID</td>
                    <td class="val">: {{ $invoice->invoice_number }}</td>
                    <td rowspan="4" class="qr-cell">
                        @if(isset($qrCodeUri))
                            <img src="{{ $qrCodeUri }}" class="qr-code">
                        @elseif(file_exists(public_path('assets/images/qr-code.png')))
                            <img src="{{ public_path('assets/images/qr-code.png') }}" class="qr-code">
                        @endif

                        @if(isset($barcodeUri))
                            <div class="barcode">
                                <img src="{{ $barcodeUri }}" class="barcode-img">
                                <div style="font-size: {{ $sz8 }}; margin-top: 1px; font-weight: bold;">
                                    {{ $invoice->invoice_number }}
                                </div>
                            </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Age/Gender</td>
                    <td class="val">: {{ $profile->age ?? '--' }}
                        {{ $profile->age_type == 'Years' ? 'Y' : ($profile->age_type == 'Months' ? 'M' : 'D') }} /
                        {{ $profile->gender ?? '--' }}
                    </td>
                    <td class="lbl">Collection Date</td>
                    <td class="val">:
                        {{ $invoice->sample_collected_at ? $invoice->sample_collected_at->format(($settings['pdf_show_time'] ?? true) ? 'd/m/Y h:i A' : 'd/m/Y') : $invoice->created_at->format(($settings['pdf_show_time'] ?? true) ? 'd/m/Y h:i A' : 'd/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Referred By</td>
                    <td class="val">: {{ $invoice->doctor ? $invoice->doctor->name : 'SELF' }}</td>
                    <td class="lbl">Report Date</td>
                    <td class="val">:
                        {{ ($report->report_date ?? $report->approved_at ?? now())->format(($settings['pdf_show_time'] ?? true) ? 'd/m/Y h:i A' : 'd/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="lbl">Patient ID</td>
                    <td class="val">: {{ $patient->formatted_id ?? $patient->id }}</td>
                    <td class="lbl"></td>
                    <td class="val"></td>
                </tr>
            </table>
        </div>
    </header>

    {{-- ══════════════════ FIXED FOOTER ══════════════════ --}}
    <footer>
        <div class="sig-container">
            @if($settings['pdf_show_signatures'] ?? true)
                {{-- ── Signature Section ── --}}
                @php $sigMode = $settings['report_signature_mode'] ?? 'global_bottom'; @endphp
                @if($sigMode === 'per_department' || $sigMode === 'last_page')
                    {{-- Global footer sigs hidden, shown per department or last page in body --}}
                @else
                    @php
                        $leftSig = null;
                        $centerSig = null;
                        $rightSig = null;

                        if ($settings['sig_1_enabled'] ?? true) {
                            $pos = $settings['sig_1_position'] ?? 'right';
                            $sigData = ['name' => $settings['global_sig_1_name'], 'desig' => $settings['global_sig_1_desig'], 'img' => $sigImgSrc];
                            if ($pos === 'left')
                                $leftSig = $sigData;
                            elseif ($pos === 'center')
                                $centerSig = $sigData;
                            else
                                $rightSig = $sigData;
                        }
                        if (($settings['sig_2_enabled'] ?? true) && $settings['global_sig_2_name']) {
                            $pos = $settings['sig_2_position'] ?? 'left';
                            $sigData = ['name' => $settings['global_sig_2_name'], 'desig' => $settings['global_sig_2_desig'], 'img' => $settings['global_sig_2_path']];
                            if ($pos === 'left')
                                $leftSig = $sigData;
                            elseif ($pos === 'center')
                                $centerSig = $sigData;
                            else
                                $rightSig = $sigData;
                        }
                        if (($settings['sig_3_enabled'] ?? true) && $settings['global_sig_3_name']) {
                            $pos = $settings['sig_3_position'] ?? 'center';
                            $sigData = ['name' => $settings['global_sig_3_name'], 'desig' => $settings['global_sig_3_desig'], 'img' => $settings['global_sig_3_path']];
                            if ($pos === 'left')
                                $leftSig = $sigData;
                            elseif ($pos === 'center')
                                $centerSig = $sigData;
                            else
                                $rightSig = $sigData;
                        }
                    @endphp
                    <table class="multi-sig-table" style="table-layout: fixed; width: 100%;">
                        <tr>
                            <!-- Left Signature -->
                            <td style="width: 33.33%; text-align: left; vertical-align: bottom; padding: 0 35px 5px;">
                                @if($leftSig)
                                    @if($leftSig['img'])
                                        <img class="sign-img" src="{{ $leftSig['img'] }}"><br>
                                    @else
                                        <div style="height: 50px;"></div>
                                    @endif
                                    <span class="doc-name">{!! nl2br(e($leftSig['name'])) !!}</span>
                                    <span class="doc-desig">{!! nl2br(e($leftSig['desig'])) !!}</span>
                                @endif
                            </td>
                            <!-- Center Signature -->
                            <td style="width: 33.33%; text-align: center; vertical-align: bottom; padding: 0 15px 5px;">
                                @if($centerSig)
                                    @if($centerSig['img'])
                                        <img class="sign-img" src="{{ $centerSig['img'] }}"><br>
                                    @else
                                        <div style="height: 50px;"></div>
                                    @endif
                                    <span class="doc-name">{!! nl2br(e($centerSig['name'])) !!}</span>
                                    <span class="doc-desig">{!! nl2br(e($centerSig['desig'])) !!}</span>
                                @endif
                            </td>
                            <!-- Right Signature -->
                            <td style="width: 33.33%; text-align: right; vertical-align: bottom; padding: 0 35px 5px;">
                                @if($rightSig)
                                    @if($rightSig['img'])
                                        <img class="sign-img" src="{{ $rightSig['img'] }}"><br>
                                    @else
                                        <div style="height: 50px;"></div>
                                    @endif
                                    <span class="doc-name">{!! nl2br(e($rightSig['name'])) !!}</span>
                                    <span class="doc-desig">{!! nl2br(e($rightSig['desig'])) !!}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                @endif
            @endif
        </div>

        <img class="footer-banner" src="{{ $footerImgSrc }}" alt="Footer"
            style="{{ ($letterheadMode === 'separate' && $showHeader && ($showFooter ?? true)) ? '' : 'display: none;' }}">

        @if($settings['pdf_show_page_number'] ?? true)
            <div
                style="position: absolute; bottom: 8px; right: 35px; font-size: 10px; color: #333; font-family: sans-serif; z-index: 10000; font-weight: bold; background-color: {{ $settings['pdf_page_number_bg_color'] ?? 'rgba(255, 255, 255, 0.85)' }}; padding: 3px 8px; border-radius: 4px; border: 1px solid #ddd; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                Page <span class="page-num"></span>
            </div>
        @endif
    </footer>

    {{-- ══════════════════ BODY CONTENT ══════════════════ --}}
    @php $testIndex = 0; @endphp

    @foreach($groupedResults as $deptId => $data)
        @php
            $dept = $data['department'];
            $tests = $data['tests'];
            $deptName = $dept ? $dept->name : 'General';
        @endphp

        @foreach($tests as $testId => $testData)
            @php
                $testName = $testData['name'];
                $labTest = $testData['labTest'];
                $results = $testData['results'];
            @endphp

            @php
                $style = $settings['report_page_break_style'] ?? 'continuous';
                $showDeptAlways = $settings['report_show_dept_header_always'] ?? true;
                $isFirstInDept = $loop->first;
            @endphp

            {{-- Page break logic --}}
            @if($testIndex > 0)
                @if($style === 'test_per_page')
                    <div style="page-break-after: always;"></div>
                @elseif($style === 'department_per_page' && $isFirstInDept)
                    <div style="page-break-after: always;"></div>
                @endif
            @endif

            <div class="test-block-wrapper" style="margin-bottom: 30px; clear: both;">
                {{-- ── Department & Test Title ── --}}
                @if($showDeptAlways || $isFirstInDept)
                    <div class="dept-title">{{ strtoupper($deptName) }}</div>
                @endif
                <div class="test-title" style="margin-bottom: 12px; font-size: {{ $sz11_5 }};">{{ strtoupper($testName) }}</div>

                {{-- ── Method (from LabTest master) ── --}}
                @if(($settings['pdf_show_test_method'] ?? true) && $labTest->method)
                    <div class="method-line">Method: {{ $labTest->method }}</div>
                @endif



                <table class="result-table">
                    @php
                        $isCultureOnly = $results->every(function ($r) {
                            return !empty($r->culture_data);
                        });
                    @endphp
                    @if(!$isCultureOnly)
                        <thead>
                            <tr>
                                <th style="width:40%">Test Description</th>
                                <th style="width:15%">Result</th>
                                <th style="width:8%">Flag</th>
                                <th style="width:22%">Ref. Range</th>
                                <th style="width:15%">Unit</th>
                            </tr>
                        </thead>
                    @endif
                    <tbody>
                        @php $hasSubHeaders = false; @endphp

                        @foreach($results as $r)
                            @php
                                // Detect sub-header: no result value AND no reference range
                                $isSubHeader = (is_null($r->result_value) || trim($r->result_value) === '')
                                    && (is_null($r->reference_range) || trim($r->reference_range) === '');

                                // Determine flag
                                $flag = null;
                                if ($r->is_highlighted) {
                                    $rawFlag = strtoupper(trim($r->status ?? ''));
                                    if (in_array($rawFlag, ['H', 'HIGH'])) {
                                        $flag = 'H';
                                    } elseif (in_array($rawFlag, ['L', 'LOW'])) {
                                        $flag = 'L';
                                    } else {
                                        $flag = $settings['report_abnormal_indicator'] ?? '*';
                                    }
                                }
                                $isAbnormal = $r->is_highlighted;
                            @endphp

                            @if($isSubHeader)
                                {{-- ── Sub-Header Row ── --}}
                                @php $hasSubHeaders = true; @endphp
                                <tr class="sub-hdr">
                                    <td colspan="5">{{ strtoupper($r->parameter_name) }}</td>
                                </tr>
                            @else
                                @if(!empty($r->culture_data))
                                    {{-- ── Culture & Sensitivity Spanned Row ── --}}
                                    <tr>
                                        <td colspan="5" style="padding: 12px 6px; border-bottom: 1px solid #333;">
                                            <div style="font-family: {{ $fontFamily }};">
                                                <div
                                                    style="font-weight: bold; font-size: {{ $sz11 }}; color: #000; margin-bottom: 8px; text-transform: uppercase;">
                                                    {{ $r->parameter_name }}
                                                </div>
                                                <table
                                                    style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: {{ $sz10 }};">
                                                    <tr>
                                                        <td
                                                            style="width: 30%; font-weight: bold; border: none; padding: 3px 0; color: #333;">
                                                            Growth Status:</td>
                                                        <td
                                                            style="width: 70%; border: none; padding: 3px 0; color: #000; font-weight: bold;">
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
                                                            <td style="font-weight: bold; border: none; padding: 3px 0; color: #333;">Organism
                                                                Isolated:</td>
                                                            <td
                                                                style="color: #000; font-weight: bold; font-style: italic; border: none; padding: 3px 0;">
                                                                {{ $r->culture_data['organism_name'] ?? 'Not Specified' }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="font-weight: bold; border: none; padding: 3px 0; color: #333;">Colony
                                                                Count:</td>
                                                            <td style="border: none; padding: 3px 0; color: #000;">
                                                                {{ $r->culture_data['colony_count'] ?? 'Not Specified' }}
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </table>

                                                @if(($r->culture_data['growth_status'] ?? 'Growth') !== 'No Growth' && !empty($r->culture_data['antibiotics']))
                                                    <div style="margin-top: 15px;">
                                                        <div
                                                            style="font-weight: bold; font-size: {{ $sz10_5 }}; border-bottom: 1.5px solid #000; padding-bottom: 4px; margin-bottom: 8px; text-transform: uppercase; color: #000;">
                                                            Antibiotic Susceptibility Profile
                                                        </div>
                                                        <table
                                                            style="width: 100%; border-collapse: collapse; font-size: {{ $sz10 }}; text-align: left;">
                                                            <thead>
                                                                <tr style="border-bottom: 1.5px solid #000; font-weight: bold; color: #000;">
                                                                    <th style="padding: 6px 4px; width: 45%; border: none;">Antibiotic Name</th>
                                                                    <th style="padding: 6px 4px; width: 35%; text-align: center; border: none;">
                                                                        Susceptibility</th>
                                                                    <th style="padding: 6px 4px; width: 20%; text-align: center; border: none;">
                                                                        MIC Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php
                                                                    $filledAntibiotics = collect($r->culture_data['antibiotics'] ?? [])->filter(function ($ab) {
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
                                                                        <td style="padding: 6px 4px; font-weight: bold; border: none; color: #000;">
                                                                            {{ $ab['name'] }}
                                                                        </td>
                                                                        <td
                                                                            style="padding: 6px 4px; text-align: center; font-weight: bold; border: none; color: #000;">
                                                                            {{ $text }}
                                                                        </td>
                                                                        <td
                                                                            style="padding: 6px 4px; text-align: center; font-family: monospace; border: none; color: #000;">
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
                                    {{-- ── Parameter Row ── --}}
                                    <tr class="{{ $hasSubHeaders ? 'param-indent' : '' }}">
                                        <td class="{{ $isAbnormal ? 'result-bold' : '' }}">
                                            {{ strtoupper($r->parameter_name) }}
                                            @if(($settings['pdf_show_test_method'] ?? true) && $r->method)
                                                <div
                                                    style="font-size: {{ $sz8 }}; font-weight: normal; font-style: italic; color: #555; margin-top: 2px;">
                                                    (Method: {{ $r->method }})
                                                </div>
                                            @endif
                                        </td>
                                        <td
                                            class="{{ $isAbnormal ? ($flag === 'H' ? 'flag-H' : ($flag === 'L' ? 'flag-L' : 'flag-abnormal')) : 'result-bold' }}">
                                            {{ $r->result_value }}
                                        </td>
                                        <td
                                            class="{{ $isAbnormal && !in_array($flag, ['H', 'L']) ? 'flag-abnormal' : ($flag ? 'flag-' . $flag : '') }}">
                                            {{ $flag }}
                                        </td>
                                        <td class="{{ $isAbnormal ? 'result-bold' : '' }}"
                                            style="width: 22%; font-size: {{ $sz8_5 }}; line-height: 1.2; vertical-align: middle;">
                                            @php
                                                $displayRange = $r->reference_range;

                                                // Backup: If range is empty, try to show the full master range list
                                                if (empty(trim($displayRange)) && isset($r->labTest->parameters) && is_array($r->labTest->parameters)) {
                                                    $masterParam = collect($r->labTest->parameters)->first(function ($p) use ($r) {
                                                        $pName = is_array($p) ? ($p['name'] ?? '') : $p;
                                                        return $pName === $r->parameter_name;
                                                    });

                                                    if ($masterParam && isset($masterParam['ranges']) && is_array($masterParam['ranges'])) {
                                                        $ranges = collect($masterParam['ranges']);

                                                        if ($ranges->count() > 1) {
                                                            // Try to find M/F explicitly
                                                            $maleRange = $ranges->firstWhere('gender', 'Male');
                                                            $femaleRange = $ranges->firstWhere('gender', 'Female');

                                                            if ($maleRange && $femaleRange) {
                                                                $displayRange = "M: " . ($maleRange['display_range'] ?? '') . "<br>F: " . ($femaleRange['display_range'] ?? '');
                                                            } else {
                                                                // Just join all unique display ranges
                                                                $displayRange = $ranges->pluck('display_range')->unique()->filter()->implode('<br>');
                                                            }
                                                        } else {
                                                            $displayRange = $ranges->first()['display_range'] ?? ($ranges->first()['normal_value'] ?? '');
                                                        }
                                                    }
                                                }
                                            @endphp
                                            {!! $displayRange !!}
                                        </td>
                                        <td class="{{ $isAbnormal ? 'result-bold' : '' }}" style="width: 15%;">
                                            {{ $r->unit }}
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    </tbody>
                </table>

                {{-- ── Method (per-result level, if different from test master) ── --}}
                @if(($settings['pdf_show_test_method'] ?? true) && $results->first()->method && $results->first()->method !== $labTest->method)
                    <p style="font-size:{{ $sz9 }}; color:#555; font-style:italic; margin-bottom:5px;">
                        <strong>Method:</strong> {{ $results->first()->method }}
                    </p>
                @endif

                {{-- ── Default Interpretation (from LabTest master — stored as HTML) ── --}}
                @if(($settings['report_show_interpretation'] ?? true) && $labTest->interpretation)
                    <div class="interp-block" style="page-break-inside: avoid;">
                        <div class="interp-label">Interpretation:</div>
                        <div class="interp-content">
                            {!! $labTest->interpretation !!}
                        </div>
                    </div>
                @endif

                {{-- ── Description / Note (from LabTest master — plain text) ── --}}
                @if(($settings['report_show_note'] ?? true) && $labTest->description)
                    <div class="interp-block" style="color:#555; page-break-inside: avoid;">
                        <div class="interp-label" style="color:#333;">Note:</div>
                        <div class="interp-content">
                            {!! nl2br(e($labTest->description)) !!}
                        </div>
                    </div>
                @endif

                {{-- ── Result Entry Remarks (Granular per test) ── --}}
                @if(!empty($testData['remark']))
                    <div class="remarks-block" style="page-break-inside: avoid;">
                        <div class="interp-label">Remarks:</div>
                        <div class="interp-content">
                            {!! $testData['remark'] !!}
                        </div>
                    </div>
                @endif

                {{-- ── Per-Department Signatures (if mode is per_department) ── --}}
                @if($settings['report_signature_mode'] === 'per_department' && $dept)
                    @if(isset($dept->sig_1_path) && $dept->sig_1_path)
                        <table class="multi-sig-table" style="margin-top:12px;">
                            <tr>
                                <td style="text-align:left; padding-left:35px; font-weight:700; font-size:{{ $sz11 }};">

                                </td>
                                @if(isset($dept->sig_1_path) && $dept->sig_1_path)
                                    <td>
                                        <img style="max-height:40px;" src="{{ storage_base64($dept->sig_1_path) }}"><br>
                                        <span class="doc-name">{!! nl2br(e($dept->sig_1_name ?? '')) !!}</span>
                                        <span class="doc-desig">{!! nl2br(e($dept->sig_1_desig ?? '')) !!}</span>
                                    </td>
                                @endif
                                @if(isset($dept->sig_2_path) && $dept->sig_2_path)
                                    <td>
                                        <img style="max-height:40px;" src="{{ storage_base64($dept->sig_2_path) }}"><br>
                                        <span class="doc-name">{!! nl2br(e($dept->sig_2_name ?? '')) !!}</span>
                                        <span class="doc-desig">{!! nl2br(e($dept->sig_2_desig ?? '')) !!}</span>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    @endif
                @endif

            </div>

            @php $testIndex++; @endphp
        @endforeach
    @endforeach

    {{-- ── Outsourced Reports ── --}}
    @if(isset($outsourcedImages) && count($outsourcedImages) > 0)
        @foreach($outsourcedImages as $itemId => $data)
            @php
                $labName = $data['lab_name'];
                $images = $data['images'];
                // Always start outsourced report on a new page if not the first item
                if ($testIndex > 0) {
                    echo '<div style="page-break-after: always;"></div>';
                }
            @endphp

            <div class="test-block-wrapper" style="clear: both; margin: 0; padding: 0;">
                @foreach($images as $img)
                    <div
                        style="text-align: center; width: 100%; margin: 0; padding: 0; page-break-inside: avoid; {{ !$loop->last ? 'page-break-after: always;' : '' }}">
                        <img src="{{ $img }}"
                            style="width: 109%; max-width: none; display: block; margin-left: 2%; margin-bottom: -2000px;">
                    </div>
                @endforeach
            </div>
            @php $testIndex++; @endphp
        @endforeach
    @endif

    {{-- ── Global Report Comments ── --}}
    @if($report->comments)
        <div class="doctor-comments">
            <div class="interp-label">Doctor's Comments / Interpretation:</div>
            <div class="interp-content">
                {!! $report->comments !!}
            </div>
        </div>
    @endif

    {{-- ── Last Page Signature Block ── --}}
    @if(($settings['pdf_show_signatures'] ?? true) && $sigMode === 'last_page')
        <div class="last-page-sig-container" style="margin-top: 30px; page-break-inside: avoid; clear: both;">
            @php
                $leftSig = null;
                $centerSig = null;
                $rightSig = null;

                if ($settings['sig_1_enabled'] ?? true) {
                    $pos = $settings['sig_1_position'] ?? 'right';
                    $sigData = ['name' => $settings['global_sig_1_name'], 'desig' => $settings['global_sig_1_desig'], 'img' => $sigImgSrc];
                    if ($pos === 'left')
                        $leftSig = $sigData;
                    elseif ($pos === 'center')
                        $centerSig = $sigData;
                    else
                        $rightSig = $sigData;
                }
                if (($settings['sig_2_enabled'] ?? true) && $settings['global_sig_2_name']) {
                    $pos = $settings['sig_2_position'] ?? 'left';
                    $sigData = ['name' => $settings['global_sig_2_name'], 'desig' => $settings['global_sig_2_desig'], 'img' => $settings['global_sig_2_path']];
                    if ($pos === 'left')
                        $leftSig = $sigData;
                    elseif ($pos === 'center')
                        $centerSig = $sigData;
                    else
                        $rightSig = $sigData;
                }
                if (($settings['sig_3_enabled'] ?? true) && $settings['global_sig_3_name']) {
                    $pos = $settings['sig_3_position'] ?? 'center';
                    $sigData = ['name' => $settings['global_sig_3_name'], 'desig' => $settings['global_sig_3_desig'], 'img' => $settings['global_sig_3_path']];
                    if ($pos === 'left')
                        $leftSig = $sigData;
                    elseif ($pos === 'center')
                        $centerSig = $sigData;
                    else
                        $rightSig = $sigData;
                }
            @endphp
            <table class="multi-sig-table" style="table-layout: fixed; width: 100%;">
                <tr>
                    <!-- Left Signature -->
                    <td style="width: 33.33%; text-align: left; vertical-align: bottom; padding: 0 35px 5px;">
                        @if($leftSig)
                            @if($leftSig['img'])
                                <img class="sign-img" src="{{ $leftSig['img'] }}"><br>
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                            <span class="doc-name">{!! nl2br(e($leftSig['name'])) !!}</span>
                            <span class="doc-desig">{!! nl2br(e($leftSig['desig'])) !!}</span>
                        @endif
                    </td>
                    <!-- Center Signature -->
                    <td style="width: 33.33%; text-align: center; vertical-align: bottom; padding: 0 15px 5px;">
                        @if($centerSig)
                            @if($centerSig['img'])
                                <img class="sign-img" src="{{ $centerSig['img'] }}"><br>
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                            <span class="doc-name">{!! nl2br(e($centerSig['name'])) !!}</span>
                            <span class="doc-desig">{!! nl2br(e($centerSig['desig'])) !!}</span>
                        @endif
                    </td>
                    <!-- Right Signature -->
                    <td style="width: 33.33%; text-align: right; vertical-align: bottom; padding: 0 35px 5px;">
                        @if($rightSig)
                            @if($rightSig['img'])
                                <img class="sign-img" src="{{ $rightSig['img'] }}"><br>
                            @else
                                <div style="height: 50px;"></div>
                            @endif
                            <span class="doc-name">{!! nl2br(e($rightSig['name'])) !!}</span>
                            <span class="doc-desig">{!! nl2br(e($rightSig['desig'])) !!}</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- ── End of Report ── --}}
    @if(!isset($outsourcedImages) || count($outsourcedImages) == 0)
        <div class="end-of-report">*** End of Report ***</div>
    @endif
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Lab Report - {{ $patient['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #121212;
            background: #fff;
            margin: 290px 25px 280px 25px;
            /* top right bottom left */
        }

        /* HEADER */
        header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            /* height: 160px; */
            height: 295px;
            overflow: hidden;
        }

        /* FOOTER */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 280px;
        }

        /* Ensure image fits */
        header>img,
        footer>img {
            width: 100%;
            /* height: 100%; */
            object-fit: contain;
        }

        /* ── PATIENT INFO BOX ── */
        .patient-info-box {
            border: 1px solid #181818;
            padding: 15px 10px;
            /* margin-top: 10px;
            margin-bottom: 10px; */
            font-size: 11px;
            margin: 5px 25px 10px;
        }

        .patient-info-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;

        }

        .patient-info-table td {
            padding: 1px 4px;
            width: 25%;
            vertical-align: top;
            /* display: inline-block; */
        }

        .patient-info-table .label {
            color: #121212;
            font-weight: bold;
        }

        .patient-info-table .value {
            color: #121212;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            width: 100%;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 15px 0 4px;
        }

        .sub-section-title {
            width: 100%;
            margin-bottom: 5px;
            position: relative;
        }

        /* ── RESULT TABLE ── */
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #181818;
            font-size: 11px;
        }

        .result-table thead tr th {
            background: #f5f5f5;
            border-bottom: 1px solid #181818;
            padding: 4px 6px;
            text-align: left;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
        }

        .result-table tbody tr td {
            padding: 1px 6px;
            vertical-align: middle;
        }

        .result-table .sub-header td {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            padding: 1px 6px 0px;
            color: #121212;
        }

        .result-table tbody tr.sub-pointers td:first-child {
            padding-left: 35px;
        }

        .flag-H {
            color: #cc0000;
            font-weight: 700;
        }

        .flag-L {
            color: #0055aa;
            font-weight: 700;
        }

        .abnormal-H {
            color: #cc0000;
            font-weight: 700;
        }

        .abnormal-L {
            color: #0055aa;
            font-weight: 700;
        }

        /* ── INTERPRETATION TABLE ── */
        .interp-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .interp-table th {
            background: #eeeeee;
            border: 1px solid #b8b8b8;
            padding: 3px 6px;
            font-weight: 700;
            text-align: left;
        }

        .interp-table td {
            border: 1px solid #b8b8b8;
            padding: 3px 6px;
            vertical-align: top;
        }

        /* ── PAGE BREAK ── */
        .page-break {
            page-break-after: always;
        }

        /* ── BARCODE PLACEHOLDER ── */
        .barcode-placeholder {
            position: absolute;
            top: -50px;
            right: 0px;
            padding: 2px 3px;
        }

        .signature-section {
            display: block;
            margin-bottom: 10px;
        }

        .signature-label {
            position: relative;
            top: 70px;
            font-size: 12px;
            padding-left: 35px;
            color: #121212;
            font-weight: 700;
        }

        .signature-right {
            float: right;
            text-align: center;
            margin-right: 35px;
            font-weight: 700;
            font-size: 12px;
        }

        .sign-img {
            margin-bottom: 4px;
            max-height: 70px;
        }

        .footer-img {
            position: absolute;
            bottom: 0px;
            left: 0px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            padding-left: 35px;
            color: #121212;
            font-weight: 700;
        }

        .checked-td {
            width: 60%;
            text-align: left;
            vertical-align: bottom;
        }

        .checked-td span {
            margin-left: 35px;
        }

        .signature-td {
            width: 40%;
            text-align: center;
            vertical-align: bottom;
            padding-right: 35px;
        }

        .watermark {
            position: fixed;
            top: 45%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1000;
            opacity: 0.1;
        }

        .watermark img {
            width: 300px;
        }
    </style>
</head>

<body>

    <div class="watermark">
        <img src="{{ public_path('assets/images/healthcare-logo.png') }}">
    </div>
    <!-- HEADER -->
    <header>
        <img src="{{ public_path('assets/images/pdf-header.jpeg') }}" alt="Trustline Logo">
        @include('pdf.patient_info', ['patient' => $patient])
    </header>

    <!-- FOOTER -->
    <footer>
        <table class="signature-table">
            <tr>
                <td class="checked-td">
                    <span>CHECKED BY</span>
                </td>

                <td class="signature-td">
                    <img class="sign-img" src="{{ public_path('assets/images/signature.jpg') }}">
                    <br>
                    <span>DR. ASHISH SHEKHAR JHA</span><br>
                    <span>MBBS, DMCH</span>
                </td>
            </tr>
        </table>

        <img class="footer-img" src="{{ public_path('assets/images/pdf-footer.jpeg') }}" alt="Trustline Logo">
    </footer>

    {{-- ============================= PAGE 1 : CBC ============================= --}}
    <div class="section-title">
        <p>Haematology</p>
        <p>Complete Blood Count (CBC)</p>
    </div>
    <div class="sub-section-title">
        <img class="barcode-placeholder" src="{{ public_path('assets/images/barcode.png') }}" height="50px">
    </div>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width:45%">Test Description</th>
                <th style="width:15%">Result</th>
                <th style="width:8%">Flag</th>
                <th style="width:20%">Ref. Range</th>
                <th style="width:12%">Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cbc as $index => $row)
                @if ($row['type'] === 'subheader')
                    <tr class="sub-header">
                        <td colspan="5">{{ $row['label'] }}</td>
                    </tr>
                @else
                    <tr class="{{ $index !== 0 ? 'sub-pointers' : '' }}">
                        <td style="{{ isset($row['bold']) && $row['bold'] ? 'font-weight:700;' : '' }}">
                            {{ $row['name'] }}
                        </td>
                        <td
                            class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'abnormal-H' : 'abnormal-L') : '' }}">
                            {{ $row['result'] }}
                        </td>
                        <td class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'flag-H' : 'flag-L') : '' }}">
                            {{ $row['flag'] ?? '' }}
                        </td>
                        <td style="{{ isset($row['bold']) && $row['bold'] ? 'font-weight:700;' : '' }}">
                            {{ $row['range'] }}</td>
                        <td style="{{ isset($row['bold']) && $row['bold'] ? 'font-weight:700;' : '' }}">
                            {{ $row['unit'] ?? '' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <div class="page-break"></div>

    {{-- ============================= PAGE 2 : LIPID PROFILE ============================= --}}
    <div class="section-title">
        <p>Biochemistry</p>
        <p>Lipid Profile</p>
    </div>
    <div class="sub-section-title">
        <img class="barcode-placeholder" src="{{ public_path('assets/images/barcode.png') }}" height="50px">
    </div>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width:45%">Test Description</th>
                <th style="width:15%">Result</th>
                <th style="width:8%">Flag</th>
                <th style="width:20%">Ref. Range</th>
                <th style="width:12%">Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lipid as $row)
                <tr>
                    <td style="{{ isset($row['bold']) && $row['bold'] ? 'font-weight:700;' : '' }}">
                        {{ $row['name'] }}
                    </td>
                    <td class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'abnormal-H' : 'abnormal-L') : '' }}">
                        {{ $row['result'] }}
                    </td>
                    <td class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'flag-H' : 'flag-L') : '' }}">
                        {{ $row['flag'] ?? '' }}
                    </td>
                    <td>{{ $row['range'] }}</td>
                    <td>{{ $row['unit'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:10px; margin:15px 0px 5px; font-weight:700;">Interpretation:</p>
    <table class="interp-table">
        <thead>
            <tr>
                <th>Lipid Profile Test</th>
                <th>Desirable Levels</th>
                <th>Borderline High</th>
                <th>High</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total cholesterol</td>
                <td>&lt;200 mg/dL</td>
                <td>200–239 mg/dL</td>
                <td>≥240 mg/dL</td>
            </tr>
            <tr>
                <td>LDL cholesterol</td>
                <td>&lt;100 mg/dL</td>
                <td>130–159 mg/dL</td>
                <td>≥160 mg/dL</td>
            </tr>
            <tr>
                <td>HDL cholesterol</td>
                <td>≥60 mg/dL</td>
                <td>40–59 mg/dL</td>
                <td>&lt;40 mg/dL</td>
            </tr>
            <tr>
                <td>Triglycerides</td>
                <td>&lt;150 mg/dL</td>
                <td>150–199 mg/dL</td>
                <td>≥200 mg/dL</td>
            </tr>
        </tbody>
    </table>
    <p style="font-size:10px; color:#555; margin-top:5px;">
        Desirable levels of cholesterol and triglycerides are associated with a lower risk of heart disease, while high
        levels
        increase the risk. HDL cholesterol is often called "good" cholesterol, as it helps to remove excess cholesterol
        from
        the blood vessels. In contrast, LDL cholesterol is often called "bad" cholesterol, as it contributes to the
        buildup
        of plaque in the arteries.
    </p>

    <div class="page-break"></div>

    {{-- ============================= PAGE 3 : LFT ============================= --}}
    <div class="section-title" style="margin-top: 30px;">
        <p>Liver Function Test (LFT)</p>
    </div>
    <div class="sub-section-title">
        <img class="barcode-placeholder" src="{{ public_path('assets/images/barcode.png') }}" height="50px">
    </div>
    </div>

    <table class="result-table">
        <thead>
            <tr>
                <th style="width:45%">Test Description</th>
                <th style="width:15%">Result</th>
                <th style="width:8%">Flag</th>
                <th style="width:20%">Ref. Range</th>
                <th style="width:12%">Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lft as $row)
                <tr>
                    <td style="{{ isset($row['bold']) && $row['bold'] ? 'font-weight:700;' : '' }}">
                        {{ $row['name'] }}
                    </td>
                    <td class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'abnormal-H' : 'abnormal-L') : '' }}">
                        {{ $row['result'] }}
                    </td>
                    <td class="{{ isset($row['flag']) ? ($row['flag'] === 'H' ? 'flag-H' : 'flag-L') : '' }}">
                        {{ $row['flag'] ?? '' }}
                    </td>
                    <td>{{ $row['range'] }}</td>
                    <td>{{ $row['unit'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="font-size:10px; margin:15px 0px 5px; font-weight:700;">Interpretation:</p>
    <table class="interp-table">
        <thead>
            <tr>
                <th style="width:25%">Test</th>
                <th>Interpretation</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total protein</td>
                <td>Total protein measures the total amount of protein in the blood, including albumin and globulin.
                    Abnormal results may indicate liver or kidney disease, malnutrition, or inflammation.</td>
            </tr>
            <tr>
                <td>Albumin</td>
                <td>Albumin is a protein produced by the liver. Abnormal results may indicate liver or kidney disease,
                    malnutrition, or inflammation.</td>
            </tr>
            <tr>
                <td>Bilirubin</td>
                <td>Bilirubin is a substance produced by the breakdown of red blood cells. Abnormal results may indicate
                    liver disease or other conditions affecting the liver or gallbladder.</td>
            </tr>
            <tr>
                <td>Alkaline phosphatase (ALP)</td>
                <td>ALP is an enzyme produced by the liver and other organs. Abnormal results may indicate liver or bone
                    disease, or certain medications.</td>
            </tr>
            <tr>
                <td>Alanine transaminase (ALT) / SGPT</td>
                <td>ALT is an enzyme primarily produced by the liver. Abnormal results may indicate liver damage or
                    disease.</td>
            </tr>
            <tr>
                <td>Aspartate transaminase (AST) / SGOT</td>
                <td>AST is an enzyme produced by the liver and other organs. Abnormal results may indicate liver damage
                    or disease.</td>
            </tr>
        </tbody>
    </table>

</body>

</html>

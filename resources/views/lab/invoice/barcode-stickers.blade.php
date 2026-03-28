<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Stickers - {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f0f0;
        }

        .sticker-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
            gap: 15px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .barcode-sticker {
            background: white;
            width: 50mm;
            height: 25mm;
            padding: 1.5mm 2mm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            border-radius: 4px;
            border: 1px solid #ddd;
            transition: transform 0.2s;
        }

        .barcode-sticker:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .sticker-header {
            display: flex;
            justify-content: space-between;
            font-size: 8px;
            font-weight: 800;
            line-height: 1.2;
            color: #000;
        }

        .patient-name {
            max-width: 75%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .barcode-svg {
            width: 100%;
            height: 11mm;
            display: block;
            margin: 1mm auto;
        }

        .sticker-footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 7px;
            font-weight: 700;
            color: #000;
        }

        .test-info {
            max-width: 65%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .date-time {
            text-align: right;
            font-size: 6.5px;
        }

        @media print {
            body { background: white; }
            .sticker-container { 
                padding: 0; 
                gap: 0; 
                display: block;
                max-width: none;
            }
            .barcode-sticker {
                box-shadow: none;
                border: 0.1mm solid #eee;
                border-radius: 0;
                float: left;
                margin: 0;
                page-break-inside: avoid;
            }
            .no-print { display: none !important; }
        }

        .no-print-header {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .preview-info {
            display: flex;
            flex-direction: column;
        }

        .preview-title {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        .preview-subtitle {
            font-size: 11px;
            color: #94a3b8;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn-print {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-print:hover { 
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-back {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

    <div class="no-print-header no-print">
        <div class="preview-info">
            <div class="preview-title">Barcode Stickers Print</div>
            <div class="preview-subtitle">Invoice: {{ $invoice->invoice_number }} | Patient: {{ $invoice->patient->name }}</div>
        </div>
        <div class="actions">
            <button class="btn-back" onclick="window.close()">Close Preview</button>
            <button class="btn-print" onclick="window.print()">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Now
            </button>
        </div>
    </div>

    <div class="sticker-container">
        @foreach($invoice->items as $item)
            @php
                $profile = $invoice->patient->patientProfile;
                $genderShort = $profile ? substr($profile->gender ?? 'M', 0, 1) : 'M';
                $age = $profile ? $profile->age : '0';
                $ageType = $profile ? $profile->age_type : 'Y';
                $ageStr = $age . substr($ageType, 0, 1);
            @endphp
            <div class="barcode-sticker">
                <div class="sticker-header">
                    <span class="patient-name">{{ $invoice->patient->name }} {{ $ageStr }}/ {{ $genderShort }}</span>
                    <span>{{ $invoice->invoice_number }}</span>
                </div>
                
                <svg class="barcode-svg" id="barcode-{{ $item->id }}"></svg>
                
                <div class="sticker-footer">
                    <span class="test-info">{{ $item->labTest->name }} ({{ $item->labTest->sample_type ?? 'Serum' }})</span>
                    <span class="date-time text-uppercase">{{ $invoice->invoice_date->format('d/m/y H:i') }}</span>
                </div>

                <script>
                    JsBarcode("#barcode-{{ $item->id }}", "{{ $invoice->barcode }}", {
                        format: "CODE128",
                        width: 1.2,
                        height: 40,
                        displayValue: true,
                        fontSize: 10,
                        margin: 0,
                        fontOptions: "bold"
                    });
                </script>
            </div>
        @endforeach
    </div>

    <script>
        // Auto-print if needed
        // window.print();
    </script>
</body>
</html>

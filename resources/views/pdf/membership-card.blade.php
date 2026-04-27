<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Membership Card - {{ $patient->name }}</title>
    <style>
        @page { margin: 0; }
        body { 
            margin: 0; 
            padding: 0; 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            background: #ffffff;
        }
        
        .card {
            width: 86mm;
            height: 54mm;
            background: #1e293b; /* Dark theme for premium feel */
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            color: #ffffff;
        }

        /* Abstract Background Pattern */
        .card::before {
            content: "";
            position: absolute;
            top: -20%;
            right: -10%;
            width: 60mm;
            height: 60mm;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .card::after {
            content: "";
            position: absolute;
            bottom: -10%;
            left: -5%;
            width: 40mm;
            height: 40mm;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(30, 78, 216, 0) 100%);
            border-radius: 50%;
        }

        .header {
            padding: 4mm 5mm;
            display: table;
            width: 100%;
            box-sizing: border-box;
            border-bottom: 0.2mm solid rgba(255,255,255,0.1);
        }

        .lab-info {
            display: table-cell;
            vertical-align: middle;
        }

        .lab-logo {
            width: 10mm;
            height: 10mm;
            background: #ffffff;
            border-radius: 6px;
            display: inline-block;
            vertical-align: middle;
            padding: 1mm;
            box-sizing: border-box;
        }

        .lab-name {
            display: inline-block;
            vertical-align: middle;
            margin-left: 3mm;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #ffffff;
            text-transform: uppercase;
        }

        .content {
            padding: 4mm 5mm;
            position: relative;
            z-index: 10;
        }

        /* Chip Icon simulation */
        .chip {
            width: 8mm;
            height: 6mm;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            border-radius: 2px;
            margin-bottom: 3mm;
            position: relative;
        }
        .chip::after {
            content: "";
            position: absolute;
            top: 1mm; left: 1mm; right: 1mm; bottom: 1mm;
            border: 0.1mm solid rgba(0,0,0,0.2);
        }

        .patient-details {
            float: left;
            width: 55mm;
        }

        .field {
            margin-bottom: 2mm;
        }

        .label {
            font-size: 6px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 0.5mm;
        }

        .value {
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 0.2px;
        }

        .id-number {
            font-family: 'Courier', monospace;
            font-size: 13px;
            color: #fbbf24; /* Gold accent */
            letter-spacing: 1px;
        }

        .qr-section {
            float: right;
            width: 18mm;
            text-align: right;
        }

        .qr-code {
            width: 16mm;
            height: 16mm;
            background: #ffffff;
            padding: 1mm;
            border-radius: 4px;
            display: inline-block;
        }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            padding: 3mm 5mm;
            box-sizing: border-box;
            background: rgba(0,0,0,0.2);
            font-size: 7px;
            color: #94a3b8;
        }

        .plan-badge {
            position: absolute;
            top: 4mm;
            right: 5mm;
            background: rgba(255,255,255,0.1);
            border: 0.2mm solid rgba(255,255,255,0.2);
            padding: 1mm 2.5mm;
            border-radius: 100px;
            font-size: 7px;
            font-weight: bold;
            color: #fbbf24;
            text-transform: uppercase;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div class="lab-info">
                @if($company->logo)
                    <div class="lab-logo">
                        <img src="{{ storage_base64($company->logo) }}" style="width:100%; height:100%; object-fit: contain;">
                    </div>
                @endif
                <div class="lab-name">{{ $company->name }}</div>
            </div>
        </div>

        <div class="plan-badge">
            {{ $plan->name }}
        </div>

        <div class="content clearfix">
            <div class="chip"></div>
            
            <div class="patient-details">
                <div class="field">
                    <div class="label">Member Name</div>
                    <div class="value text-uppercase">{{ $patient->name }}</div>
                </div>
                
                <div class="field">
                    <div class="label">Card Number</div>
                    <div class="id-number">{{ $patient->patientProfile->patient_id_string ?? 'P-' . str_pad($patient->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>

                <div class="field" style="margin-top: 3mm;">
                    <div class="label">Valid Thru</div>
                    <div class="value" style="font-size: 9px;">{{ $membership->valid_until->format('m/Y') }}</div>
                </div>
            </div>

            <div class="qr-section">
                <div class="qr-code">
                    @php
                        $qrData = route('portal.login', ['id' => $patient->id]); // Placeholder link
                        $qrBase64 = generate_qr_base64($qrData);
                    @endphp
                    <img src="{{ $qrBase64 }}" style="width:100%; height:100%;">
                </div>
                <div style="font-size: 5px; color: #94a3b8; margin-top: 1mm;">SCAN TO VERIFY</div>
            </div>
        </div>

        <div class="footer clearfix">
            <div style="float: left;">
                PH: {{ $company->phone ?? 'Support' }}
            </div>
            <div style="float: right;">
                HEALTHPATH NETWORK
            </div>
        </div>
    </div>
</body>
</html>

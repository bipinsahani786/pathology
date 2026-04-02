<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Membership Card - {{ $patient->name }}</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', 'Arial', sans-serif; background-color: #f8fafc; }
        
        .card {
            width: 86mm;
            height: 54mm;
            background: #ffffff;
            position: relative;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header {
            height: 18mm;
            background: linear-gradient(135deg, #2563eb 0%, #1e4ed8 100%);
            color: white;
            padding: 2mm 4mm;
            display: flex;
            align-items: center;
        }

        .lab-logo { width: 10mm; height: 10mm; background: white; border-radius: 4px; display: inline-block; vertical-align: middle; }
        .lab-name { display: inline-block; margin-left: 2mm; vertical-align: middle; font-size: 11px; font-weight: bold; width: 65mm; line-height: 1.2; }

        .content { padding: 3mm 4mm; position: relative; }
        
        .patient-photo {
            width: 16mm;
            height: 20mm;
            background: #e2e8f0;
            border-radius: 4px;
            float: right;
            border: 1px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #94a3b8;
        }

        .field { margin-bottom: 2mm; }
        .label { font-size: 7px; color: #64748b; text-transform: uppercase; font-weight: bold; letter-spacing: 0.5px; }
        .value { font-size: 11px; font-weight: bold; color: #1e293b; text-transform: uppercase; }

        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 8mm;
            background: #1e293b;
            color: white;
            padding: 0 4mm;
            display: table;
        }
        .footer-cell { display: table-cell; vertical-align: middle; font-size: 7px; }

        .membership-badge {
            position: absolute;
            top: 20mm;
            right: 4mm;
            background: #fef3c7;
            color: #d97706;
            padding: 1mm 2mm;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            border: 1px solid #fcd34d;
        }

        .validity { font-size: 8px; color: #2563eb; font-weight: bold; }
        
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            @if($company->logo)
                <img src="{{ public_path('storage/' . $company->logo) }}" style="max-height:10mm; max-width:10mm;">
            @else
                <div class="lab-logo"></div>
            @endif
            <div class="lab-name text-uppercase">{{ $company->name }}</div>
        </div>

        <div class="content clearfix">
            <div class="patient-photo">
                @if($patient->patientProfile && $patient->patientProfile->photo)
                    <img src="{{ public_path('storage/' . $patient->patientProfile->photo) }}" style="width:100%; height:100%; object-fit: cover;">
                @else
                    PHOTO
                @endif
            </div>

            <div class="field">
                <div class="label">Patient Name</div>
                <div class="value">{{ $patient->name }}</div>
            </div>

            <div class="field">
                <div class="label">Patient ID</div>
                <div class="value" style="color: #2563eb;">{{ $patient->patientProfile->patient_id_string ?? 'PAT-' . $patient->id }}</div>
            </div>

            <div class="field">
                <div class="label">Plan Validity</div>
                <div class="validity">
                    {{ $membership->valid_from->format('d/m/Y') }} — {{ $membership->valid_until->format('d/m/Y') }}
                </div>
            </div>
            
            <div class="membership-badge">
                {{ $plan->name }} MEMBER
            </div>
        </div>

        <div class="footer">
            <div class="footer-cell" style="text-align: left;">
                ✉ {{ $company->email ?? 'info@lab.com' }}
            </div>
            <div class="footer-cell" style="text-align: right;">
                📞 {{ $company->phone ?? 'Contact' }}
            </div>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-danger {
            color: #dc3545;
        }
        .text-success {
            color: #28a745;
        }
        .text-warning {
            color: #ffc107;
        }
        .summary-box {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
            text-align: right;
            font-size: 12px;
        }
        .fw-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h2>Invoices Report</h2>

    <table>
        <thead>
            <tr>
                @if($columns['invoice_number'] ?? false) <th>Invoice #</th> @endif
                @if($columns['date'] ?? false) <th>Date</th> @endif
                @if($columns['patient_name'] ?? false) <th>Patient Name</th> @endif
                @if($columns['patient_phone'] ?? false) <th>Phone</th> @endif
                @if($columns['total_amount'] ?? false) <th class="text-right">Total</th> @endif
                @if($columns['paid_amount'] ?? false) <th class="text-right">Paid</th> @endif
                @if($columns['due_amount'] ?? false) <th class="text-right">Pending</th> @endif
                @if($columns['payment_status'] ?? false) <th class="text-center">Status</th> @endif
                @if($columns['collection_center'] ?? false) <th>Branch / CC</th> @endif
            </tr>
        </thead>
        <tbody>
            @php
                $sumTotal = 0;
                $sumPaid = 0;
                $sumDue = 0;
            @endphp
            @foreach($invoices as $inv)
                @php
                    $sumTotal += $inv->total_amount;
                    $sumPaid += $inv->paid_amount;
                    $sumDue += $inv->due_amount;
                @endphp
                <tr>
                    @if($columns['invoice_number'] ?? false) 
                        <td>{{ $inv->invoice_number }}</td> 
                    @endif
                    @if($columns['date'] ?? false) 
                        <td>{{ $inv->invoice_date->format('d/m/Y') }}</td> 
                    @endif
                    @if($columns['patient_name'] ?? false) 
                        <td>{{ $inv->patient->name ?? '' }}</td> 
                    @endif
                    @if($columns['patient_phone'] ?? false) 
                        <td>{{ $inv->patient->phone ?? '' }}</td> 
                    @endif
                    @if($columns['total_amount'] ?? false) 
                        <td class="text-right">{{ number_format($inv->total_amount, 2) }}</td> 
                    @endif
                    @if($columns['paid_amount'] ?? false) 
                        <td class="text-right">{{ number_format($inv->paid_amount, 2) }}</td> 
                    @endif
                    @if($columns['due_amount'] ?? false) 
                        <td class="text-right @if($inv->due_amount > 0) text-danger fw-bold @endif">{{ number_format($inv->due_amount, 2) }}</td> 
                    @endif
                    @if($columns['payment_status'] ?? false) 
                        <td class="text-center">{{ $inv->payment_status }}</td> 
                    @endif
                    @if($columns['collection_center'] ?? false) 
                        <td>{{ $inv->collectionCenter->name ?? ($inv->branch->name ?? '') }}</td> 
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        @if($columns['total_amount'] ?? false)
            <p>Overall Total: <strong>{{ number_format($sumTotal, 2) }}</strong></p>
        @endif
        @if($columns['paid_amount'] ?? false)
            <p>Overall Paid: <strong class="text-success">{{ number_format($sumPaid, 2) }}</strong></p>
        @endif
        @if($columns['due_amount'] ?? false)
            <p>Overall Pending (Due): <strong class="text-danger">{{ number_format($sumDue, 2) }}</strong></p>
        @endif
        <p>Total Records: <strong>{{ $invoices->count() }}</strong></p>
    </div>

</body>
</html>

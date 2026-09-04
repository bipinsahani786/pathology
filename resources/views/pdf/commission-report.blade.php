<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Commission Report - {{ $partner->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #2c3e50;
            background: #fff;
            padding: 22px 26px;
        }

        /* ── HEADER ─────────────────────────────────── */
        .header-bar {
            border-bottom: 3px solid #1a5276;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .header-bar table { width: 100%; }
        .lab-name {
            font-size: 16px;
            font-weight: bold;
            color: #1a5276;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .report-sub { font-size: 10px; color: #7f8c8d; margin-top: 2px; }
        .header-right { text-align: right; font-size: 9px; color: #7f8c8d; line-height: 1.7; }
        .period-chip {
            display: inline-block;
            background: #1a5276;
            color: #fff;
            padding: 3px 10px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: bold;
            margin-top: 3px;
        }

        /* ── PARTNER STRIP ─────────────────────────── */
        .partner-strip {
            background: #eaf0fb;
            border-left: 4px solid #2980b9;
            padding: 8px 14px;
            border-radius: 0 5px 5px 0;
            margin-bottom: 12px;
        }
        .partner-strip table { width: 100%; }
        .ps-label { font-size: 8px; color: #7f8c8d; text-transform: uppercase; font-weight: bold; letter-spacing: 0.4px; }
        .ps-value { font-size: 11px; font-weight: bold; color: #1a5276; margin-top: 2px; }

        /* ── NOTE BOX ──────────────────────────────── */
        .note-box {
            background: #fffde7;
            border: 1px solid #f9e79f;
            border-left: 4px solid #f39c12;
            padding: 6px 10px;
            border-radius: 0 4px 4px 0;
            margin-bottom: 12px;
            font-size: 8.5px;
            color: #7d6608;
            line-height: 1.5;
        }

        /* ── SUMMARY CARDS ─────────────────────────── */
        .sum-table { width: 100%; border-collapse: separate; border-spacing: 5px; margin-bottom: 12px; }
        .s-card { border-radius: 5px; padding: 8px 10px; text-align: center; border: 1px solid #e0e0e0; }
        .s-card .s-label { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; color: #7f8c8d; margin-bottom: 3px; }
        .s-card .s-val   { font-size: 12px; font-weight: bold; }

        .c-navy   { background:#eaf0f6; border-color:#aed6f1; } .c-navy   .s-val { color:#1a5276; }
        .c-teal   { background:#e8f8f5; border-color:#a2d9ce; } .c-teal   .s-val { color:#117a65; }
        .c-red    { background:#fdedec; border-color:#f1948a; } .c-red    .s-val { color:#c0392b; }
        .c-green  { background:#eafaf1; border-color:#a9dfbf; } .c-green  .s-val { color:#1e8449; }
        .c-purple { background:#f4ecf7; border-color:#d7bde2; } .c-purple .s-val { color:#7d3c98; }

        /* ── SECTION TITLE ─────────────────────────── */
        .sec-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #1a5276;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-left: 3px solid #2980b9;
            padding-left: 7px;
            margin-bottom: 6px;
        }

        /* ── DATA TABLE ────────────────────────────── */
        .dt { width: 100%; border-collapse: collapse; }
        .dt thead tr { background: #1a5276; color: #fff; }
        .dt thead th {
            padding: 7px 5px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            border: 1px solid #154360;
        }
        .dt tbody tr.ev { background: #f8f9fa; }
        .dt tbody tr.od { background: #ffffff; }
        .dt tbody td {
            padding: 6px 5px;
            border: 1px solid #eaecee;
            font-size: 9.5px;
            vertical-align: middle;
        }
        .tr { text-align: right; }
        .tc { text-align: center; }
        .tl { text-align: left; }
        .fw { font-weight: bold; }

        /* status */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .b-paid    { background:#eafaf1; color:#1e8449; }
        .b-partial { background:#fdf2e9; color:#d35400; }
        .b-unpaid  { background:#fdedec; color:#c0392b; }
        .b-yes     { background:#eafaf1; color:#1e8449; }
        .b-no      { background:#fdedec; color:#c0392b; }
        .b-pct     { background:#f4ecf7; color:#7d3c98; padding:2px 5px; border-radius:7px; font-size:8px; font-weight:bold; }

        /* totals row */
        .tot td {
            background: #1a5276 !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 9.5px;
            padding: 8px 5px;
            border: 1.5px solid #154360;
        }

        /* footer */
        .footer {
            margin-top: 14px;
            border-top: 1px solid #eaecee;
            padding-top: 7px;
            font-size: 8px;
            color: #aab7b8;
            text-align: center;
        }
        .footer span { color: #7f8c8d; }
    </style>
</head>
<body>

{{-- ══ HEADER ══ --}}
<div class="header-bar">
    <table>
        <tr>
            <td style="width:62%">
                <div class="lab-name">{{ $company->name ?? 'Pathology Lab' }}</div>
                <div class="report-sub">{{ $partnerType }} Commission Report</div>
            </td>
            <td style="width:38%" class="header-right">
                <div>Generated: <span style="color:#2c3e50; font-weight:bold">{{ $generatedAt }}</span></div>
                <div class="period-chip">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                    &nbsp;—&nbsp;
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ PARTNER INFO ══ --}}
<div class="partner-strip">
    <table>
        <tr>
            <td style="width:35%">
                <div class="ps-label">{{ $partnerType }} Name</div>
                <div class="ps-value">{{ $partner->name }}</div>
            </td>
            <td style="width:32%">
                <div class="ps-label">Phone</div>
                <div class="ps-value">{{ $partner->phone ?? 'N/A' }}</div>
            </td>
            <td style="width:33%" class="tr">
                <div class="ps-label">Avg Commission Rate (on MRP)</div>
                <div class="ps-value" style="color:#7d3c98">{{ $avgCommissionPct }}%</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══ NOTE BOX ══ --}}
<div class="note-box">
    <strong>How Commission is Calculated:</strong>
    Commission % shown is calculated on <strong>MRP (before discount)</strong>.
    The system applies commission on the net effective price per test (MRP apportioned after discount),
    which may differ from the doctor's global rate if a profit-based commission basis is configured.
</div>

{{-- ══ SUMMARY CARDS ══ --}}
<table class="sum-table">
    <tr>
        <td style="width:20%">
            <div class="s-card c-navy">
                <div class="s-label">Total Invoices</div>
                <div class="s-val">{{ count($invoices) }}</div>
            </div>
        </td>
        <td style="width:20%">
            <div class="s-card c-teal">
                <div class="s-label">Total MRP</div>
                <div class="s-val">&#8377;{{ number_format($totalSubtotal, 2) }}</div>
            </div>
        </td>
        <td style="width:20%">
            <div class="s-card c-red">
                <div class="s-label">Total Discount</div>
                <div class="s-val">&#8377;{{ number_format($totalDiscount, 2) }}</div>
            </div>
        </td>
        <td style="width:20%">
            <div class="s-card c-green">
                <div class="s-label">Customer Paid</div>
                <div class="s-val">&#8377;{{ number_format($totalPaid, 2) }}</div>
            </div>
        </td>
        <td style="width:20%">
            <div class="s-card c-purple">
                <div class="s-label">Total Commission</div>
                <div class="s-val">&#8377;{{ number_format($totalCommission, 2) }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══ TABLE TITLE ══ --}}
<div class="sec-title">Invoice-wise Commission Breakdown</div>

{{-- ══ DATA TABLE ══ --}}
<table class="dt">
    <thead>
        <tr>
            <th class="tc" style="width:4%">#</th>
            <th class="tl" style="width:13%">Invoice No</th>
            <th class="tl" style="width:19%">Patient Name</th>
            <th class="tc" style="width:9%">Date</th>
            <th class="tr" style="width:10%">MRP (&#8377;)</th>
            <th class="tr" style="width:8%">Disc (&#8377;)</th>
            <th class="tr" style="width:9%">Net Amt (&#8377;)</th>
            <th class="tr" style="width:9%">Cust Paid (&#8377;)</th>
            <th class="tr" style="width:9%">Comm (&#8377;)</th>
            <th class="tc" style="width:6%">Comm %</th>
            <th class="tc" style="width:5%">Pay</th>
            <th class="tc" style="width:5%">Settled</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $i => $inv)
            @php
                $row       = ($i % 2 === 0) ? 'ev' : 'od';
                $payStatus = $inv->payment_status;
                $payCls    = match($payStatus) { 'Paid' => 'b-paid', 'Partial' => 'b-partial', default => 'b-unpaid' };
                $settled   = (bool) $inv->{$settledField};
                $pct       = $inv->commission_pct ?? 0;
                $mrp       = $inv->subtotal > 0 ? $inv->subtotal : $inv->total_amount;
            @endphp
            <tr class="{{ $row }}">
                <td class="tc" style="color:#aab7b8">{{ $i + 1 }}</td>
                <td class="tl fw" style="color:#1a5276">{{ $inv->invoice_number }}</td>
                <td class="tl">{{ $inv->patient->name ?? 'N/A' }}</td>
                <td class="tc" style="color:#555">{{ $inv->invoice_date->format('d M Y') }}</td>
                <td class="tr fw">{{ number_format($mrp, 2) }}</td>
                <td class="tr" style="color:#c0392b">
                    {{ $inv->discount_amount > 0 ? number_format($inv->discount_amount, 2) : '—' }}
                </td>
                <td class="tr" style="color:#2c3e50">{{ number_format($inv->total_amount, 2) }}</td>
                <td class="tr" style="color:#1e8449; font-weight:bold">{{ number_format($inv->paid_amount, 2) }}</td>
                <td class="tr" style="color:#2980b9; font-weight:bold">{{ number_format($inv->{$commissionField}, 2) }}</td>
                <td class="tc">
                    @if($pct > 0)
                        <span class="b-pct">{{ $pct }}%</span>
                    @else
                        <span style="color:#bdc3c7">0%</span>
                    @endif
                </td>
                <td class="tc"><span class="badge {{ $payCls }}">{{ $payStatus }}</span></td>
                <td class="tc">
                    <span class="badge {{ $settled ? 'b-yes' : 'b-no' }}">
                        {{ $settled ? 'Yes' : 'No' }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="12" class="tc" style="padding:22px; color:#bdc3c7">
                    No invoices found for this date range.
                </td>
            </tr>
        @endforelse
    </tbody>

    @if(count($invoices) > 0)
    <tfoot>
        <tr class="tot">
            <td colspan="4" class="tc">
                TOTAL &nbsp;&bull;&nbsp; {{ count($invoices) }} Invoice{{ count($invoices) > 1 ? 's' : '' }}
            </td>
            <td class="tr">&#8377;{{ number_format($totalSubtotal, 2) }}</td>
            <td class="tr">&#8377;{{ number_format($totalDiscount, 2) }}</td>
            <td class="tr">&#8377;{{ number_format($totalMrp, 2) }}</td>
            <td class="tr">&#8377;{{ number_format($totalPaid, 2) }}</td>
            <td class="tr">&#8377;{{ number_format($totalCommission, 2) }}</td>
            <td class="tc">{{ $avgCommissionPct }}%</td>
            <td colspan="2" class="tc">—</td>
        </tr>
    </tfoot>
    @endif
</table>

{{-- ══ FOOTER ══ --}}
<div class="footer">
    This report is system-generated and confidential.
    &nbsp;|&nbsp; <span>{{ $company->name ?? 'Lab' }}</span>
    &nbsp;|&nbsp; Generated on <span>{{ $generatedAt }}</span>
</div>

</body>
</html>

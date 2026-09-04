<?php

namespace App\Http\Controllers;

use App\Models\CollectionCenter;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Configuration;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CommissionReportController extends Controller
{
    /**
     * Download commission report as PDF
     */
    public function downloadPdf(Request $request)
    {
        $data = $this->getReportData($request);

        $pdf = Pdf::loadView('pdf.commission-report', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'commission-report-' . $data['partnerType'] . '-' . $data['partner']->name . '-' . $data['startDate'] . '-to-' . $data['endDate'] . '.pdf';
        $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '-', $filename);

        return $pdf->download($filename);
    }

    /**
     * Download commission report as Excel
     */
    public function downloadExcel(Request $request)
    {
        $data = $this->getReportData($request);

        $spreadsheet = $this->buildSpreadsheet($data);

        $filename = 'commission-report-' . $data['partnerType'] . '-' . $data['partner']->name . '-' . $data['startDate'] . '-to-' . $data['endDate'] . '.xlsx';
        $filename = preg_replace('/[^A-Za-z0-9\-\.]/', '-', $filename);

        $writer = new Xlsx($spreadsheet);

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Fetch and prepare report data
     */
    private function getReportData(Request $request): array
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $partnerId  = (int) $request->query('partner_id');
        $partnerType = $request->query('partner_type', 'Doctor'); // Doctor | Agent
        $startDate  = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate    = $request->query('end_date', now()->format('Y-m-d'));

        // Validate partner belongs to this company
        $partner = User::where('company_id', $companyId)->findOrFail($partnerId);

        // Branch restriction
        $myBranchId = $this->getMyBranchId($user);

        // Determine the correct commission field
        $commissionField = match ($partnerType) {
            'Agent'  => 'agent_commission_amount',
            default  => 'doctor_commission_amount',
        };

        $partnerIdField = match ($partnerType) {
            'Agent'  => 'referred_by_agent_id',
            default  => 'referred_by_doctor_id',
        };

        $settledField = match ($partnerType) {
            'Agent'  => 'is_agent_settled',
            default  => 'is_doctor_settled',
        };

        // Fetch invoices for this partner in date range
        $invoices = Invoice::with(['patient'])
            ->where('company_id', $companyId)
            ->when($myBranchId, fn ($q) => $q->where('branch_id', $myBranchId))
            ->where($partnerIdField, $partnerId)
            ->where('status', '!=', 'Cancelled')
            ->whereBetween('invoice_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('invoice_date', 'asc')
            ->get();

        // Calculate totals
        $totalSubtotal   = $invoices->sum('subtotal');   // original MRP before discount
        $totalMrp        = $invoices->sum('total_amount'); // net payable (after discount)
        $totalPaid       = $invoices->sum('paid_amount');
        $totalCommission = $invoices->sum($commissionField);
        $totalDiscount   = $invoices->sum('discount_amount');

        // Commission % per invoice — calculated on subtotal (original MRP, before discount)
        $invoices->each(function ($inv) use ($commissionField) {
            $base = $inv->subtotal > 0 ? $inv->subtotal : $inv->total_amount;
            $inv->commission_pct = $base > 0
                ? round(($inv->{$commissionField} / $base) * 100, 2)
                : 0;
        });

        // Overall avg commission % based on subtotal
        $avgCommissionPct = $totalSubtotal > 0
            ? round(($totalCommission / $totalSubtotal) * 100, 2)
            : 0;

        // Company info
        $company = $user->company;

        return [
            'partner'          => $partner,
            'partnerType'      => $partnerType,
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'commissionField'  => $commissionField,
            'settledField'     => $settledField,
            'invoices'         => $invoices,
            'totalSubtotal'    => $totalSubtotal,   // original MRP sum
            'totalMrp'         => $totalMrp,         // after discount sum
            'totalPaid'        => $totalPaid,
            'totalCommission'  => $totalCommission,
            'totalDiscount'    => $totalDiscount,
            'avgCommissionPct' => $avgCommissionPct,
            'company'          => $company,
            'generatedAt'      => now()->format('d M Y, h:i A'),
        ];
    }

    /**
     * Build PhpSpreadsheet workbook
     */
    private function buildSpreadsheet(array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Commission Report');

        $partner      = $data['partner'];
        $company      = $data['company'];
        $partnerType  = $data['partnerType'];
        $invoices     = $data['invoices'];
        $commField    = $data['commissionField'];
        $settledField = $data['settledField'];

        // ── Column widths ─────────────────────────────────────────────────────
        // A=S.No | B=Invoice | C=Patient | D=Date | E=MRP(subtotal) | F=Discount | G=Net Amt | H=Cust Paid | I=Commission | J=Comm% | K=PayStatus | L=Settled
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);  // MRP (subtotal)
        $sheet->getColumnDimension('F')->setWidth(13);  // Discount
        $sheet->getColumnDimension('G')->setWidth(14);  // Net Amount (after discount)
        $sheet->getColumnDimension('H')->setWidth(14);  // Customer Paid
        $sheet->getColumnDimension('I')->setWidth(18);  // Commission Amount
        $sheet->getColumnDimension('J')->setWidth(12);  // Commission %
        $sheet->getColumnDimension('K')->setWidth(14);  // Payment Status
        $sheet->getColumnDimension('L')->setWidth(12);  // Settled

        // ── Header Block ─────────────────────────────────────────────────────
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', strtoupper($company->name ?? 'Lab') . ' — Commission Report');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2',
            $partnerType . ': ' . $partner->name .
            ' | Phone: ' . ($partner->phone ?? 'N/A') .
            ' | Period: ' . \Carbon\Carbon::parse($data['startDate'])->format('d M Y') .
            ' to ' . \Carbon\Carbon::parse($data['endDate'])->format('d M Y') .
            ' | Avg Commission: ' . $data['avgCommissionPct'] . '%' .
            ' | Generated: ' . $data['generatedAt']
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2980b9']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // Blank separator row
        $sheet->getRowDimension(3)->setRowHeight(6);

        // ── Column Headers ────────────────────────────────────────────────────
        $headers = [
            'A' => '#',
            'B' => 'Invoice No',
            'C' => 'Patient Name',
            'D' => 'Date',
            'E' => 'MRP (₹)',          // subtotal — before discount
            'F' => 'Discount (₹)',
            'G' => 'Net Amount (₹)',   // total_amount — after discount
            'H' => 'Cust. Paid (₹)',
            'I' => $partnerType . ' Commission (₹)',
            'J' => 'Comm %',
            'K' => 'Payment Status',
            'L' => 'Settled',
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue($col . '4', $header);
        }

        $sheet->getStyle('A4:L4')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e8449']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(24);

        // ── Data Rows ─────────────────────────────────────────────────────────
        $row = 5;
        $sno = 1;
        foreach ($invoices as $inv) {
            $isSettled = (bool) $inv->{$settledField};
            $bgColor   = ($row % 2 === 0) ? 'F2F3F4' : 'FFFFFF';
            $mrp       = $inv->subtotal > 0 ? $inv->subtotal : $inv->total_amount;
            $commPct   = $inv->commission_pct ?? 0;

            $sheet->setCellValue('A' . $row, $sno);
            $sheet->setCellValue('B' . $row, $inv->invoice_number);
            $sheet->setCellValue('C' . $row, $inv->patient->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $inv->invoice_date->format('d M Y'));
            $sheet->setCellValue('E' . $row, (float) $mrp);
            $sheet->setCellValue('F' . $row, (float) $inv->discount_amount);
            $sheet->setCellValue('G' . $row, (float) $inv->total_amount);
            $sheet->setCellValue('H' . $row, (float) $inv->paid_amount);
            $sheet->setCellValue('I' . $row, (float) $inv->{$commField});
            $sheet->setCellValue('J' . $row, $commPct . '%');
            $sheet->setCellValue('K' . $row, $inv->payment_status);
            $sheet->setCellValue('L' . $row, $isSettled ? 'Settled' : 'Pending');

            $sheet->getStyle('A' . $row . ':L' . $row)->applyFromArray([
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Amount columns → right align + number format
            foreach (['E', 'F', 'G', 'H', 'I'] as $amtCol) {
                $sheet->getStyle($amtCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle($amtCol . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
            // Center columns
            foreach (['A', 'D', 'J', 'K', 'L'] as $cCol) {
                $sheet->getStyle($cCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }

            // Color: discount = red, paid = green, commission = blue
            $sheet->getStyle('F' . $row)->getFont()->getColor()->setRGB('c0392b');
            $sheet->getStyle('H' . $row)->getFont()->getColor()->setRGB('1e8449');
            $sheet->getStyle('H' . $row)->getFont()->setBold(true);
            $sheet->getStyle('I' . $row)->getFont()->getColor()->setRGB('2980b9');
            $sheet->getStyle('I' . $row)->getFont()->setBold(true);
            $sheet->getStyle('J' . $row)->getFont()->getColor()->setRGB('7d3c98');
            $sheet->getStyle('J' . $row)->getFont()->setBold(true);

            // Color payment status
            $statusColor = match($inv->payment_status) {
                'Paid'    => '1e8449',
                'Partial' => 'd35400',
                default   => 'c0392b',
            };
            $sheet->getStyle('K' . $row)->getFont()->getColor()->setRGB($statusColor);

            // Color settled
            $sheet->getStyle('L' . $row)->getFont()->getColor()->setRGB($isSettled ? '1e8449' : 'c0392b');

            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
            $sno++;
        }

        // ── Totals Row ────────────────────────────────────────────────────────
        $totalRow = $row;
        $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
        $sheet->setCellValue('A' . $totalRow, 'TOTAL (' . count($invoices) . ' Invoices)');
        $sheet->setCellValue('E' . $totalRow, (float) $data['totalSubtotal']);    // MRP sum
        $sheet->setCellValue('F' . $totalRow, (float) $data['totalDiscount']);
        $sheet->setCellValue('G' . $totalRow, (float) $data['totalMrp']);         // Net amt sum
        $sheet->setCellValue('H' . $totalRow, (float) $data['totalPaid']);
        $sheet->setCellValue('I' . $totalRow, (float) $data['totalCommission']);
        $sheet->setCellValue('J' . $totalRow, $data['avgCommissionPct'] . '%');
        $sheet->setCellValue('K' . $totalRow, '');
        $sheet->setCellValue('L' . $totalRow, '');

        $sheet->getStyle('A' . $totalRow . ':L' . $totalRow)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1a5276']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1a5276']]],
        ]);

        foreach (['E', 'F', 'G', 'H', 'I'] as $amtCol) {
            $sheet->getStyle($amtCol . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle($amtCol . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->getRowDimension($totalRow)->setRowHeight(26);

        return $spreadsheet;
    }

    /**
     * Determine branch restriction for the authenticated user
     */
    private function getMyBranchId($user): ?int
    {
        $restrictAccess = Configuration::getFor('restrict_branch_access', '1') === '1';
        $activeBranchId = session('active_branch_id', 'all');

        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = ($user->hasAnyRole(['lab_admin', 'super_admin']) ||
            collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
            && ! $user->hasRole('branch_admin');

        if ($isGlobalAdmin) {
            return ($activeBranchId === 'all' ? null : (int) $activeBranchId);
        }

        return $user->branch_id;
    }
}

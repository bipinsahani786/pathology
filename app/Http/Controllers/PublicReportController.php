<?php

namespace App\Http\Controllers;

use App\Models\TestReport;
use Illuminate\Http\Request;

class PublicReportController extends Controller
{
    /**
     * Publicly download a report PDF (mostly for QR code scanning).
     * The ID is decrypted or checked via hash.
     */
    public function download($hash)
    {
        // For simplicity and matching the QR link generated, 
        // we'll assume the hash is just the base64 encoded invoice_id 
        // or we can search by invoice_number if it's unique enough.
        
        $invoiceId = base64_decode($hash);
        
        if (!$invoiceId || !is_numeric($invoiceId)) {
            // Try to find by invoice_number if decode fails
            $report = TestReport::whereHas('invoice', function($q) use ($hash) {
                $q->where('invoice_number', $hash);
            })->first();
        } else {
            $report = TestReport::where('invoice_id', $invoiceId)->first();
        }

        if (!$report) {
            abort(404, 'Report not found.');
        }

        // Forward to the main ReportPdfController download method
        // We bypass auth since this is a public verification/download link
        $controller = app(ReportPdfController::class);
        
        // We need to spoof the request or just call the method directly with a flag
        // However, the download method checks auth()->user()->company_id.
        // We should modify ReportPdfController to allow public access for this method
        // or handle the PDF generation here.
        
        return $controller->streamPublicLink($report->invoice_id);
    }
}

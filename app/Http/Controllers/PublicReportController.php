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
        // Try to decode as base64 first (expected format: base64 encoded invoice_id)
        $invoiceId = base64_decode($hash, true);
        
        $report = null;

        if ($invoiceId && is_numeric($invoiceId)) {
            // Priority 1: Globally unique Invoice ID
            $report = TestReport::with('invoice.company')
                ->where('invoice_id', $invoiceId)
                ->latest()
                ->first();
        } 
        
        if (!$report) {
            // Priority 2: Search by Invoice Number (Fallback for legacy or manual links)
            // Note: This could collide across companies, so we take the latest one 
            // or we could ideally require a company prefix/slug in the future.
            $report = TestReport::with('invoice.company')
                ->whereHas('invoice', function($q) use ($hash) {
                    $q->where('invoice_number', $hash);
                })
                ->latest()
                ->first();
        }

        if (!$report) {
            abort(404, 'Report not found.');
        }

        // Forward to the main ReportPdfController download method
        $controller = app(ReportPdfController::class);
        return $controller->streamPublicLink($report->invoice_id);
    }
}

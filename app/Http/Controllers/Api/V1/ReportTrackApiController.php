<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportTrackApiController extends BaseApiController
{
    /**
     * Track invoice report status and obtain PDF download link.
     */
    public function track(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $validator = Validator::make($request->all(), [
            'bill_number' => 'required|string',
            'phone'       => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error.', 422, $validator->errors()->toArray());
        }

        $billNumber = trim($request->input('bill_number'));
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        $inputPhone10 = substr($cleanInputPhone, -10);

        // Find invoice for this company matching bill_number or invoice_number
        $invoice = Invoice::where('company_id', $company->id)
            ->where(function ($q) use ($billNumber) {
                $q->where('invoice_number', $billNumber)
                  ->orWhere('bill_number', $billNumber);
            })
            ->with(['patient', 'items', 'testReport'])
            ->first();

        if (!$invoice) {
            return $this->error('No invoice found for the provided Bill Number.', 404);
        }

        // Verify patient phone number (compare last 10 digits for safety)
        $patientPhone = preg_replace('/[^0-9]/', '', $invoice->patient?->phone ?? '');
        $patientPhone10 = substr($patientPhone, -10);

        if (empty($patientPhone10) || $inputPhone10 !== $patientPhone10) {
            return $this->error('Provided phone number does not match our records for this bill.', 403);
        }

        $report = $invoice->testReport;
        $isReady = false;
        $downloadUrl = null;

        // Check if report is approved or finalized
        if ($report && in_array(strtolower($report->status ?? ''), ['approved', 'finalized', 'completed'])) {
            $isReady = true;
            $downloadUrl = route('public.report.download', ['hash' => base64_encode($invoice->id)]);
        } elseif ($invoice->sample_status === 'Ready' && $report) {
            $isReady = true;
            $downloadUrl = route('public.report.download', ['hash' => base64_encode($invoice->id)]);
        }

        // Test items breakdown
        $tests = $invoice->items->map(function ($item) {
            return [
                'name'   => $item->test_name,
                'status' => $item->status ?? 'pending',
            ];
        });

        // Determine clear patient-friendly status message
        $stage = 'Sample Collection Pending';
        if ($isReady) {
            $stage = 'Report Ready';
        } elseif ($invoice->sample_status === 'Processing') {
            $stage = 'Report Under Analysis';
        } elseif ($invoice->sample_status === 'Collected') {
            $stage = 'Sample Received at Lab';
        }

        return $this->success([
            'bill_number'          => $invoice->invoice_number,
            'patient_name'         => $invoice->patient?->name ?? 'Patient',
            'invoice_date'         => $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : $invoice->created_at->format('Y-m-d'),
            'expected_report_time' => $invoice->expected_report_time ? $invoice->expected_report_time->format('Y-m-d H:i') : null,
            'current_stage'        => $stage,
            'is_ready'             => $isReady,
            'download_url'         => $downloadUrl,
            'tests'                => $tests,
        ], $isReady ? 'Report is ready for download.' : 'Report is currently in progress.');
    }
}

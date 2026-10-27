<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Models\WebBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportTrackApiController extends BaseApiController
{
    /**
     * Track invoice / booking report status and obtain PDF download link.
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

        $billNumber = trim(ltrim(trim($request->input('bill_number')), '#'));
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $request->input('phone'));
        $inputPhone10 = substr($cleanInputPhone, -10);

        try {
            // 1. Direct Invoice lookup matching invoice_number or barcode (case-insensitive)
            $invoice = Invoice::where('company_id', $company->id)
                ->where(function ($q) use ($billNumber) {
                    $q->where('invoice_number', 'ilike', $billNumber)
                      ->orWhere('barcode', 'ilike', $billNumber);
                })
                ->with(['patient', 'items', 'testReport'])
                ->first();

            // 2. If not found by invoice number, check WebBookings by booking_reference or external_booking_reference
            $webBooking = null;
            if (!$invoice) {
                $webBooking = WebBooking::where('company_id', $company->id)
                    ->where(function ($q) use ($billNumber) {
                        $q->where('booking_reference', 'ilike', $billNumber)
                          ->orWhere('external_booking_reference', 'ilike', $billNumber);
                    })
                    ->first();

                if ($webBooking && $webBooking->invoice_id) {
                    $invoice = Invoice::where('company_id', $company->id)
                        ->where('id', $webBooking->invoice_id)
                        ->with(['patient', 'items', 'testReport'])
                        ->first();
                }
            }

            // If neither invoice nor web booking found
            if (!$invoice && !$webBooking) {
                return $this->error('No record found for the provided Reference / Bill Number.', 404);
            }

            // Verify patient phone number (compare last 10 digits for safety)
            $patientPhone = $invoice
                ? ($invoice->patient?->phone ?? '')
                : ($webBooking->patient_phone ?? '');

            $cleanRecordPhone = preg_replace('/[^0-9]/', '', $patientPhone);
            $recordPhone10 = substr($cleanRecordPhone, -10);

            if (empty($recordPhone10) || $inputPhone10 !== $recordPhone10) {
                return $this->error('Provided phone number does not match our records for this booking/bill.', 403);
            }

            // If booking exists but not yet converted to an invoice in lab
            if (!$invoice && $webBooking) {
                $items = is_array($webBooking->items) ? $webBooking->items : json_decode($webBooking->items, true) ?? [];
                $tests = array_map(function ($it) {
                    return [
                        'name'   => $it['name'] ?? 'Test',
                        'status' => 'Pending Review',
                    ];
                }, $items);

                $statusStage = ($webBooking->status === 'confirmed')
                    ? 'Appointment Confirmed - Phlebotomist Assigned'
                    : 'Booking Received - Awaiting Lab Acceptance';

                return $this->success([
                    'bill_number'          => $webBooking->booking_reference,
                    'barcode'              => null,
                    'patient_name'         => $webBooking->patient_name,
                    'invoice_date'         => $webBooking->preferred_date ? $webBooking->preferred_date->format('Y-m-d') : $webBooking->created_at->format('Y-m-d'),
                    'expected_report_time' => null,
                    'current_stage'        => $statusStage,
                    'is_ready'             => false,
                    'download_url'         => null,
                    'tests'                => $tests,
                ], 'Appointment booking is received and in progress.');
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
            $tests = $invoice->items ? $invoice->items->map(function ($item) {
                return [
                    'name'   => $item->test_name ?? 'Test',
                    'status' => $item->status ?? 'pending',
                ];
            }) : [];

            // Determine clear patient-friendly status message
            $stage = 'Sample Collection Pending';
            if ($isReady) {
                $stage = 'Report Ready';
            } elseif ($invoice->sample_status === 'Processing') {
                $stage = 'Report Under Analysis';
            } elseif ($invoice->sample_status === 'Collected') {
                $stage = 'Sample Received at Lab';
            }

            $invoiceDate = null;
            if ($invoice->invoice_date) {
                $invoiceDate = \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d');
            } elseif ($invoice->created_at) {
                $invoiceDate = \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d');
            }

            $expectedTime = null;
            if ($invoice->expected_report_time) {
                $expectedTime = \Carbon\Carbon::parse($invoice->expected_report_time)->format('Y-m-d H:i');
            }

            return $this->success([
                'bill_number'          => $invoice->invoice_number,
                'barcode'              => $invoice->barcode,
                'patient_name'         => $invoice->patient?->name ?? 'Patient',
                'invoice_date'         => $invoiceDate,
                'expected_report_time' => $expectedTime,
                'current_stage'        => $stage,
                'is_ready'             => $isReady,
                'download_url'         => $downloadUrl,
                'tests'                => $tests,
            ], $isReady ? 'Report is ready for download.' : 'Report is currently in progress.');
        } catch (\Exception $e) {
            return $this->error('Failed to track report: ' . $e->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LabTest;
use App\Models\WebBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingApiController extends BaseApiController
{
    /**
     * Submit an online appointment / booking from the lab's external website.
     */
    public function store(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $validator = Validator::make($request->all(), [
            'patient_name'                => 'required|string|max:255',
            'patient_phone'               => 'required|string|min:10|max:20',
            'patient_email'               => 'nullable|email|max:255',
            'patient_gender'              => 'nullable|string|in:male,female,other',
            'patient_age'                 => 'nullable|integer|min:0|max:150',
            'patient_age_unit'            => 'nullable|string|in:years,months,days',
            'collection_type'             => 'required|string|in:lab_visit,home_collection',
            'collection_address'          => 'required_if:collection_type,home_collection|nullable|string|max:1000',
            'preferred_date'              => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'preferred_time_slot'         => 'nullable|string|max:100',
            'branch_id'                   => 'nullable|integer|exists:branches,id',
            'external_booking_reference'  => 'nullable|string|max:50',
            'payment_method'              => 'nullable|string|max:30',
            'payment_status'              => 'nullable|string|max:20',
            'paid_amount'                 => 'nullable|numeric|min:0',
            'transaction_id'              => 'nullable|string|max:100',
            'assigned_collector_name'     => 'nullable|string|max:255',
            'assigned_collector_phone'    => 'nullable|string|max:20',
            'assigned_collector_id'       => 'nullable|integer',
            'webhook_url'                 => 'nullable|string|max:500',
            'notes'                       => 'nullable|string|max:1000',
            'items'                       => 'required|array|min:1',
            'items.*.type'                => 'nullable|string|in:test,package',
            'items.*.id'                  => 'nullable|integer',
            'items.*.name'                => 'nullable|string|max:255',
            'items.*.test_code'           => 'nullable|string|max:50',
            'items.*.price'               => 'nullable|numeric|min:0',
        ], [
            'collection_address.required_if' => 'Collection address is required when selecting Home Collection.',
            'items.required'                 => 'At least one test or package must be selected.',
            'items.min'                      => 'Please select at least one test or package.',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation error.', 422, $validator->errors()->toArray());
        }

        $validated = $validator->validated();

        // Resolve requested items from database to compute tamper-proof prices
        $resolvedItems = [];
        $subtotal = 0.00;

        foreach ($validated['items'] as $itemInput) {
            $test = null;
            $itemId = $itemInput['id'] ?? null;
            $itemCode = $itemInput['test_code'] ?? null;
            $itemName = $itemInput['name'] ?? null;

            if ($itemId) {
                $test = LabTest::where('company_id', $company->id)
                    ->where('is_active', true)
                    ->find($itemId);
            }

            if (!$test && $itemCode) {
                $test = LabTest::where('company_id', $company->id)
                    ->where('is_active', true)
                    ->where('test_code', $itemCode)
                    ->first();
            }

            if (!$test && $itemName) {
                $test = LabTest::where('company_id', $company->id)
                    ->where('is_active', true)
                    ->where('name', $itemName)
                    ->first();
            }

            if ($test) {
                $price = (float) $test->mrp;
                $subtotal += $price;

                $resolvedItems[] = [
                    'type'        => $test->is_package ? 'package' : 'test',
                    'id'          => $test->id,
                    'name'        => $test->name,
                    'test_code'   => $test->test_code,
                    'price'       => $price,
                    'sample_type' => $test->sample_type ?: 'Blood',
                ];
            } else {
                // Fallback to provided payload data if test ID not found locally
                $price = (float) ($itemInput['price'] ?? 0);
                $subtotal += $price;

                $resolvedItems[] = [
                    'type'        => ($itemInput['type'] ?? 'test') === 'package' ? 'package' : 'test',
                    'id'          => $itemId ?: 0,
                    'name'        => $itemName ?: 'Laboratory Test',
                    'test_code'   => $itemCode ?: '',
                    'price'       => $price,
                    'sample_type' => 'Blood',
                ];
            }
        }

        $bookingRef = WebBooking::generateReference();
        $extRef = !empty($validated['external_booking_reference']) ? trim($validated['external_booking_reference']) : null;
        $paymentStatus = !empty($validated['payment_status']) ? strtolower(trim($validated['payment_status'])) : 'pending';
        $paidAmount = isset($validated['paid_amount']) ? (float) $validated['paid_amount'] : ($paymentStatus === 'paid' ? $subtotal : 0.00);

        // Check if an existing booking with this external reference already exists (prevent duplicate creation)
        if ($extRef) {
            $existing = WebBooking::where('company_id', $company->id)
                ->where('external_booking_reference', $extRef)
                ->first();

            if ($existing) {
                return $this->success([
                    'booking_reference'          => $existing->booking_reference,
                    'external_booking_reference' => $existing->external_booking_reference,
                    'status'                     => $existing->status,
                    'patient_name'               => $existing->patient_name,
                    'patient_phone'              => $existing->patient_phone,
                    'collection_type'            => $existing->collection_type,
                    'preferred_date'             => $existing->preferred_date ? $existing->preferred_date->format('Y-m-d') : null,
                    'preferred_time_slot'        => $existing->preferred_time_slot,
                    'total_amount'               => (float) $existing->total_amount,
                    'payment_status'             => $existing->payment_status,
                    'paid_amount'                => (float) $existing->paid_amount,
                    'items_count'                => count(is_array($existing->items) ? $existing->items : []),
                    'created_at'                 => $existing->created_at->toIso8601String(),
                ], 'Booking already exists with this reference.', 200);
            }
        }

        $booking = WebBooking::create([
            'company_id'                 => $company->id,
            'branch_id'                  => $validated['branch_id'] ?? null,
            'booking_reference'          => $bookingRef,
            'external_booking_reference' => $extRef,
            'webhook_url'                => $validated['webhook_url'] ?? null,
            'patient_name'               => trim($validated['patient_name']),
            'patient_phone'              => trim($validated['patient_phone']),
            'patient_email'              => $validated['patient_email'] ?? null,
            'patient_gender'             => $validated['patient_gender'] ?? null,
            'patient_age'                => $validated['patient_age'] ?? null,
            'patient_age_unit'           => $validated['patient_age_unit'] ?? 'years',
            'collection_type'            => $validated['collection_type'],
            'collection_address'         => $validated['collection_address'] ?? null,
            'preferred_date'             => $validated['preferred_date'] ?? null,
            'preferred_time_slot'        => $validated['preferred_time_slot'] ?? null,
            'items'                      => $resolvedItems,
            'subtotal'                   => $subtotal,
            'discount'                   => 0.00,
            'total_amount'               => $subtotal,
            'payment_method'             => $validated['payment_method'] ?? 'cash',
            'payment_status'             => $paymentStatus,
            'paid_amount'                => $paidAmount,
            'transaction_id'             => $validated['transaction_id'] ?? null,
            'assigned_collector_name'    => $validated['assigned_collector_name'] ?? null,
            'assigned_collector_phone'   => $validated['assigned_collector_phone'] ?? null,
            'assigned_collector_id'      => $validated['assigned_collector_id'] ?? null,
            'status'                     => 'pending',
            'notes'                      => $validated['notes'] ?? null,
            'source_ip'                  => $request->ip(),
        ]);

        return $this->success([
            'booking_reference'          => $booking->booking_reference,
            'external_booking_reference' => $booking->external_booking_reference,
            'status'                     => $booking->status,
            'patient_name'               => $booking->patient_name,
            'patient_phone'              => $booking->patient_phone,
            'collection_type'            => $booking->collection_type,
            'preferred_date'             => $booking->preferred_date ? $booking->preferred_date->format('Y-m-d') : null,
            'preferred_time_slot'        => $booking->preferred_time_slot,
            'total_amount'               => (float) $booking->total_amount,
            'payment_status'             => $booking->payment_status,
            'paid_amount'                => (float) $booking->paid_amount,
            'items_count'                => count($resolvedItems),
            'items'                      => $resolvedItems,
            'created_at'                 => $booking->created_at->toIso8601String(),
        ], 'Appointment booked successfully. The lab staff will contact you shortly.', 201);
    }

    /**
     * Get booking details and real-time status by booking_reference or external_booking_reference.
     */
    public function show(Request $request, string $reference): JsonResponse
    {
        $company = $this->getCompany($request);
        $cleanRef = trim(ltrim(trim($reference), '#'));

        $booking = WebBooking::where('company_id', $company->id)
            ->where(function ($q) use ($cleanRef) {
                $q->where('booking_reference', $cleanRef)
                  ->orWhere('external_booking_reference', $cleanRef);
            })
            ->with(['branch', 'invoice.testReport', 'invoice.items'])
            ->first();

        if (!$booking) {
            return $this->error("No booking found with reference '{$reference}'.", 404);
        }

        $invoice = $booking->invoice;
        $report = $invoice?->testReport;
        $isReady = false;
        $downloadUrl = null;

        if ($report && in_array(strtolower($report->status ?? ''), ['approved', 'finalized', 'completed'])) {
            $isReady = true;
            $downloadUrl = route('public.report.download', ['hash' => base64_encode($invoice->id)]);
        } elseif ($invoice && $invoice->sample_status === 'Ready' && $report) {
            $isReady = true;
            $downloadUrl = route('public.report.download', ['hash' => base64_encode($invoice->id)]);
        }

        $homeVisit = $invoice ? \App\Models\HomeCollection::where('invoice_id', $invoice->id)->with('phlebotomist')->first() : null;
        $hvStatus = $homeVisit?->status;
        $isDispatched = (bool) (
            $homeVisit?->dispatched_at
            || $hvStatus === 'Dispatched'
            || $hvStatus === 'Received'
            || in_array($invoice?->sample_status, ['Dispatched', 'Received', 'In Process', 'Processing'])
        );
        $isCollected = (bool) (
            $homeVisit?->collected_at
            || $hvStatus === 'Collected'
            || $invoice?->sample_status === 'Sample Collected'
        );

        $resolvedSampleStatus = 'Pending';
        if ($isDispatched) {
            $resolvedSampleStatus = 'Dispatched';
        } elseif ($isCollected) {
            $resolvedSampleStatus = 'Sample Collected';
        } elseif (!empty($invoice?->sample_status)) {
            $resolvedSampleStatus = $invoice->sample_status;
        }

        $collectorName = $homeVisit?->phlebotomist?->name ?? $booking->assigned_collector_name;
        $collectorPhone = $homeVisit?->phlebotomist?->phone ?? $homeVisit?->phlebotomist?->mobile ?? $booking->assigned_collector_phone;

        $tests = [];
        if ($invoice && $invoice->items) {
            foreach ($invoice->items as $it) {
                $tests[] = [
                    'name'   => $it->test_name ?? 'Test',
                    'status' => $it->status ?? 'pending',
                ];
            }
        } elseif (!empty($booking->items)) {
            $rawItems = is_array($booking->items) ? $booking->items : json_decode($booking->items, true) ?? [];
            foreach ($rawItems as $it) {
                $tests[] = [
                    'name'   => $it['name'] ?? 'Test',
                    'status' => 'pending',
                ];
            }
        }

        return $this->success([
            'booking_reference'          => $booking->booking_reference,
            'external_booking_reference' => $booking->external_booking_reference,
            'status'                     => $booking->status,
            'is_converted_to_invoice'    => (bool) ($booking->invoice_id > 0),
            'invoice_id'                 => $booking->invoice_id,
            'invoice_number'             => $invoice?->invoice_number,
            'barcode'                    => $invoice?->barcode,
            'patient_name'               => $booking->patient_name,
            'patient_phone'              => $booking->patient_phone,
            'collection_type'            => $booking->collection_type,
            'collection_address'         => $booking->collection_address,
            'preferred_date'             => $booking->preferred_date ? $booking->preferred_date->format('Y-m-d') : null,
            'preferred_time_slot'        => $booking->preferred_time_slot,
            'subtotal'                   => (float) $booking->subtotal,
            'total_amount'               => (float) $booking->total_amount,
            'payment_method'             => $booking->payment_method,
            'payment_status'             => $invoice ? $invoice->payment_status : $booking->payment_status,
            'paid_amount'                => (float) ($invoice ? $invoice->paid_amount : $booking->paid_amount),
            'due_amount'                 => (float) ($invoice ? $invoice->due_amount : max(0, $booking->total_amount - $booking->paid_amount)),
            'sample_status'              => $resolvedSampleStatus,
            'home_collection_status'     => $hvStatus,
            'is_dispatched'              => $isDispatched,
            'dispatched_at'              => $homeVisit?->dispatched_at?->toIso8601String(),
            'collected_at'               => $homeVisit?->collected_at?->toIso8601String(),
            'report_status'              => $report?->status ?? 'pending',
            'is_report_ready'            => $isReady,
            'report_download_url'        => $downloadUrl,
            'assigned_collector_name'    => $collectorName,
            'assigned_collector_phone'   => $collectorPhone,
            'tests'                      => $tests,
            'created_at'                 => $booking->created_at->toIso8601String(),
            'updated_at'                 => $booking->updated_at->toIso8601String(),
        ], 'Booking status retrieved successfully.');
    }
}

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
            'patient_name'        => 'required|string|max:255',
            'patient_phone'       => 'required|string|min:10|max:20',
            'patient_email'       => 'nullable|email|max:255',
            'patient_gender'      => 'nullable|string|in:male,female,other',
            'patient_age'         => 'nullable|integer|min:0|max:150',
            'patient_age_unit'    => 'nullable|string|in:years,months,days',
            'collection_type'     => 'required|string|in:lab_visit,home_collection',
            'collection_address'  => 'required_if:collection_type,home_collection|nullable|string|max:1000',
            'preferred_date'      => 'nullable|date_format:Y-m-d|after_or_equal:today',
            'preferred_time_slot' => 'nullable|string|max:100',
            'branch_id'           => 'nullable|integer|exists:branches,id',
            'notes'               => 'nullable|string|max:1000',
            'items'               => 'required|array|min:1',
            'items.*.type'        => 'required|string|in:test,package',
            'items.*.id'          => 'required|integer',
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
            $test = LabTest::where('company_id', $company->id)
                ->where('is_active', true)
                ->find($itemInput['id']);

            if (!$test) {
                return $this->error("Selected item ID {$itemInput['id']} was not found or is inactive.", 422);
            }

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
        }

        $bookingRef = WebBooking::generateReference();

        $booking = WebBooking::create([
            'company_id'          => $company->id,
            'branch_id'           => $validated['branch_id'] ?? null,
            'booking_reference'   => $bookingRef,
            'patient_name'        => trim($validated['patient_name']),
            'patient_phone'       => trim($validated['patient_phone']),
            'patient_email'       => $validated['patient_email'] ?? null,
            'patient_gender'      => $validated['patient_gender'] ?? null,
            'patient_age'         => $validated['patient_age'] ?? null,
            'patient_age_unit'    => $validated['patient_age_unit'] ?? 'years',
            'collection_type'     => $validated['collection_type'],
            'collection_address'  => $validated['collection_address'] ?? null,
            'preferred_date'      => $validated['preferred_date'] ?? null,
            'preferred_time_slot' => $validated['preferred_time_slot'] ?? null,
            'items'               => $resolvedItems,
            'subtotal'            => $subtotal,
            'discount'            => 0.00,
            'total_amount'        => $subtotal,
            'status'              => 'pending',
            'notes'               => $validated['notes'] ?? null,
            'source_ip'           => $request->ip(),
        ]);

        return $this->success([
            'booking_reference'   => $booking->booking_reference,
            'status'              => $booking->status,
            'patient_name'        => $booking->patient_name,
            'patient_phone'       => $booking->patient_phone,
            'collection_type'     => $booking->collection_type,
            'preferred_date'      => $booking->preferred_date ? $booking->preferred_date->format('Y-m-d') : null,
            'preferred_time_slot' => $booking->preferred_time_slot,
            'total_amount'        => (float) $booking->total_amount,
            'items_count'         => count($resolvedItems),
            'items'               => $resolvedItems,
            'created_at'          => $booking->created_at->toIso8601String(),
        ], 'Appointment booked successfully. The lab staff will contact you shortly.', 201);
    }
}

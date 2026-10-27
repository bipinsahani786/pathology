<?php

namespace App\Livewire\Lab;

use App\Models\Branch;
use App\Models\Configuration;
use App\Models\HomeCollection;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTest;
use App\Models\PatientProfile;
use App\Models\Payment;
use App\Models\PaymentMode;
use App\Models\User;
use App\Models\WebBooking;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class WebBookingManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $statusFilter = 'all';
    public $collectionTypeFilter = 'all';
    public $branchFilter = 'all';

    // Detail Modal
    public $selectedBooking = null;
    public $isDetailModalOpen = false;

    public function mount()
    {
        $this->authorize('view pos');

        $company = auth()->user()->company;
        if (!$company || !$company->hasWebsiteApiFeature()) {
            abort(403, 'External Website API & Bookings feature is not enabled in your subscription plan. Contact Superadmin to upgrade.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCollectionTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingBranchFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'collectionTypeFilter', 'branchFilter']);
        $this->resetPage();
    }

    public function viewBooking($id)
    {
        $booking = WebBooking::with(['branch', 'invoice'])
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        $this->selectedBooking = $booking->toArray();
        $this->isDetailModalOpen = true;
    }

    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->selectedBooking = null;
    }

    public function updateStatus($bookingId, $status)
    {
        $this->authorize('create pos');

        $validStatuses = ['pending', 'confirmed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            return;
        }

        $booking = WebBooking::where('company_id', auth()->user()->company_id)->findOrFail($bookingId);
        $booking->update(['status' => $status]);

        if ($this->selectedBooking && $this->selectedBooking['id'] == $bookingId) {
            $this->selectedBooking['status'] = $status;
        }

        session()->flash('message', "Booking reference {$booking->booking_reference} marked as {$status}.");
    }

    /**
     * 1-Click Convert Web Booking to POS Invoice & Patient Profile
     */
    public function convertToInvoice($bookingId)
    {
        $this->authorize('create pos');

        $companyId = auth()->user()->company_id;
        $booking = WebBooking::where('company_id', $companyId)->findOrFail($bookingId);

        if ($booking->status === 'converted_to_invoice' && $booking->invoice_id) {
            session()->flash('error', 'This booking has already been converted to an invoice.');
            return redirect()->route('lab.pos.summary', $booking->invoice_id);
        }

        try {
            DB::beginTransaction();

            // 1. Locate or create Patient User & PatientProfile
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $booking->patient_phone);
            $phone10 = (strlen($cleanPhone) >= 10) ? substr($cleanPhone, -10) : $cleanPhone;
            $inputEmail = !empty($booking->patient_email) ? trim(strtolower($booking->patient_email)) : null;

            // 1a. Try finding user in this company by phone OR email
            $user = User::where('company_id', $companyId)
                ->where(function ($q) use ($cleanPhone, $phone10, $inputEmail) {
                    if (!empty($phone10)) {
                        $q->where('phone', 'like', "%{$phone10}%");
                    }
                    if (!empty($inputEmail)) {
                        $q->orWhereRaw('LOWER(email) = ?', [$inputEmail]);
                    }
                })
                ->first();

            // 1b. If not found in this company, check if user exists globally by email
            if (!$user && $inputEmail) {
                $userWithEmail = User::whereRaw('LOWER(email) = ?', [$inputEmail])->first();
                if ($userWithEmail && $userWithEmail->company_id == $companyId) {
                    $user = $userWithEmail;
                }
            }

            // 1c. If still not found, create new User safely handling unique email
            if (!$user) {
                $emailToUse = null;
                if ($inputEmail && !User::whereRaw('LOWER(email) = ?', [$inputEmail])->exists()) {
                    $emailToUse = $inputEmail;
                } else {
                    $emailToUse = 'patient_' . ($phone10 ?: uniqid()) . '_' . substr(uniqid(), -4) . '@pathology.local';
                }

                $user = User::create([
                    'company_id' => $companyId,
                    'name'       => $booking->patient_name,
                    'email'      => $emailToUse,
                    'phone'      => $cleanPhone ?: $booking->patient_phone,
                    'password'   => bcrypt(uniqid()),
                    'address'    => $booking->collection_address,
                ]);
            }

            // Ensure patient role is attached
            if (method_exists($user, 'hasRole') && !$user->hasRole('patient')) {
                try {
                    $user->assignRole('patient');
                } catch (\Throwable $rErr) {
                    // Role already exists
                }
            }

            // 1d. Locate or create PatientProfile (handles case where user existed previously without profile)
            $profile = PatientProfile::where('company_id', $companyId)->where('user_id', $user->id)->first();
            if (!$profile) {
                // Generate Patient ID
                $pPrefix = Configuration::getFor('patient_id_prefix', 'PAT', $companyId);
                $pDigits = (int) Configuration::getFor('patient_id_digits', 4, $companyId);
                $maxLocalId = PatientProfile::where('company_id', $companyId)->max('company_patient_number') ?? 0;
                $nextPId = $maxLocalId + 1;
                $patientIdString = $pPrefix . str_pad($nextPId, $pDigits, '0', STR_PAD_LEFT);

                while (PatientProfile::where('company_id', $companyId)->where('patient_id_string', $patientIdString)->exists()) {
                    $nextPId++;
                    $patientIdString = $pPrefix . str_pad($nextPId, $pDigits, '0', STR_PAD_LEFT);
                }

                // Strictly enforce Postgres CHECK constraint values: 'Years', 'Months', 'Days'
                $rawAgeUnit = strtolower((string) ($booking->patient_age_unit ?: 'years'));
                $ageType = match (true) {
                    str_contains($rawAgeUnit, 'month') => 'Months',
                    str_contains($rawAgeUnit, 'day')   => 'Days',
                    default                            => 'Years',
                };

                // Strictly enforce Postgres CHECK constraint values: 'Male', 'Female', 'Other'
                $rawGender = strtolower((string) ($booking->patient_gender ?: 'other'));
                $gender = match (true) {
                    str_starts_with($rawGender, 'm') => 'Male',
                    str_starts_with($rawGender, 'f') => 'Female',
                    default                          => 'Other',
                };

                $profile = PatientProfile::create([
                    'company_id'             => $companyId,
                    'user_id'                => $user->id,
                    'patient_id_string'      => $patientIdString,
                    'company_patient_number' => $nextPId,
                    'age'                    => (int) ($booking->patient_age ?: 30),
                    'age_type'               => $ageType,
                    'gender'                 => $gender,
                    'address'                => $booking->collection_address,
                ]);
            }

            // 2. Generate Invoice Number & Barcode
            $branchId = $booking->branch_id ?: auth()->user()->branch_id ?: (Branch::where('company_id', $companyId)->first()->id ?? null);
            $prefix = Configuration::getFor('invoice_prefix', 'INV', $companyId, $branchId);
            $sep = Configuration::getFor('invoice_separator', '-', $companyId, $branchId);
            $digits = (int) Configuration::getFor('invoice_counter_digits', 4, $companyId, $branchId);
            $dateFormat = Configuration::getFor('invoice_date_format', 'ym', $companyId, $branchId);

            $dateMap = [
                'ym'   => date('ym'),
                'ymd'  => date('ymd'),
                'Ymd'  => date('Ymd'),
                'Y'    => date('Y'),
                'none' => '',
            ];
            $datePart = $dateMap[$dateFormat] ?? date('ym');
            $maxInvId = Invoice::where('company_id', $companyId)->max('id') ?? 0;
            $nextId = $maxInvId + 1;

            $bcPrefix = Configuration::getFor('barcode_prefix', 'LAB', $companyId, $branchId);
            $bcCounterDigits = (int) Configuration::getFor('barcode_counter_digits', 6, $companyId, $branchId);

            do {
                $counter = str_pad($nextId, $digits, '0', STR_PAD_LEFT);
                $parts = array_filter([$prefix, $datePart, $counter]);
                $invoiceNumber = implode($sep, $parts);
                $barcode = $bcPrefix . date('ymd') . str_pad($nextId, $bcCounterDigits, '0', STR_PAD_LEFT);

                $exists = Invoice::where('company_id', $companyId)
                    ->where(function ($q) use ($invoiceNumber, $barcode) {
                        $q->where('invoice_number', $invoiceNumber)->orWhere('barcode', $barcode);
                    })->exists();

                if ($exists) {
                    $nextId++;
                }
            } while ($exists);

            // 3. Calculate Payment Status from WebBooking
            $collectionTypeFormatted = ($booking->collection_type === 'home_collection') ? 'Home Collection' : 'Lab Visit';
            $ccId = auth()->user()->collection_center_id 
                ?? \App\Models\CollectionCenter::where('company_id', $companyId)->where('is_main_lab', true)->value('id')
                ?? \App\Models\CollectionCenter::where('company_id', $companyId)->value('id');

            $isPaidOnline = (strtolower((string) $booking->payment_status) === 'paid');
            $paidAmt = $isPaidOnline ? (float) ($booking->paid_amount ?: $booking->total_amount) : 0.00;
            $dueAmt = max(0.00, (float) $booking->total_amount - $paidAmt);
            $paymentStatus = $isPaidOnline ? ($dueAmt == 0 ? 'Paid' : 'Partial') : 'Unpaid';

            $invoice = Invoice::create([
                'company_id'              => $companyId,
                'branch_id'               => $branchId,
                'collection_center_id'    => $ccId,
                'patient_id'              => $user->id,
                'created_by'              => auth()->id(),
                'invoice_number'          => $invoiceNumber,
                'barcode'                 => $barcode,
                'invoice_date'            => now(),
                'collection_type'         => $collectionTypeFormatted,
                'home_collection_address' => $booking->collection_address,
                'subtotal'                => $booking->subtotal,
                'discount_amount'         => $booking->discount,
                'total_amount'            => $booking->total_amount,
                'paid_amount'             => $paidAmt,
                'due_amount'              => $dueAmt,
                'payment_status'          => $paymentStatus,
                'sample_status'           => 'Pending',
                'status'                  => 'Pending',
            ]);

            // 4. Create Payment ledger entry if already paid online
            if ($isPaidOnline && $paidAmt > 0) {
                $onlineModeId = PaymentMode::where('company_id', $companyId)
                    ->where(function ($q) {
                        $q->where('name', 'ilike', '%online%')
                          ->orWhere('name', 'ilike', '%upi%')
                          ->orWhere('name', 'ilike', '%razorpay%');
                    })->value('id') ?? PaymentMode::where('company_id', $companyId)->value('id');

                Payment::create([
                    'company_id'      => $companyId,
                    'invoice_id'      => $invoice->id,
                    'patient_id'      => $user->id,
                    'payment_mode_id' => $onlineModeId,
                    'amount'          => $paidAmt,
                    'collected_by'    => auth()->id(),
                    'transaction_id'  => $booking->transaction_id,
                    'remarks'         => "Online payment synced from OutsourceLab (#" . ($booking->external_booking_reference ?: $booking->booking_reference) . ")",
                ]);
            }

            // 5. Create HomeCollection entry if home collection
            if ($booking->collection_type === 'home_collection') {
                $phleboUserId = null;
                if ($booking->assigned_collector_id) {
                    $phleboUserId = User::where('company_id', $companyId)
                        ->where('id', $booking->assigned_collector_id)
                        ->whereHas('roles', fn($q) => $q->where('name', 'phlebotomist'))
                        ->value('id');
                }
                if (!$phleboUserId && $booking->assigned_collector_phone) {
                    $phleboUserId = User::where('company_id', $companyId)
                        ->where('phone', preg_replace('/[^0-9]/', '', $booking->assigned_collector_phone))
                        ->whereHas('roles', fn($q) => $q->where('name', 'phlebotomist'))
                        ->value('id');
                }

                HomeCollection::create([
                    'company_id'         => $companyId,
                    'branch_id'          => $branchId,
                    'invoice_id'         => $invoice->id,
                    'patient_id'         => $user->id,
                    'phlebotomist_id'    => $phleboUserId,
                    'assigned_by'        => $phleboUserId ? auth()->id() : null,
                    'assigned_at'        => $phleboUserId ? now() : null,
                    'collection_address' => $booking->collection_address,
                    'scheduled_date'     => $booking->preferred_date ? $booking->preferred_date->format('Y-m-d') : now()->format('Y-m-d'),
                    'status'             => $phleboUserId ? 'Assigned' : 'Pending',
                    'notes'              => "Web Booking #{$booking->booking_reference}" . ($booking->external_booking_reference ? " (OutsourceLab: #{$booking->external_booking_reference})" : "") . ($booking->notes ? " - {$booking->notes}" : ""),
                ]);
            }

            // 6. Create Invoice Items
            $items = is_array($booking->items) ? $booking->items : json_decode($booking->items, true) ?? [];
            $sortOrder = 1;

            foreach ($items as $item) {
                $labTestId = null;
                $rawId = $item['id'] ?? null;
                if (!empty($rawId) && is_numeric($rawId) && $rawId > 0) {
                    $exists = LabTest::where('company_id', $companyId)->where('id', (int) $rawId)->exists();
                    if ($exists) {
                        $labTestId = (int) $rawId;
                    }
                }

                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'lab_test_id' => $labTestId,
                    'test_name'   => $item['name'] ?? 'Test',
                    'is_package'  => ($item['type'] ?? '') === 'package',
                    'mrp'         => (float) ($item['price'] ?? 0),
                    'price'       => (float) ($item['price'] ?? 0),
                    'sort_order'  => $sortOrder++,
                    'status'      => 'Pending',
                ]);
            }

            // 7. Link WebBooking to Invoice
            $booking->update([
                'invoice_id' => $invoice->id,
                'status'     => 'converted_to_invoice',
            ]);

            DB::commit();

            session()->flash('message', "Booking #{$booking->booking_reference} converted successfully into Invoice #{$invoice->invoice_number}!");
            return redirect()->route('lab.pos.summary', $invoice->id);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to convert booking: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        // Stats Query
        $stats = [
            'total'     => WebBooking::where('company_id', $companyId)->count(),
            'pending'   => WebBooking::where('company_id', $companyId)->where('status', 'pending')->count(),
            'confirmed' => WebBooking::where('company_id', $companyId)->where('status', 'confirmed')->count(),
            'converted' => WebBooking::where('company_id', $companyId)->where('status', 'converted_to_invoice')->count(),
        ];

        // List Query
        $query = WebBooking::with(['branch', 'invoice'])
            ->where('company_id', $companyId);

        if ($this->search) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('patient_name', 'like', "%{$term}%")
                  ->orWhere('patient_phone', 'like', "%{$term}%")
                  ->orWhere('booking_reference', 'like', "%{$term}%")
                  ->orWhere('external_booking_reference', 'like', "%{$term}%");
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->collectionTypeFilter !== 'all') {
            $query->where('collection_type', $this->collectionTypeFilter);
        }

        if ($this->branchFilter !== 'all') {
            $query->where('branch_id', $this->branchFilter);
        }

        $bookings = $query->latest()->paginate(15);
        $branches = Branch::where('company_id', $companyId)->get();

        return view('livewire.lab.web-booking-manager', [
            'bookings' => $bookings,
            'branches' => $branches,
            'stats'    => $stats,
        ])->layout('layouts.app', ['title' => 'Web Bookings']);
    }
}

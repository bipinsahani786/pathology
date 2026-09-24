<?php

namespace App\Livewire\Lab;

use App\Models\Branch;
use App\Models\Configuration;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabTest;
use App\Models\PatientProfile;
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
            $cleanPhone = preg_replace('/[^0-9]/', '', $booking->patient_phone);
            $user = User::where('company_id', $companyId)
                ->where(function ($q) use ($cleanPhone, $booking) {
                    $q->where('phone', $cleanPhone)
                      ->orWhere('phone', $booking->patient_phone);
                })
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'patient');
                })
                ->first();

            if (!$user) {
                // Generate safe email if not provided
                $email = $booking->patient_email ?: ('patient_' . $cleanPhone . '_' . uniqid() . '@pathology.local');

                $user = User::create([
                    'company_id' => $companyId,
                    'name'       => $booking->patient_name,
                    'email'      => $email,
                    'phone'      => $cleanPhone,
                    'password'   => bcrypt(uniqid()),
                    'address'    => $booking->collection_address,
                ]);

                // Assign role
                $user->assignRole('patient');

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

                PatientProfile::create([
                    'company_id'             => $companyId,
                    'user_id'                => $user->id,
                    'patient_id_string'      => $patientIdString,
                    'company_patient_number' => $nextPId,
                    'age'                    => $booking->patient_age,
                    'age_type'               => $booking->patient_age_unit ?: 'years',
                    'gender'                 => $booking->patient_gender ?: 'other',
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

            // 3. Create Invoice
            $collectionTypeFormatted = ($booking->collection_type === 'home_collection') ? 'Home Collection' : 'Lab Visit';
            $ccId = auth()->user()->collection_center_id 
                ?? \App\Models\CollectionCenter::where('company_id', $companyId)->where('is_main_lab', true)->value('id')
                ?? \App\Models\CollectionCenter::where('company_id', $companyId)->value('id');

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
                'paid_amount'             => 0,
                'due_amount'              => $booking->total_amount,
                'payment_status'          => 'Unpaid',
                'sample_status'           => 'Pending',
                'status'                  => 'Pending',
            ]);

            // 4. Create Invoice Items
            $items = is_array($booking->items) ? $booking->items : json_decode($booking->items, true) ?? [];
            $sortOrder = 1;

            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'lab_test_id' => $item['id'] ?? null,
                    'test_name'   => $item['name'] ?? 'Test',
                    'is_package'  => ($item['type'] ?? '') === 'package',
                    'mrp'         => (float) ($item['price'] ?? 0),
                    'price'       => (float) ($item['price'] ?? 0),
                    'sort_order'  => $sortOrder++,
                    'status'      => 'Pending',
                ]);
            }

            // 5. Link WebBooking to Invoice
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
                  ->orWhere('booking_reference', 'like', "%{$term}%");
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

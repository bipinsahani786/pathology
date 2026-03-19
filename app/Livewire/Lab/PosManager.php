<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\{User, LabTest, PaymentMode, Invoice, InvoiceItem, Payment, CollectionCenter, Branch, PatientProfile, DoctorProfile, AgentProfile, Membership, PatientMembership, Voucher, Company, Configuration, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PosManager extends Component
{
    // ==========================================
    // 1. SELECTIONS & SEARCH
    // ==========================================
    public $patientSearch = '', $selectedPatient = null, $patientProfileData = null;
    public $doctorSearch = '', $selectedDoctor = null, $doctorProfileData = null;
    public $agentSearch = '', $selectedAgent = null, $agentProfileData = null;

    // ==========================================
    // 2. LOGISTICS
    // ==========================================
    public $collection_center_id, $branch_id, $collection_type = 'Center';
    public $expected_report_date, $expected_report_time;

    // ==========================================
    // 3. CART & PRICING
    // ==========================================
    public $testSearch = '';
    public $cart = [];
    public $subtotal = 0;

    public $active_membership = null;
    public $membership_discount_amt = 0;
    public $membership_fee = 0;

    public $voucher_code = '';
    public $applied_voucher = null;
    public $voucher_discount_amt = 0;

    public $manual_discount_type = 'flat';
    public $manual_discount_input = 0;
    public $manual_discount_amt = 0;

    public $total_discount = 0;
    public $net_payable = 0;
    public $due_amount = 0;

    // ==========================================
    // 4. PAYMENTS
    // ==========================================
    public $payments = [];

    // ==========================================
    // 5. MODALS
    // ==========================================
    public $isPatientModalOpen = false, $isDoctorModalOpen = false;
    public $new_name, $new_phone, $new_age, $new_gender = 'Male';
    public $new_doc_name, $new_doc_phone, $new_doc_commission = 0;
    public $isMembershipModalOpen = false, $selectedMembershipId = null;
    public $purchasedMembershipRecordId = null;
    public $isPaymentModeModalOpen = false, $new_payment_mode_name = '';
    public $modalError = '';

    // ==========================================
    // 6. CART EXPAND
    // ==========================================
    public $expandedCartItems = [];

    public function mount()
    {
        $companyId = auth()->user()->company_id;
        $this->collection_center_id = CollectionCenter::where('company_id', $companyId)->first()->id ?? null;
        $this->branch_id = Branch::where('company_id', $companyId)->first()->id ?? null;
        $this->expected_report_date = date('Y-m-d');
        $this->expected_report_time = date('H:i', strtotime('+24 hours'));
        $this->addPaymentRow();
    }

    // ==========================================
    // SELECTIONS — FULL PROFILE LOAD
    // ==========================================
    public function selectPatient($userId)
    {
        $user = User::with(['patientProfile'])->find($userId);
        $this->selectedPatient = $user->toArray();
        $this->patientProfileData = $user->patientProfile ? $user->patientProfile->toArray() : null;
        $this->patientSearch = '';

        // Check active membership — only auto-apply if fully paid
        $record = PatientMembership::where('patient_id', $userId)
            ->where('is_active', true)
            ->where('valid_until', '>=', now())
            ->latest()->first();

        if ($record) {
            $membership = Membership::find($record->membership_id);
            // Only auto-apply if membership fee was fully paid
            if ($membership && $record->amount_paid >= $membership->price) {
                $this->active_membership = $membership->toArray();
            } else {
                $this->active_membership = null;
            }
        } else {
            $this->active_membership = null;
        }

        $this->membership_fee = 0;
        $this->purchasedMembershipRecordId = null;
        $this->calculateTotals();
    }

    public function selectDoctor($userId)
    {
        $user = User::with(['doctorProfile'])->find($userId);
        $this->selectedDoctor = $user->toArray();
        $this->doctorProfileData = $user->doctorProfile ? $user->doctorProfile->toArray() : null;
        $this->doctorSearch = '';
    }

    public function selectAgent($userId)
    {
        $user = User::with(['agentProfile'])->find($userId);
        $this->selectedAgent = $user->toArray();
        $this->agentProfileData = $user->agentProfile ? $user->agentProfile->toArray() : null;
        $this->agentSearch = '';
    }

    public function clearPatient() { $this->selectedPatient = null; $this->patientProfileData = null; $this->active_membership = null; $this->membership_fee = 0; $this->purchasedMembershipRecordId = null; $this->calculateTotals(); }
    public function clearDoctor() { $this->selectedDoctor = null; $this->doctorProfileData = null; }
    public function clearAgent() { $this->selectedAgent = null; $this->agentProfileData = null; }

    // ==========================================
    // CART LOGIC
    // ==========================================
    public function addTestToCart($testId)
    {
        $test = LabTest::findOrFail($testId);
        if (!collect($this->cart)->contains('id', $test->id)) {
            $cartItem = [
                'id' => $test->id,
                'name' => $test->name,
                'test_code' => $test->test_code,
                'mrp' => (float)$test->mrp,
                'is_package' => (bool)$test->is_package,
                'department' => $test->department,
                'sample_type' => $test->sample_type,
                'parameters' => $test->parameters ?? [],
                'linked_tests' => [],
            ];

            if ($test->is_package && !empty($test->linked_test_ids)) {
                $linkedTests = LabTest::whereIn('id', $test->linked_test_ids)->get();
                $cartItem['linked_tests'] = $linkedTests->map(fn($lt) => [
                    'id' => $lt->id,
                    'name' => $lt->name,
                    'test_code' => $lt->test_code,
                    'mrp' => (float)$lt->mrp,
                    'department' => $lt->department,
                    'parameters' => $lt->parameters ?? [],
                ])->toArray();
            }

            $this->cart[] = $cartItem;
            $this->calculateTotals();
        }
        $this->testSearch = '';
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
        $this->expandedCartItems = array_values(array_diff($this->expandedCartItems, [$index]));
        $this->calculateTotals();
    }

    public function toggleCartItemDetail($index)
    {
        if (in_array($index, $this->expandedCartItems)) {
            $this->expandedCartItems = array_values(array_diff($this->expandedCartItems, [$index]));
        } else {
            $this->expandedCartItems[] = $index;
        }
    }

    // ==========================================
    // MEMBERSHIP PURCHASE
    // ==========================================
    public function purchaseMembership()
    {
        $this->modalError = '';
        if (!$this->selectedPatient) { $this->modalError = 'Select a patient first.'; return; }
        if (!$this->selectedMembershipId) { $this->modalError = 'Select a plan.'; return; }

        $membership = Membership::find($this->selectedMembershipId);
        if (!$membership) { $this->modalError = 'Invalid membership.'; return; }

        DB::beginTransaction();
        try {
            $record = PatientMembership::create([
                'company_id' => auth()->user()->company_id,
                'patient_id' => $this->selectedPatient['id'] ?? null,
                'membership_id' => $membership->id,
                'amount_paid' => 0, // Will be marked paid when invoice is fully paid
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addDays($membership->validity_days)->toDateString(),
                'is_active' => true,
            ]);
            DB::commit();

            $this->active_membership = $membership->toArray();
            $this->membership_fee = (float) $membership->price;
            $this->purchasedMembershipRecordId = $record->id;
            $this->isMembershipModalOpen = false;
            $this->selectedMembershipId = null;
            $this->modalError = '';
            $this->calculateTotals();
            session()->flash('message', '🎉 ' . $membership->name . ' activated! ' . $membership->discount_percentage . '% discount applied.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Membership Purchase Error: " . $e->getMessage());
            $this->modalError = 'Error: ' . $e->getMessage();
        }
    }

    public function removeMembership()
    {
        $this->active_membership = null;
        $this->membership_fee = 0;

        // If membership was just purchased in this session, delete the record
        if ($this->purchasedMembershipRecordId) {
            PatientMembership::where('id', $this->purchasedMembershipRecordId)->delete();
            $this->purchasedMembershipRecordId = null;
        }

        $this->calculateTotals();
    }

    // ==========================================
    // QUICK ADD PAYMENT MODE
    // ==========================================
    public function quickAddPaymentMode()
    {
        $this->modalError = '';
        if (empty(trim($this->new_payment_mode_name))) { $this->modalError = 'Name is required.'; return; }
        try {
            PaymentMode::create(['company_id' => auth()->user()->company_id, 'name' => trim($this->new_payment_mode_name), 'is_active' => true]);
            $this->isPaymentModeModalOpen = false;
            $this->new_payment_mode_name = '';
            $this->modalError = '';
            session()->flash('message', 'Payment mode added!');
        } catch (\Exception $e) {
            Log::error("Payment Mode Error: " . $e->getMessage());
            $this->modalError = 'Error adding payment mode.';
        }
    }

    // ==========================================
    // VOUCHER
    // ==========================================
    public function applyVoucher()
    {
        $this->resetErrorBag('voucher_code');
        if (empty($this->voucher_code)) { $this->addError('voucher_code', 'Enter a voucher code.'); return; }

        $voucher = Voucher::where('company_id', auth()->user()->company_id)
            ->where('code', strtoupper($this->voucher_code))
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->first();

        if (!$voucher) { $this->addError('voucher_code', 'Invalid or expired voucher.'); return; }
        if ($this->subtotal < $voucher->min_bill_amount) { $this->addError('voucher_code', 'Min ₹' . $voucher->min_bill_amount); return; }

        $this->applied_voucher = $voucher;
        $this->calculateTotals();
    }

    public function removeVoucher()
    {
        $this->applied_voucher = null;
        $this->voucher_code = '';
        $this->calculateTotals();
    }

    // ==========================================
    // CALCULATOR ENGINE
    // ==========================================
    public function updatedManualDiscountInput() { $this->calculateTotals(); }
    public function updatedManualDiscountType() { $this->calculateTotals(); }
    public function updatedPayments() { $this->calculateTotals(); }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum('mrp');
        $running = $this->subtotal;

        $this->membership_discount_amt = 0;
        if ($this->active_membership && $running > 0) {
            $pct = is_array($this->active_membership) ? ($this->active_membership['discount_percentage'] ?? 0) : $this->active_membership->discount_percentage;
            $this->membership_discount_amt = ($running * $pct) / 100;
            $running -= $this->membership_discount_amt;
        }

        $this->voucher_discount_amt = 0;
        if ($this->applied_voucher && $running > 0) {
            if ($this->applied_voucher->discount_type === 'percentage') {
                $v = ($running * $this->applied_voucher->discount_value) / 100;
                if ($this->applied_voucher->max_discount_amount > 0) $v = min($v, $this->applied_voucher->max_discount_amount);
                $this->voucher_discount_amt = $v;
            } else {
                $this->voucher_discount_amt = $this->applied_voucher->discount_value;
            }
            $running -= $this->voucher_discount_amt;
        }

        $this->manual_discount_amt = 0;
        $manualVal = (float) $this->manual_discount_input;
        if ($manualVal > 0 && $running > 0) {
            $this->manual_discount_amt = $this->manual_discount_type === 'percent' ? ($running * $manualVal) / 100 : $manualVal;
            $running -= $this->manual_discount_amt;
        }

        $this->net_payable = max($running, 0) + $this->membership_fee;
        $this->total_discount = $this->membership_discount_amt + $this->voucher_discount_amt + $this->manual_discount_amt;
        $this->due_amount = max($this->net_payable - collect($this->payments)->sum('amount'), 0);
    }

    // ==========================================
    // SPLIT PAYMENTS
    // ==========================================
    public function addPaymentRow() { $this->payments[] = ['mode_id' => '', 'amount' => 0, 'transaction_id' => '']; }
    public function removePaymentRow($index) { unset($this->payments[$index]); $this->payments = array_values($this->payments); $this->calculateTotals(); }

    // ==========================================
    // QUICK ADD PATIENT
    // ==========================================
    public function quickAddPatient()
    {
        $this->modalError = '';
        $this->validate([
            'new_name' => 'required|string|max:255',
            'new_phone' => 'required|numeric|digits:10|unique:users,phone',
            'new_age' => 'required|numeric|min:1|max:150',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $user = User::create([
                'name' => $this->new_name,
                'phone' => $this->new_phone,
                'email' => 'patient_' . $this->new_phone . '@noemail.local',
                'password' => Hash::make($this->new_phone),
                'is_active' => true,
                'company_id' => $companyId,
            ]);
            PatientProfile::create([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'patient_id_string' => 'PAT-' . date('ym') . str_pad(PatientProfile::where('company_id', $companyId)->count() + 1, 4, '0', STR_PAD_LEFT),
                'age' => $this->new_age,
                'gender' => $this->new_gender,
            ]);
            DB::commit();
            $this->selectPatient($user->id);
            $this->isPatientModalOpen = false;
            $this->modalError = '';
            $this->reset(['new_name', 'new_phone', 'new_age']);
            $this->new_gender = 'Male';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Quick Add Patient: " . $e->getMessage());
            $this->modalError = 'Error: ' . $e->getMessage();
        }
    }

    // ==========================================
    // QUICK ADD DOCTOR
    // ==========================================
    public function quickAddDoctor()
    {
        $this->modalError = '';
        $this->validate([
            'new_doc_name' => 'required|string|max:255',
            'new_doc_phone' => 'nullable|numeric|digits:10|unique:users,phone',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $finalName = str_starts_with(strtolower($this->new_doc_name), 'dr') ? $this->new_doc_name : 'Dr. ' . $this->new_doc_name;
            $phone = $this->new_doc_phone ?: 'DOC' . time() . rand(10, 99);
            $user = User::create([
                'name' => $finalName,
                'phone' => $phone,
                'email' => 'doctor_' . $phone . '@noemail.local',
                'password' => Hash::make($phone),
                'is_active' => true,
                'company_id' => $companyId,
            ]);
            DoctorProfile::create([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'commission_percentage' => $this->new_doc_commission ?: 0,
            ]);
            DB::commit();
            $this->selectDoctor($user->id);
            $this->isDoctorModalOpen = false;
            $this->modalError = '';
            $this->reset(['new_doc_name', 'new_doc_phone', 'new_doc_commission']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Quick Add Doctor: " . $e->getMessage());
            $this->modalError = 'Error: ' . $e->getMessage();
        }
    }

    // ==========================================
    // GENERATE BILL
    // ==========================================
    public function generateBill()
    {
        if (!$this->selectedPatient) { session()->flash('error', 'Select a patient.'); return; }
        if (empty($this->cart)) { session()->flash('error', 'Add at least one test.'); return; }

        $this->validate([
            'collection_center_id' => 'required|exists:collection_centers,id',
            'branch_id'            => 'nullable|exists:branches,id',
            'collection_type'      => 'required|string',
        ], [
            'collection_center_id.required' => 'Please select a Collection Center.',
            'collection_center_id.exists'   => 'The selected Collection Center is invalid.',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;

            // ── Build Invoice Number from Configuration ──
            $prefix = Configuration::getFor('invoice_prefix', 'INV');
            $separator = Configuration::getFor('invoice_separator', '-');
            $dateFormat = Configuration::getFor('invoice_date_format', 'ym');
            $counterDigits = (int) Configuration::getFor('invoice_counter_digits', 4);
            $counterReset = Configuration::getFor('invoice_counter_reset', 'monthly');

            $dateMap = ['ym' => date('ym'), 'ymd' => date('ymd'), 'Ymd' => date('Ymd'), 'Y' => date('Y'), 'none' => ''];
            $datePart = $dateMap[$dateFormat] ?? date('ym');

            // Count existing invoices in current period for counter
            $counterQuery = Invoice::where('company_id', $companyId);
            switch ($counterReset) {
                case 'daily':   $counterQuery->whereDate('created_at', today()); break;
                case 'monthly': $counterQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year); break;
                case 'yearly':  $counterQuery->whereYear('created_at', now()->year); break;
                // 'never' — no filter, continuous count
            }
            $nextId = $counterQuery->count() + 1;

            $counter = str_pad($nextId, $counterDigits, '0', STR_PAD_LEFT);
            $parts = array_filter([$prefix, $datePart, $counter]);
            $invoiceNumber = implode($separator, $parts);
            $barcode = $prefix . date('ymd') . str_pad($nextId, $counterDigits, '0', STR_PAD_LEFT);

            // ── Commission Calculation ──
            $docCommission = 0;
            $agentCommission = 0;
            $doctorId = $this->selectedDoctor['id'] ?? null;
            $agentId = $this->selectedAgent['id'] ?? null;

            if ($doctorId) {
                $profile = DoctorProfile::where('user_id', $doctorId)->first();
                $docCommission = ($this->net_payable * ($profile->commission_percentage ?? 0)) / 100;
            }
            if ($agentId) {
                $profile = AgentProfile::where('user_id', $agentId)->first();
                $agentCommission = ($this->net_payable * ($profile->commission_percentage ?? 0)) / 100;
            }

            $invoice = Invoice::create([
                'company_id' => $companyId,
                'collection_center_id' => $this->collection_center_id,
                'branch_id' => $this->branch_id,
                'collection_type' => $this->collection_type,
                'patient_id' => $this->selectedPatient['id'] ?? null,
                'created_by' => auth()->id(),
                'referred_by_doctor_id' => $doctorId,
                'referred_by_agent_id' => $agentId,
                'invoice_number' => $invoiceNumber,
                'barcode' => $barcode,
                'invoice_date' => now(),
                'expected_report_time' => $this->expected_report_date && $this->expected_report_time
                    ? $this->expected_report_date . ' ' . $this->expected_report_time
                    : null,
                'subtotal' => $this->subtotal,
                'membership_discount_amount' => $this->membership_discount_amt,
                'voucher_id' => $this->applied_voucher->id ?? null,
                'voucher_discount_amount' => $this->voucher_discount_amt,
                'discount_amount' => $this->manual_discount_amt,
                'total_amount' => $this->net_payable,
                'doctor_commission_amount' => $docCommission,
                'agent_commission_amount' => $agentCommission,
                'paid_amount' => $this->net_payable - $this->due_amount,
                'due_amount' => $this->due_amount,
                'payment_status' => $this->due_amount <= 0 ? 'Paid' : ($this->due_amount == $this->net_payable ? 'Unpaid' : 'Partial'),
                'status' => 'Pending',
            ]);

            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'lab_test_id' => $item['id'],
                    'test_name' => $item['name'],
                    'is_package' => $item['is_package'],
                    'mrp' => $item['mrp'],
                    'price' => $item['mrp'],
                ]);
            }

            foreach ($this->payments as $payment) {
                if (!empty($payment['mode_id']) && $payment['amount'] > 0) {
                    Payment::create([
                        'company_id' => $companyId,
                        'invoice_id' => $invoice->id,
                        'patient_id' => $this->selectedPatient['id'] ?? null,
                        'collected_by' => auth()->id(),
                        'payment_mode_id' => $payment['mode_id'],
                        'amount' => $payment['amount'],
                        'transaction_id' => $payment['transaction_id'] ?? null,
                    ]);
                }
            }

            if ($this->applied_voucher) $this->applied_voucher->increment('used_count');

            // ── Mark membership as paid if bill is fully paid ──
            if ($this->purchasedMembershipRecordId && $this->due_amount <= 0) {
                $memRecord = PatientMembership::find($this->purchasedMembershipRecordId);
                if ($memRecord) {
                    $memRecord->update(['amount_paid' => $this->membership_fee]);
                }
            }

            // ── Credit Doctor & Agent Wallets ──
            if ($docCommission > 0 && $doctorId) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $doctorId, 'company_id' => $companyId],
                    ['balance' => 0]
                );
                $wallet->credit($docCommission, 'Commission from Invoice #' . $invoiceNumber, 'invoice', $invoice->id);
            }
            if ($agentCommission > 0 && $agentId) {
                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $agentId, 'company_id' => $companyId],
                    ['balance' => 0]
                );
                $wallet->credit($agentCommission, 'Commission from Invoice #' . $invoiceNumber, 'invoice', $invoice->id);
            }

            DB::commit();
            session()->flash('message', '✅ Bill Generated! Invoice: ' . $invoiceNumber);

            $this->cart = []; $this->selectedPatient = null; $this->patientProfileData = null;
            $this->selectedDoctor = null; $this->doctorProfileData = null;
            $this->selectedAgent = null; $this->agentProfileData = null;
            $this->applied_voucher = null; $this->active_membership = null; $this->membership_fee = 0; $this->purchasedMembershipRecordId = null;
            $this->manual_discount_input = 0; $this->expandedCartItems = [];
            $this->payments = []; $this->addPaymentRow(); $this->calculateTotals();
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Invoice Generation Error: " . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'patient_id' => $this->selectedPatient['id'] ?? null,
                'cart_count' => count($this->cart)
            ]);
            session()->flash('error', 'Failed to generate bill: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $patients = [];
        if (strlen($this->patientSearch) >= 2) {
            $s = $this->patientSearch;
            $patients = User::whereHas('patientProfile', fn($q) => $q->where('company_id', $companyId))
                ->where(fn($q) => $q->where('phone', 'ilike', "%{$s}%")->orWhere('name', 'ilike', "%{$s}%"))
                ->with('patientProfile')
                ->take(5)->get();
        }

        $doctors = [];
        if (strlen($this->doctorSearch) >= 2) {
            $s = $this->doctorSearch;
            $doctors = User::whereHas('doctorProfile', fn($q) => $q->where('company_id', $companyId))
                ->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('phone', 'ilike', "%{$s}%"))
                ->with('doctorProfile')
                ->take(5)->get();
        }

        $agents = [];
        if (strlen($this->agentSearch) >= 2) {
            $s = $this->agentSearch;
            $agents = User::whereHas('agentProfile', fn($q) => $q->where('company_id', $companyId))
                ->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('phone', 'ilike', "%{$s}%"))
                ->with('agentProfile')
                ->take(5)->get();
        }

        $tests = [];
        if (strlen($this->testSearch) >= 2) {
            $s = $this->testSearch;
            $tests = LabTest::where('company_id', $companyId)->where('is_active', true)
                ->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('test_code', 'ilike', "%{$s}%"))
                ->take(8)->get();
        }

        $paymentModes = PaymentMode::where('company_id', $companyId)->where('is_active', true)->get();
        $centers = CollectionCenter::where('company_id', $companyId)->where('is_active', true)->get();
        $branches = Branch::where('company_id', $companyId)->where('is_active', true)->get();
        $memberships = Membership::where('company_id', $companyId)->where('is_active', true)->get();

        return view('livewire.lab.pos-manager', compact(
            'patients', 'doctors', 'agents', 'tests',
            'paymentModes', 'centers', 'branches', 'memberships'
        ))->layout('layouts.app', ['title' => 'Billing POS']);
    }
}
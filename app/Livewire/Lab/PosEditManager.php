<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\{User, LabTest, PaymentMode, Invoice, InvoiceItem, Payment, CollectionCenter, Branch, PatientProfile, DoctorProfile, AgentProfile, Membership, PatientMembership, Voucher, Configuration, Wallet, WalletTransaction};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PosEditManager extends Component
{
    // Invoice being edited
    public $invoiceId;
    public $invoice;

    // Selections
    public $patientSearch = '', $selectedPatient = null, $patientProfileData = null;
    public $doctorSearch = '', $selectedDoctor = null, $doctorProfileData = null;
    public $agentSearch = '', $selectedAgent = null, $agentProfileData = null;

    // Logistics
    public $collection_center_id, $branch_id, $collection_type = 'Center';
    public $expected_report_date, $expected_report_time;

    // Cart & Pricing
    public $testSearch = '';
    public $cart = [];
    public $subtotal = 0;

    public $active_membership = null;
    public $membership_discount_amt = 0;

    public $voucher_code = '';
    public $applied_voucher = null;
    public $voucher_discount_amt = 0;

    public $manual_discount_type = 'flat';
    public $manual_discount_input = 0;
    public $manual_discount_amt = 0;

    public $total_discount = 0;
    public $net_payable = 0;
    public $due_amount = 0;

    // Payments
    public $payments = [];

    // ==========================================
    // MODALS & QUICK ADD
    // ==========================================
    public $isPatientModalOpen = false, $isDoctorModalOpen = false, $isAgentModalOpen = false;
    public $activeSearchField = null; // null, 'patient', 'doctor', 'agent', 'test'
    public $overpaymentError = false;
    public $paymentModesList = [], $cachedCenters = [], $cachedBranches = [], $cachedMemberships = [];
    public $new_name, $new_phone, $new_age, $new_gender = 'Male';
    public $new_doc_name, $new_doc_phone, $new_doc_commission = 0;
    public $new_agent_name, $new_agent_phone, $new_agent_agency, $new_agent_commission = 0;
    public $isMembershipModalOpen = false, $selectedMembershipId = null;
    public $isPaymentModeModalOpen = false, $new_payment_mode_name = '';
    public $modalError = '';
    public $membership_fee = 0;

    // Logistics EXTRA
    public $sample_received_at;

    // Cart expansion
    public $expandedCartItems = [];

    public function mount($id)
    {
        $this->authorize('edit pos');
        $companyId = auth()->user()->company_id;
        $this->invoiceId = $id;

        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile'])
            ->findOrFail($id);

        $this->invoice = $invoice;

        // Load patient
        $patient = $invoice->patient;
        if ($patient) {
            $this->selectedPatient = $patient->toArray();
            $this->patientProfileData = $patient->patientProfile ? $patient->patientProfile->toArray() : null;
        }

        // Check active membership
        // First strictly check if THIS invoice itself applied a membership
        if ($invoice->membership_id) {
            $membership = Membership::find($invoice->membership_id);
            if ($membership) {
                $this->active_membership = $membership->toArray();
                $this->selectedMembershipId = $membership->id;
            }
        } elseif ($patient) {
            // Otherwise check if patient has a pre-existing active membership
            $record = PatientMembership::where('patient_id', $patient->id)
                ->where('is_active', true)
                ->where('valid_until', '>=', now())
                ->latest()->first();
            if ($record) {
                $membership = Membership::find($record->membership_id);
                // For pre-existing memberships, it must be fully paid
                if ($membership && $record->amount_paid >= $membership->price) {
                    $this->active_membership = $membership->toArray();
                }
            }
        }

        // Load doctor
        if ($invoice->referred_by_doctor_id) {
            $doc = User::with('doctorProfile')->find($invoice->referred_by_doctor_id);
            if ($doc) {
                $this->selectedDoctor = $doc->toArray();
                $this->doctorProfileData = $doc->doctorProfile ? $doc->doctorProfile->toArray() : null;
            }
        }

        // Load agent
        if ($invoice->referred_by_agent_id) {
            $agent = User::with('agentProfile')->find($invoice->referred_by_agent_id);
            if ($agent) {
                $this->selectedAgent = $agent->toArray();
                $this->agentProfileData = $agent->agentProfile ? $agent->agentProfile->toArray() : null;
            }
        }

        // Logistics
        $this->collection_center_id = $invoice->collection_center_id;
        $this->branch_id = $invoice->branch_id;
        $this->collection_type = $invoice->collection_type;
        $this->sample_received_at = $this->invoice->sample_received_at ? $this->invoice->sample_received_at->format('Y-m-d\TH:i') : null;
        $this->expected_report_date = $this->invoice->expected_report_time ? $this->invoice->expected_report_time->format('Y-m-d') : date('Y-m-d');
        $this->expected_report_time = $this->invoice->expected_report_time ? $this->invoice->expected_report_time->format('H:i') : date('H:i', strtotime('+24 hours'));

        $this->membership_fee = 0; // Initialize

        // Load cart from invoice items
        foreach ($invoice->items as $item) {
            // Special case: If this is the Membership Fee line item, extract it to $this->membership_fee and do NOT add to cart!
            if (is_null($item->lab_test_id) && str_starts_with($item->test_name, 'Membership:')) {
                $this->membership_fee = (float) $item->price;
                continue; 
            }

            $test = LabTest::find($item->lab_test_id);
            $cartItem = [
                'id' => $item->lab_test_id,
                'name' => $item->test_name,
                'test_code' => $test->test_code ?? '',
                'mrp' => (float) $item->mrp,
                'price' => (float) ($item->price > 0 ? $item->price : $item->mrp),
                'is_package' => (bool) $item->is_package,
                'department' => $test->department ?? '',
                'sample_type' => $test->sample_type ?? '',
                'parameters' => $test->parameters ?? [],
                'linked_tests' => [],
            ];

            if ($item->is_package && $test && !empty($test->linked_test_ids)) {
                $linkedTests = LabTest::whereIn('id', $test->linked_test_ids)->get();
                $cartItem['linked_tests'] = $linkedTests->map(fn($lt) => [
                    'id' => $lt->id,
                    'name' => $lt->name,
                    'test_code' => $lt->test_code,
                    'mrp' => (float) $lt->mrp,
                    'department' => $lt->department,
                    'parameters' => $lt->parameters ?? [],
                ])->toArray();
            }

            $this->cart[] = $cartItem;
        }

        // Manual discount
        $this->manual_discount_amt = (float) $invoice->discount_amount;
        $this->manual_discount_input = $this->manual_discount_amt;
        $this->manual_discount_type = 'flat';

        // Load payments
        foreach ($invoice->payments as $pmt) {
            $this->payments[] = [
                'id' => $pmt->id,
                'mode_id' => $pmt->payment_mode_id,
                'amount' => (float) $pmt->amount,
                'transaction_id' => $pmt->transaction_id ?? '',
            ];
        }
        if (empty($this->payments)) {
            $this->addPaymentRow();
        }

        // Voucher
        if ($invoice->voucher_id) {
            $this->applied_voucher = Voucher::find($invoice->voucher_id);
            if ($this->applied_voucher) {
                $this->voucher_code = $this->applied_voucher->code;
            }
        }

        // Cache static data
        $this->paymentModesList = PaymentMode::where('company_id', $companyId)->where('is_active', true)->get();
        $this->cachedMemberships = Membership::where('company_id', $companyId)->where('is_active', true)->get();

        $restrictAccess = Configuration::getFor('restrict_branch_access', '1') === '1';
        if (auth()->user()->hasRole('branch_admin') && $restrictAccess) {
            $this->cachedCenters = CollectionCenter::where('company_id', $companyId)->where('branch_id', auth()->user()->branch_id)->where('is_active', true)->get();
            $this->cachedBranches = Branch::where('id', auth()->user()->branch_id)->get();
            $this->branch_id = auth()->user()->branch_id;
        } else {
            $this->cachedCenters = CollectionCenter::where('company_id', $companyId)->where('is_active', true)->get();
            $this->cachedBranches = Branch::where('company_id', $companyId)->where('is_active', true)->get();
        }

        $this->calculateTotals();
    }

    // ==========================================
    // SEARCH HELPERS (doctor, agent, tests)
    // ==========================================
    public function updatedPatientSearch()
    {
        $this->activeSearchField = 'patient';
    }
    public function updatedDoctorSearch()
    {
        $this->activeSearchField = 'doctor';
    }
    public function updatedAgentSearch()
    {
        $this->activeSearchField = 'agent';
    }
    public function updatedTestSearch()
    {
        $this->activeSearchField = 'test';
    }

    public function selectPatient($userId)
    {
        $user = User::with(['patientProfile'])->find($userId);
        $this->selectedPatient = $user->toArray();
        $this->patientProfileData = $user->patientProfile ? $user->patientProfile->toArray() : null;
        $this->patientSearch = '';
        $this->activeSearchField = null;

        // Check active membership
        $record = PatientMembership::where('patient_id', $userId)
            ->where('is_active', true)
            ->where('valid_until', '>=', now())
            ->latest()->first();
        if ($record) {
            $membership = Membership::find($record->membership_id);
            if ($membership && $record->amount_paid >= $membership->price) {
                $this->active_membership = $membership->toArray();
            }
        }
        $this->calculateTotals();
    }

    public function selectDoctor($userId)
    {
        $user = User::with(['doctorProfile'])->find($userId);
        $this->selectedDoctor = $user->toArray();
        $this->doctorProfileData = $user->doctorProfile ? $user->doctorProfile->toArray() : null;
        $this->doctorSearch = '';
        $this->activeSearchField = null;
    }

    public function selectAgent($userId)
    {
        $user = User::with(['agentProfile'])->find($userId);
        $this->selectedAgent = $user->toArray();
        $this->agentProfileData = $user->agentProfile ? $user->agentProfile->toArray() : null;
        $this->agentSearch = '';
        $this->activeSearchField = null;
    }

    public function clearPatient()
    {
        $this->selectedPatient = null;
        $this->patientProfileData = null;
        $this->active_membership = null;
        $this->calculateTotals();
    }
    public function clearDoctor()
    {
        $this->selectedDoctor = null;
        $this->doctorProfileData = null;
    }
    public function clearAgent()
    {
        $this->selectedAgent = null;
        $this->agentProfileData = null;
    }

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
                'mrp' => (float) $test->mrp,
                'price' => (float) $test->mrp,
                'is_package' => (bool) $test->is_package,
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
                    'mrp' => (float) $lt->mrp,
                    'department' => $lt->department,
                    'parameters' => $lt->parameters ?? [],
                ])->toArray();
            }

            $this->cart[] = $cartItem;
            $this->calculateTotals();
        }
        $this->testSearch = '';
        $this->activeSearchField = null;
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
    // VOUCHER & MEMBERSHIP
    // ==========================================
    public function applyVoucher()
    {
        $this->resetErrorBag('voucher_code');
        if (empty($this->voucher_code)) {
            $this->addError('voucher_code', 'Enter a voucher code.');
            return;
        }

        $voucher = Voucher::where('company_id', auth()->user()->company_id)
            ->where('code', strtoupper($this->voucher_code))
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', now()))
            ->first();

        if (!$voucher) {
            $this->addError('voucher_code', 'Invalid or expired voucher.');
            return;
        }
        if ($this->subtotal < $voucher->min_bill_amount) {
            $this->addError('voucher_code', 'Min ₹' . $voucher->min_bill_amount);
            return;
        }

        $this->applied_voucher = $voucher;
        $this->calculateTotals();
    }

    public function removeVoucher()
    {
        $this->applied_voucher = null;
        $this->voucher_code = '';
        $this->calculateTotals();
    }

    public function purchaseMembership()
    {
        $this->authorize('create marketing');
        if (!$this->selectedMembershipId)
            return;
        $membership = Membership::find($this->selectedMembershipId);
        if ($membership) {
            $this->active_membership = $membership->toArray();
            $this->membership_fee = $membership->price;
            $this->isMembershipModalOpen = false;
            $this->calculateTotals();
        }
    }

    public function removeMembership()
    {
        $this->active_membership = null;
        $this->membership_fee = 0;
        $this->calculateTotals();
    }

    // ==========================================
    // CALCULATOR ENGINE
    // ==========================================
    public function updatedManualDiscountInput()
    {
        $this->calculateTotals();
    }
    public function updatedManualDiscountType()
    {
        $this->calculateTotals();
    }
    public function updatedPayments()
    {
        $this->calculateTotals();
    }
    public function updatedCart()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        // 1. Recalculate Subtotal from MRP
        $this->subtotal = collect($this->cart)->sum(fn($item) => (float) ($item['mrp'] ?? 0));
        
        // 2. Initial Running Total is the sum of current item prices
        $itemTotal = collect($this->cart)->sum(fn($item) => (float) ($item['price'] ?? 0));
        
        // 3. Implicit Item Discount (e.g. if price was manually reduced per line)
        $itemDiscount = $this->subtotal - $itemTotal;
        
        $running = $itemTotal;

        // 4. Membership Discount
        $this->membership_discount_amt = 0;
        if ($this->active_membership && $running > 0) {
            $pct = data_get($this->active_membership, 'discount_percentage', 0);
            $this->membership_discount_amt = ($running * $pct) / 100;
            $running -= $this->membership_discount_amt;
        }

        // 5. Voucher Discount
        $this->voucher_discount_amt = 0;
        if ($this->applied_voucher && $running > 0) {
            if ($this->applied_voucher->discount_type === 'percentage') {
                $v = ($running * $this->applied_voucher->discount_value) / 100;
                if ($this->applied_voucher->max_discount_amount > 0) {
                    $v = min($v, $this->applied_voucher->max_discount_amount);
                }
                $this->voucher_discount_amt = $v;
            } else {
                $this->voucher_discount_amt = $this->applied_voucher->discount_value;
            }
            $running -= $this->voucher_discount_amt;
        }

        // 6. Manual Overall Discount
        $this->manual_discount_amt = 0;
        $manualVal = (float) $this->manual_discount_input;
        if ($manualVal > 0 && $running > 0) {
            $calcManual = $this->manual_discount_type === 'percent' ? ($running * $manualVal) / 100 : $manualVal;
            $this->manual_discount_amt = min($calcManual, $running);
            $running -= $this->manual_discount_amt;
        }

        // 7. Net Payable
        $this->net_payable = max($running, 0) + $this->membership_fee;
        
        // 8. Total Savings shown in UI (Implicit + Explicit)
        $this->total_discount = $itemDiscount + $this->membership_discount_amt + $this->voucher_discount_amt + $this->manual_discount_amt;
        
        // 9. Due calculation
        $totalCollected = collect($this->payments)->sum(fn($p) => (float) ($p['amount'] ?? 0));
        $this->due_amount = max($this->net_payable - $totalCollected, 0);
        $this->overpaymentError = $totalCollected > $this->net_payable;
    }

    // ==========================================
    // SPLIT PAYMENTS
    // ==========================================
    public function addPaymentRow()
    {
        $this->payments[] = ['id' => null, 'mode_id' => '', 'amount' => 0, 'transaction_id' => ''];
    }
    public function removePaymentRow($index)
    {
        unset($this->payments[$index]);
        $this->payments = array_values($this->payments);
        $this->calculateTotals();
    }

    // ==========================================
    // QUICK ADD MODALS
    // ==========================================
    public function quickAddPatient()
    {
        $this->authorize('create patients');
        $this->modalError = '';
        $this->validate(['new_name' => 'required|string|max:255', 'new_phone' => 'nullable|numeric|digits:10|unique:users,phone', 'new_age' => 'required|numeric|min:0']);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $user = User::create([
                'name' => $this->new_name,
                'phone' => $this->new_phone ?: 'P' . time(),
                'email' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'is_active' => true,
                'company_id' => $companyId,
                'branch_id' => $this->branch_id,
            ]);
            PatientProfile::create([
                'company_id' => $companyId,
                'user_id' => $user->id,
                'patient_id_string' => 'PAT-' . date('ym') . str_pad(PatientProfile::where('company_id', $companyId)->count() + 1, 4, '0', STR_PAD_LEFT),
                'age' => $this->new_age,
                'gender' => $this->new_gender,
            ]);
            $user->assignRole('patient');
            DB::commit();
            $this->selectPatient($user->id);
            $this->isPatientModalOpen = false;
            $this->reset(['new_name', 'new_phone', 'new_age']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->modalError = $e->getMessage();
        }
    }

    public function quickAddDoctor()
    {
        $this->authorize('create doctors');
        $this->modalError = '';
        $this->validate(['new_doc_name' => 'required|string|max:255']);
        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $finalName = str_starts_with(strtolower($this->new_doc_name), 'dr') ? $this->new_doc_name : 'Dr. ' . $this->new_doc_name;
            $user = User::create([
                'name' => $finalName,
                'phone' => $this->new_doc_phone ?: 'D' . time(),
                'email' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'is_active' => true,
                'company_id' => $companyId,
                'branch_id' => $this->branch_id,
            ]);
            DoctorProfile::create(['company_id' => $companyId, 'user_id' => $user->id, 'commission_percentage' => $this->new_doc_commission ?: 0]);
            $user->assignRole('doctor');
            DB::commit();
            $this->selectDoctor($user->id);
            $this->isDoctorModalOpen = false;
            $this->reset(['new_doc_name', 'new_doc_phone', 'new_doc_commission']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->modalError = $e->getMessage();
        }
    }

    public function quickAddAgent()
    {
        $this->authorize('create agents');
        $this->modalError = '';
        $this->validate(['new_agent_name' => 'required|string|max:255']);
        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $user = User::create([
                'name' => $this->new_agent_name,
                'phone' => $this->new_agent_phone ?: 'A' . time(),
                'email' => null,
                'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                'is_active' => true,
                'company_id' => $companyId,
                'branch_id' => $this->branch_id,
            ]);
            AgentProfile::create(['company_id' => $companyId, 'user_id' => $user->id, 'agency_name' => $this->new_agent_agency, 'commission_percentage' => $this->new_agent_commission ?: 0]);
            $user->assignRole('agent');
            DB::commit();
            $this->selectAgent($user->id);
            $this->isAgentModalOpen = false;
            $this->reset(['new_agent_name', 'new_agent_phone', 'new_agent_agency', 'new_agent_commission']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->modalError = $e->getMessage();
        }
    }

    public function quickAddPaymentMode()
    {
        $this->authorize('edit settings');
        if (empty($this->new_payment_mode_name))
            return;
        PaymentMode::create(['company_id' => auth()->user()->company_id, 'name' => $this->new_payment_mode_name, 'is_active' => true]);
        $this->paymentModesList = PaymentMode::where('company_id', auth()->user()->company_id)->where('is_active', true)->get();
        $this->new_payment_mode_name = '';
        $this->isPaymentModeModalOpen = false;
    }

    // ==========================================
    // UPDATE BILL
    // ==========================================
    public function updateBill()
    {
        $this->authorize('edit pos');
        if (!$this->selectedPatient) {
            session()->flash('error', 'Select a patient.');
            return;
        }
        if (empty($this->cart)) {
            session()->flash('error', 'Add at least one test.');
            return;
        }

        if ($this->overpaymentError) {
            session()->flash('error', 'Total payment cannot exceed Net Payable amount.');
            return;
        }

        $this->validate([
            'collection_center_id' => 'required|exists:collection_centers,id',
            'branch_id' => 'nullable|exists:branches,id',
            'collection_type' => 'required|string',
        ], [
            'collection_center_id.required' => 'Please select a Collection Center.',
            'collection_center_id.exists' => 'The selected Collection Center is invalid.',
        ]);

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $invoice = Invoice::where('company_id', $companyId)->findOrFail($this->invoiceId);

            // 1. Reverse old commissions using service
            $commissionService = new \App\Services\CommissionService();
            $commissionService->reverseCommissions($invoice, "Invoice Updated");

            // 2. Recalculate Commissions
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

            // 3. B2B & Profit Calculation
            $totalB2bAmount = 0;
            $cartIds = collect($this->cart)->pluck('id');
            $testPrices = LabTest::whereIn('id', $cartIds)->get()->keyBy('id');

            foreach ($this->cart as $item) {
                $b2b = (float) data_get($testPrices->get($item['id']), 'b2b_price', 0);
                $totalB2bAmount += $b2b;
            }

            $ccProfitAmount = 0;
            if ($this->collection_center_id) {
                $ccProfitAmount = max(0, $this->net_payable - $totalB2bAmount);

                // Enforcement of B2B Price Floor
                $restrictBilling = Configuration::getFor('restrict_billing_below_b2b', '0') === '1';
                if ($restrictBilling && $this->net_payable < $totalB2bAmount) {
                    session()->flash('error', 'Action Blocked: Net Payable (₹' . number_format($this->net_payable, 2) . ') cannot be less than the total B2B cost (₹' . number_format($totalB2bAmount, 2) . '). Please reduce discount or adjust tests.');
                    return;
                }
            }

            // Update invoice
            $invoice->update([
                'patient_id' => $this->selectedPatient['id'],
                'collection_center_id' => $this->collection_center_id,
                'branch_id' => $this->branch_id,
                'collection_type' => $this->collection_type,
                'sample_received_at' => $this->sample_received_at,
                'referred_by_doctor_id' => $doctorId,
                'referred_by_agent_id' => $agentId,
                'expected_report_time' => $this->expected_report_date && $this->expected_report_time
                    ? $this->expected_report_date . ' ' . $this->expected_report_time
                    : null,
                'subtotal' => $this->subtotal,
                'membership_discount_amount' => $this->membership_discount_amt,
                'voucher_id' => $this->applied_voucher->id ?? null,
                'voucher_discount_amount' => $this->voucher_discount_amt,
                'discount_amount' => $this->manual_discount_amt,
                'total_amount' => $this->net_payable,
                'total_b2b_amount' => $totalB2bAmount,
                'cc_profit_amount' => $ccProfitAmount,
                'doctor_commission_amount' => $docCommission,
                'agent_commission_amount' => $agentCommission,
                'paid_amount' => $this->net_payable - $this->due_amount,
                'due_amount' => $this->due_amount,
                'payment_status' => $this->due_amount <= 0 ? 'Paid' : ($this->due_amount == $this->net_payable ? 'Unpaid' : 'Partial'),
            ]);

            // Replace invoice items
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'lab_test_id' => $item['id'],
                    'test_name' => $item['name'],
                    'is_package' => $item['is_package'],
                    'mrp' => $item['mrp'],
                    'price' => $item['price'],
                    'b2b_price' => data_get($testPrices->get($item['id']), 'b2b_price', 0),
                ]);
            }

            // Replace payments
            Payment::where('invoice_id', $invoice->id)->delete();
            foreach ($this->payments as $payment) {
                if (!empty($payment['mode_id']) && $payment['amount'] > 0) {
                    Payment::create([
                        'company_id' => $companyId,
                        'invoice_id' => $invoice->id,
                        'patient_id' => $this->selectedPatient['id'],
                        'collected_by' => auth()->id(),
                        'payment_mode_id' => $payment['mode_id'],
                        'amount' => $payment['amount'],
                        'transaction_id' => $payment['transaction_id'] ?? null,
                    ]);
                }
            }

            // Handle Membership purchase if applicable
            if ($this->membership_fee > 0 && $this->active_membership) {
                $newMembership = PatientMembership::create([
                    'company_id' => $companyId,
                    'patient_id' => $this->selectedPatient['id'],
                    'membership_id' => $this->active_membership['id'],
                    'amount_paid' => $this->membership_fee,
                    'valid_from' => now()->toDateString(),
                    'valid_until' => now()->addDays($this->active_membership['validity_days'] ?? 365)->toDateString(),
                    'is_active' => true,
                ]);
                
                // Also update the invoice to link to this new membership
                $invoice->update(['patient_membership_id' => $newMembership->id]);
                
                // And insert as line item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'lab_test_id' => null,
                    'test_name' => 'Membership: ' . ($this->active_membership['name'] ?? 'Plan'),
                    'is_package' => false,
                    'mrp' => $this->membership_fee,
                    'price' => $this->membership_fee,
                    'b2b_price' => 0,
                ]);
            }

            // 4. Apply new Commissions using service
            $commissionService->applyCommissions($invoice);

            DB::commit();
            session()->flash('message', '✅ Invoice updated successfully!');
            return redirect()->route('lab.pos.summary', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Bill Error: ' . $e->getMessage());
            $this->addError('general', 'Failed to update invoice: ' . $e->getMessage());
        }
    }

    public function cancelInvoice()
    {
        try {
            $this->authorize('delete pos');
            $invoice = Invoice::findOrFail($this->invoiceId);
            $result = $invoice->cancel();

            if ($result['status']) {
                session()->flash('message', '🔴 ' . $result['message']);
                return redirect()->route('lab.pos.summary', $invoice->id);
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Throwable $th) {
            session()->flash('error', 'Critical Error: ' . $th->getMessage());
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $activeBranchId = session('active_branch_id', 'all');
        $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
        $myBranchId = (auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') || !$restrictAccess) 
            ? ($activeBranchId === 'all' ? null : $activeBranchId) 
            : auth()->user()->branch_id;

        $sharePatients = \App\Models\Configuration::getFor('branch_share_patients', '1') === '1';
        $shareDoctors = \App\Models\Configuration::getFor('branch_share_doctors', '1') === '1';
        $shareAgents = \App\Models\Configuration::getFor('branch_share_agents', '1') === '1';
        
        $patients = $doctors = $agents = $tests = [];

        // Patient suggestions
        if ($this->activeSearchField === 'patient') {
            $s = $this->patientSearch;
            $query = clone \App\Models\User::whereHas('patientProfile', fn($q) => $q->where('company_id', $companyId))
                ->when($myBranchId && !$sharePatients, fn($q) => $q->where('branch_id', $myBranchId));
            if (!empty($s)) {
                $query->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('phone', 'ilike', "%{$s}%"));
            }
            $patients = $query->with('patientProfile')->orderBy('id', 'desc')->take(15)->get();
        }

        // Doctor suggestions
        if ($this->activeSearchField === 'doctor') {
            $s = $this->doctorSearch;
            $query = clone \App\Models\User::whereHas('doctorProfile', fn($q) => $q->where('company_id', $companyId))
                ->when($myBranchId && !$shareDoctors, fn($q) => $q->where('branch_id', $myBranchId));
            if (!empty($s)) {
                $query->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('phone', 'ilike', "%{$s}%"));
            }
            $doctors = $query->with('doctorProfile')->orderBy('id', 'desc')->take(15)->get();
        }

        // Agent suggestions
        if ($this->activeSearchField === 'agent') {
            $s = $this->agentSearch;
            $query = clone \App\Models\User::whereHas('agentProfile', fn($q) => $q->where('company_id', $companyId))
                ->when($myBranchId && !$shareAgents, fn($q) => $q->where('branch_id', $myBranchId));
            if (!empty($s)) {
                $query->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('phone', 'ilike', "%{$s}%"));
            }
            $agents = $query->with('agentProfile')->orderBy('id', 'desc')->take(15)->get();
        }

        // Test suggestions
        if ($this->activeSearchField === 'test') {
            $s = $this->testSearch;
            $query = LabTest::where('company_id', $companyId)->where('is_active', true);
            if (!empty($s)) {
                $query->where(fn($q) => $q->where('name', 'ilike', "%{$s}%")->orWhere('test_code', 'ilike', "%{$s}%"));
            }
            $tests = $query->orderBy('id', 'desc')->take(15)->get();
        }

        return view('livewire.lab.pos-edit-manager', [
            'doctors' => $doctors,
            'agents' => $agents,
            'tests' => $tests,
            'patients' => $patients,
            'paymentModes' => $this->paymentModesList,
            'centers' => $this->cachedCenters,
            'branches' => $this->cachedBranches,
            'memberships' => $this->cachedMemberships
        ])->layout('layouts.app', ['title' => 'Edit Invoice #' . ($this->invoice->invoice_number ?? '')]);
    }
}

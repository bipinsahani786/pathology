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
    public $selectedPatient = null, $patientProfileData = null;
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

    // Cart expansion
    public $expandedCartItems = [];

    // Status
    public $invoiceStatus;

    public function mount($id)
    {
        $companyId = auth()->user()->company_id;
        $this->invoiceId = $id;

        $invoice = Invoice::where('company_id', $companyId)
            ->with(['items', 'payments.paymentMode', 'patient.patientProfile', 'doctor.doctorProfile'])
            ->findOrFail($id);

        $this->invoice = $invoice;
        $this->invoiceStatus = $invoice->status;

        // Load patient
        $patient = $invoice->patient;
        if ($patient) {
            $this->selectedPatient = $patient->toArray();
            $this->patientProfileData = $patient->patientProfile ? $patient->patientProfile->toArray() : null;

            // Check active membership
            $record = PatientMembership::where('patient_id', $patient->id)
                ->where('is_active', true)
                ->where('valid_until', '>=', now())
                ->latest()->first();
            if ($record) {
                $membership = Membership::find($record->membership_id);
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
        $this->expected_report_date = $invoice->expected_report_time ? date('Y-m-d', strtotime($invoice->expected_report_time)) : date('Y-m-d');
        $this->expected_report_time = $invoice->expected_report_time ? date('H:i', strtotime($invoice->expected_report_time)) : date('H:i', strtotime('+24 hours'));

        // Load cart from invoice items
        foreach ($invoice->items as $item) {
            $test = LabTest::find($item->lab_test_id);
            $cartItem = [
                'id' => $item->lab_test_id,
                'name' => $item->test_name,
                'test_code' => $test->test_code ?? '',
                'mrp' => (float) $item->mrp,
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

        $this->calculateTotals();
    }

    // ==========================================
    // SEARCH HELPERS (doctor, agent, tests)
    // ==========================================
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
                'mrp' => (float) $test->mrp,
                'is_package' => (bool) $test->is_package,
                'department' => $test->department,
                'sample_type' => $test->sample_type,
                'parameters' => $test->parameters ?? [],
                'linked_tests' => [],
            ];

            if ($test->is_package && !empty($test->linked_test_ids)) {
                $linkedTests = LabTest::whereIn('id', $test->linked_test_ids)->get();
                $cartItem['linked_tests'] = $linkedTests->map(fn($lt) => [
                    'id' => $lt->id, 'name' => $lt->name, 'test_code' => $lt->test_code,
                    'mrp' => (float) $lt->mrp, 'department' => $lt->department, 'parameters' => $lt->parameters ?? [],
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

        $this->net_payable = max($running, 0);
        $this->total_discount = $this->membership_discount_amt + $this->voucher_discount_amt + $this->manual_discount_amt;
        $this->due_amount = max($this->net_payable - collect($this->payments)->sum('amount'), 0);
    }

    // ==========================================
    // SPLIT PAYMENTS
    // ==========================================
    public function addPaymentRow() { $this->payments[] = ['id' => null, 'mode_id' => '', 'amount' => 0, 'transaction_id' => '']; }
    public function removePaymentRow($index) { unset($this->payments[$index]); $this->payments = array_values($this->payments); $this->calculateTotals(); }

    // ==========================================
    // UPDATE BILL
    // ==========================================
    public function updateBill()
    {
        if (empty($this->cart)) { session()->flash('error', 'Add at least one test.'); return; }

        DB::beginTransaction();
        try {
            $companyId = auth()->user()->company_id;
            $invoice = Invoice::where('company_id', $companyId)->findOrFail($this->invoiceId);

            // Commission
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

            // Update invoice
            $invoice->update([
                'collection_center_id' => $this->collection_center_id,
                'branch_id' => $this->branch_id,
                'collection_type' => $this->collection_type,
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
                'doctor_commission_amount' => $docCommission,
                'agent_commission_amount' => $agentCommission,
                'paid_amount' => $this->net_payable - $this->due_amount,
                'due_amount' => $this->due_amount,
                'payment_status' => $this->due_amount <= 0 ? 'Paid' : ($this->due_amount == $this->net_payable ? 'Unpaid' : 'Partial'),
                'status' => $this->invoiceStatus,
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
                    'price' => $item['mrp'],
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

            DB::commit();
            session()->flash('message', '✅ Invoice #' . $invoice->invoice_number . ' updated successfully!');
            $this->invoice = $invoice->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Invoice Update Error: " . $e->getMessage());
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

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

        return view('livewire.lab.pos-edit-manager', compact(
            'doctors', 'agents', 'tests', 'paymentModes', 'centers', 'branches'
        ))->layout('layouts.app', ['title' => 'Edit Invoice #' . ($this->invoice->invoice_number ?? '')]);
    }
}

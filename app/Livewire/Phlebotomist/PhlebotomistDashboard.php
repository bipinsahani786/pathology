<?php

namespace App\Livewire\Phlebotomist;

use App\Models\HomeCollection;
use App\Models\InvoiceItem;
use App\Models\LabTest;
use App\Models\Payment;
use App\Models\PhlebotomistCashHandover;
use App\Models\PhlebotomistProfile;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class PhlebotomistDashboard extends Component
{
    // Active navigation tab: 'visits', 'history', 'earnings', 'profile'
    public $activeTab = 'visits';

    // Date filter for visits tab
    public $filterDate;

    // History tab filters
    public $historySearch = '';
    public $historyFilter = 'all'; // 'all', 'today', 'week', 'month'

    // Profile tab password update
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // GPS (captured via JS)
    public $capturedLat = null;
    public $capturedLng = null;

    // Payment collection
    public $collectPaymentVisitId = null;
    public $paymentAmount         = 0;
    public $paymentMode           = 'Cash';
    public $isPaymentModalOpen    = false;

    // On-site test addition
    public $addTestVisitId     = null;
    public $testSearchQuery    = '';
    public $testSearchResults  = [];
    public $isAddTestModalOpen = false;

    // Status confirm modal
    public $confirmVisitId = null;
    public $confirmStatus  = '';
    public $isConfirmOpen  = false;

    public function mount()
    {
        $user = auth()->user();

        // Only phlebotomist role allowed
        if (! $user->hasRole('phlebotomist')) {
            abort(403, 'Access denied. Phlebotomist only area.');
        }

        // Plan gate via company
        $hasFeature = $user->company->plan?->features['home_collection'] ?? false;
        if (! $hasFeature) {
            abort(403, 'Home Collection module not enabled.');
        }

        $this->filterDate = now()->format('Y-m-d');
    }

    // ==========================================
    // STATUS MANAGEMENT
    // ==========================================

    public function openConfirm($visitId, $status)
    {
        $this->confirmVisitId = $visitId;
        $this->confirmStatus  = $status;
        $this->isConfirmOpen  = true;
    }

    public function confirmStatusUpdate()
    {
        $visit = HomeCollection::where('phlebotomist_id', auth()->id())
            ->findOrFail($this->confirmVisitId);

        $lat = $this->capturedLat ? (float) $this->capturedLat : null;
        $lng = $this->capturedLng ? (float) $this->capturedLng : null;

        $visit->transitionStatus($this->confirmStatus, auth()->id(), $lat, $lng);

        // Notify patient when en route
        if ($this->confirmStatus === 'En Route') {
            try {
                app(NotificationService::class)->notifyPatientEnRoute(
                    $visit->fresh(['invoice.patient', 'phlebotomist'])
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('En Route notification failed: ' . $e->getMessage());
            }
        }

        $this->capturedLat  = null;
        $this->capturedLng  = null;
        $this->isConfirmOpen = false;
        session()->flash('message', "Status updated to: {$this->confirmStatus}");
    }

    // ==========================================
    // PAYMENT COLLECTION
    // ==========================================

    public function openPaymentModal($visitId)
    {
        $visit = HomeCollection::with('invoice')
            ->where('phlebotomist_id', auth()->id())
            ->findOrFail($visitId);

        $this->collectPaymentVisitId = $visitId;
        $this->paymentAmount         = $visit->invoice->due_amount ?? 0;
        $this->paymentMode           = 'Cash';
        $this->isPaymentModalOpen    = true;
    }

    public function recordPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMode'   => 'required|string',
        ]);

        $visit   = HomeCollection::with('invoice')
            ->where('phlebotomist_id', auth()->id())
            ->findOrFail($this->collectPaymentVisitId);
        $invoice = $visit->invoice;

        $amount  = min((float) $this->paymentAmount, (float) $invoice->due_amount);

        // Find or fallback payment mode
        $paymentModeModel = \App\Models\PaymentMode::where('company_id', $invoice->company_id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->paymentMode . '%');
            })
            ->first() ?? \App\Models\PaymentMode::where('company_id', $invoice->company_id)->where('is_active', true)->first();

        if (! $paymentModeModel) {
            $paymentModeModel = \App\Models\PaymentMode::create([
                'company_id' => $invoice->company_id,
                'name'       => $this->paymentMode ?: 'Cash',
                'is_active'  => true,
            ]);
        }

        DB::transaction(function () use ($invoice, $amount, $paymentModeModel) {
            Payment::create([
                'company_id'      => $invoice->company_id,
                'invoice_id'      => $invoice->id,
                'patient_id'      => $invoice->patient_id,
                'collected_by'    => auth()->id(),
                'payment_mode_id' => $paymentModeModel->id,
                'amount'          => $amount,
                'remarks'         => 'Collected by phlebotomist during home visit',
            ]);

            $newPaid = (float) $invoice->paid_amount + $amount;
            $newDue  = max(0, (float) $invoice->total_amount - $newPaid);
            $invoice->update([
                'paid_amount'    => $newPaid,
                'due_amount'     => $newDue,
                'payment_status' => $newDue <= 0 ? 'Paid' : 'Partial',
            ]);
        });

        $this->isPaymentModalOpen = false;
        session()->flash('message', "Payment of ₹{$amount} recorded successfully.");
    }

    // ==========================================
    // ON-SITE TEST ADDITION
    // ==========================================

    public function openAddTestModal($visitId)
    {
        $this->authorize('add tests to home_collection');
        $this->addTestVisitId     = $visitId;
        $this->testSearchQuery    = '';
        $this->isAddTestModalOpen = true;

        $this->loadTestSearchResults();
    }

    public function updatedTestSearchQuery()
    {
        $this->loadTestSearchResults();
    }

    protected function loadTestSearchResults()
    {
        if (! $this->addTestVisitId) {
            $this->testSearchResults = [];
            return;
        }

        $visit = HomeCollection::where('phlebotomist_id', auth()->id())
            ->findOrFail($this->addTestVisitId);
        $existingIds = $visit->invoice->items()->pluck('lab_test_id')->filter()->toArray();

        $query = trim($this->testSearchQuery);

        $testQuery = LabTest::where('company_id', $visit->invoice->company_id)
            ->where('is_active', true)
            ->whereNotIn('id', $existingIds);

        if (strlen($query) >= 2) {
            $testQuery->where(function ($q) use ($query) {
                $q->where('name', 'ilike', '%' . $query . '%')
                  ->orWhere('test_code', 'ilike', '%' . $query . '%')
                  ->orWhere('department', 'ilike', '%' . $query . '%');
            });
        } elseif (strlen($query) > 0) {
            $this->testSearchResults = [];
            return;
        }

        $tests = $testQuery->orderBy('name', 'asc')
            ->limit(15)
            ->get(['id', 'name', 'test_code', 'mrp', 'b2b_price', 'department', 'sample_type', 'is_package']);

        $this->testSearchResults = $tests->map(function ($t) {
            return [
                'id'          => $t->id,
                'name'        => $t->name,
                'test_code'   => $t->test_code,
                'price'       => (float) ($t->mrp ?? 0),
                'mrp'         => (float) ($t->mrp ?? 0),
                'department'  => $t->department ?? '',
                'sample_type' => $t->sample_type ?? '',
                'is_package'  => (bool) $t->is_package,
            ];
        })->toArray();
    }

    public function addTestToVisit($labTestId)
    {
        $this->authorize('add tests to home_collection');

        $visit   = HomeCollection::with('invoice.items')
            ->where('phlebotomist_id', auth()->id())
            ->findOrFail($this->addTestVisitId);
        $invoice = $visit->invoice;
        $labTest = LabTest::where('company_id', $invoice->company_id)->findOrFail($labTestId);

        if ($invoice->items()->where('lab_test_id', $labTestId)->exists()) {
            $this->addError('testSearchQuery', 'This test is already in the invoice.');
            return;
        }

        DB::transaction(function () use ($invoice, $labTest) {
            $hasParams = method_exists($labTest, 'hasParameters') ? $labTest->hasParameters() : false;
            $mrp = (float) ($labTest->mrp ?? 0);
            $b2b = (float) ($labTest->b2b_price ?? $mrp);

            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'lab_test_id' => $labTest->id,
                'test_name'   => $labTest->name,
                'is_package'  => (bool) $labTest->is_package,
                'mrp'         => $mrp,
                'price'       => $mrp,
                'b2b_price'   => $b2b,
                'status'      => $hasParams ? 'Pending' : 'Completed',
                'sort_order'  => $invoice->items()->count(),
            ]);

            // Recalculate totals
            $newSubtotal = (float) $invoice->items()->sum('price');
            $newTotal    = max(0, $newSubtotal - (float) $invoice->discount_amount);
            $newPaid     = (float) $invoice->paid_amount;
            $newDue      = max(0, $newTotal - $newPaid);
            $invoice->update([
                'subtotal'       => $newSubtotal,
                'total_amount'   => $newTotal,
                'due_amount'     => $newDue,
                'payment_status' => $newDue <= 0 ? 'Paid' : ($newPaid > 0 ? 'Partial' : 'Unpaid'),
            ]);
        });

        $this->isAddTestModalOpen = false;
        $this->testSearchQuery = '';
        $this->testSearchResults = [];
        session()->flash('message', "Test '{$labTest->name}' (₹" . number_format($labTest->mrp ?? 0, 2) . ") added successfully.");
    }

    // ==========================================
    // TAB NAVIGATION & PROFILE ACTIONS
    // ==========================================

    public function setTab($tab)
    {
        if (in_array($tab, ['visits', 'history', 'earnings', 'profile'])) {
            $this->activeTab = $tab;
        }
    }

    public function toggleDutyStatus()
    {
        $user = auth()->user();
        $profile = PhlebotomistProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['company_id' => $user->company_id, 'is_available' => true]
        );

        $profile->is_available = ! $profile->is_available;
        $profile->save();

        $statusText = $profile->is_available ? 'ON DUTY (Ready for visits)' : 'OFF DUTY';
        session()->flash('message', "Duty status updated: {$statusText}");
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:6|confirmed',
        ]);

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'The current password you entered is incorrect.');
            return;
        }

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('message', 'Password updated successfully.');
    }

    // Cash Handover Modal
    public $isHandoverModalOpen = false;
    public $handoverNotes       = '';

    public function openHandoverModal()
    {
        $this->handoverNotes = '';
        $this->isHandoverModalOpen = true;
    }

    public function requestCashHandover()
    {
        $user = auth()->user();

        // Calculate pending cash
        $todayPayments = Payment::where('collected_by', $user->id)
            ->whereDate('created_at', now())
            ->with('paymentMode')
            ->get();

        $cashTotal = $todayPayments->filter(function ($p) {
            $mode = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
            return str_contains($mode, 'cash');
        })->sum('amount');

        $handovers = PhlebotomistCashHandover::where('phlebotomist_id', $user->id)
            ->whereDate('created_at', now())
            ->whereIn('status', ['pending', 'approved'])
            ->get();

        $alreadyHanded = $handovers->sum('amount');
        $pendingAmount = max(0, (float) $cashTotal - (float) $alreadyHanded);

        if ($pendingAmount <= 0) {
            $this->addError('handoverNotes', 'No pending physical cash available to hand over.');
            return;
        }

        PhlebotomistCashHandover::create([
            'company_id'      => $user->company_id,
            'phlebotomist_id' => $user->id,
            'amount'          => $pendingAmount,
            'status'          => 'pending',
            'notes'           => $this->handoverNotes ?: 'Physical cash submitted to lab reception',
        ]);

        $this->isHandoverModalOpen = false;
        $this->handoverNotes       = '';
        session()->flash('message', 'Cash handover request of ₹' . number_format($pendingAmount, 2) . ' submitted. Awaiting Lab Admin verification & approval.');
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $user = auth()->user();

        // 1. Visits for active runs
        $visits = HomeCollection::with([
            'invoice.patient',
            'invoice.items.labTest',
        ])
        ->where('phlebotomist_id', $user->id)
        ->whereDate('scheduled_date', $this->filterDate)
        ->whereIn('status', ['Assigned', 'En Route', 'Arrived', 'Collected'])
        ->orderBy('scheduled_slot_start', 'asc')
        ->get();

        $completedStatuses = ['Collected', 'Dispatched', 'Received'];

        $todayCount  = HomeCollection::where('phlebotomist_id', $user->id)
            ->whereDate('scheduled_date', now())->count();
        $doneToday   = HomeCollection::where('phlebotomist_id', $user->id)
            ->whereDate('scheduled_date', now())
            ->whereIn('status', $completedStatuses)
            ->count();

        // 2. Profile & Duty status
        $profile = PhlebotomistProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['company_id' => $user->company_id, 'is_available' => true]
        );

        // 3. History Visits
        $historyQuery = HomeCollection::with([
            'invoice.patient',
            'invoice.items.labTest',
        ])
        ->where('phlebotomist_id', $user->id);

        if ($this->historyFilter === 'today') {
            $historyQuery->whereDate('scheduled_date', now());
        } elseif ($this->historyFilter === 'week') {
            $historyQuery->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($this->historyFilter === 'month') {
            $historyQuery->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year);
        }

        if (! empty(trim($this->historySearch))) {
            $q = trim($this->historySearch);
            $historyQuery->where(function ($query) use ($q) {
                $query->whereHas('invoice.patient', function ($pq) use ($q) {
                    $pq->where('name', 'ilike', "%{$q}%")
                       ->orWhere('phone', 'ilike', "%{$q}%");
                })
                ->orWhereHas('invoice', function ($iq) use ($q) {
                    $iq->where('invoice_number', 'ilike', "%{$q}%");
                })
                ->orWhere('collection_address', 'ilike', "%{$q}%")
                ->orWhere('sample_barcode', 'ilike', "%{$q}%");
            });
        }

        $historyVisits = $historyQuery->orderByDesc('scheduled_date')
            ->orderByDesc('id')
            ->limit(40)
            ->get();

        // 4. Earnings & Commissions Math (Includes Collected, Dispatched, Received)
        $commissionPerVisit = (float) ($profile->commission_per_visit ?? 0);

        $todayCollectedCount = HomeCollection::where('phlebotomist_id', $user->id)
            ->whereDate('scheduled_date', now())
            ->whereIn('status', $completedStatuses)
            ->count();

        $monthCollectedCount = HomeCollection::where('phlebotomist_id', $user->id)
            ->whereMonth('scheduled_date', now()->month)
            ->whereYear('scheduled_date', now()->year)
            ->whereIn('status', $completedStatuses)
            ->count();

        $totalCollectedCount = HomeCollection::where('phlebotomist_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->count();

        $todayCommission = $todayCollectedCount * $commissionPerVisit;
        $monthCommission = $monthCollectedCount * $commissionPerVisit;
        $totalCommission = $totalCollectedCount * $commissionPerVisit;

        // 5. Cash Handover Reconciliation (Payments collected by phlebotomist today)
        $todayPayments = Payment::where('collected_by', $user->id)
            ->whereDate('created_at', now())
            ->with(['paymentMode', 'invoice.patient'])
            ->orderByDesc('created_at')
            ->get();

        $todayCashCollected = $todayPayments->filter(function ($p) {
            $mode = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
            return str_contains($mode, 'cash');
        })->sum('amount');

        // Cash handovers to lab reception
        $todayHandovers = PhlebotomistCashHandover::where('phlebotomist_id', $user->id)
            ->whereDate('created_at', now())
            ->with('approvedBy')
            ->latest()
            ->get();

        $todayApprovedHandovers = $todayHandovers->where('status', 'approved')->sum('amount');
        $todayPendingHandovers  = $todayHandovers->where('status', 'pending')->sum('amount');
        $todayPendingCash       = max(0, $todayCashCollected - $todayApprovedHandovers - $todayPendingHandovers);

        $latestHandover = $todayHandovers->first();

        $todayDigitalCollected = $todayPayments->filter(function ($p) {
            $mode = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
            return ! str_contains($mode, 'cash');
        })->sum('amount');

        $todayTotalCollected = $todayPayments->sum('amount');

        // Recent Completed Visits for Commission Ledger
        $commissionLedger = HomeCollection::with(['invoice.patient', 'invoice.items.labTest', 'commissionSettlement'])
            ->where('phlebotomist_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->orderByDesc('collected_at')
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        // Commission settlements / payouts received from lab
        $commissionPayouts = \App\Models\PhlebotomistCommissionSettlement::where('phlebotomist_id', $user->id)
            ->with('settledBy')
            ->latest()
            ->get();

        $totalCommissionPaid = (float) $commissionPayouts->sum('amount');
        $totalCommissionPending = max(0, $totalCommission - $totalCommissionPaid);

        $company = $user->company;

        return view('livewire.phlebotomist.dashboard', compact(
            'visits',
            'todayCount',
            'doneToday',
            'profile',
            'historyVisits',
            'commissionPerVisit',
            'todayCollectedCount',
            'monthCollectedCount',
            'totalCollectedCount',
            'todayCommission',
            'monthCommission',
            'totalCommission',
            'totalCommissionPaid',
            'totalCommissionPending',
            'commissionPayouts',
            'todayPayments',
            'todayCashCollected',
            'todayPendingCash',
            'todayApprovedHandovers',
            'todayPendingHandovers',
            'latestHandover',
            'todayDigitalCollected',
            'todayTotalCollected',
            'commissionLedger',
            'company'
        ))->layout('layouts.phlebotomist', ['title' => 'Phlebotomist Portal']);
    }
}

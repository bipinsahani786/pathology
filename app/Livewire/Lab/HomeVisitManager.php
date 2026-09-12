<?php

namespace App\Livewire\Lab;

use App\Models\Branch;
use App\Models\Configuration;
use App\Models\HomeCollection;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class HomeVisitManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $searchTerm        = '';
    public $filterStatus      = '';
    public $filterDate        = '';
    public $filterPhlebotomist = '';
    public $filterBranch      = '';

    // Assignment modal
    public $selectedVisitId    = null;
    public $phlebotomist_id    = null;
    public $assignNotes        = '';
    public $isAssignModalOpen  = false;

    // Status update modal
    public $statusVisitId      = null;
    public $newStatus          = '';
    public $statusNotes        = '';
    public $isStatusModalOpen  = false;

    // Live Tracking & GPS Audit modal
    public $trackingVisitId     = null;
    public $isTrackingModalOpen = false;

    // View: list or schedule
    public $viewMode           = 'list';
    public $scheduleDate;

    // Tab switching
    public $activeTab          = 'visits'; // 'visits' | 'analytics'

    // Analytics Filters
    public $analyticsPeriod    = 'all'; // 'today', 'this_week', 'this_month', 'all'

    // Detailed Ledger Modal
    public $selectedPhleboId   = null;
    public $isLedgerModalOpen  = false;

    // Commission Edit Modal
    public $editCommissionPhleboId = null;
    public $editCommissionRate     = '';
    public $isCommissionModalOpen  = false;

    // Direct Cash Settle Modal
    public $directSettlePhleboId   = null;
    public $directSettleAmount     = '';
    public $directSettleNotes      = '';
    public $isDirectSettleModalOpen = false;

    // Commission Payout Modal
    public $payoutPhleboId      = null;
    public $payoutAmount        = '';
    public $payoutMode          = 'Cash'; // Cash, UPI, Bank Transfer, Cheque
    public $payoutReference     = '';
    public $payoutNotes         = '';
    public $isPayoutModalOpen   = false;

    public function mount()
    {
        $hasFeature = auth()->user()->company->plan?->features['home_collection'] ?? false;
        if (! $hasFeature) {
            abort(403, 'Home Collection module is not available on your current plan.');
        }
        $this->authorize('view home_collections');
        $this->filterDate    = now()->format('Y-m-d');
        $this->scheduleDate  = now()->format('Y-m-d');
    }

    public function updatingSearchTerm() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDate() { $this->resetPage(); }

    // ==========================================
    // ASSIGNMENT
    // ==========================================

    public function openAssignModal($visitId)
    {
        $this->authorize('assign home_collections');
        $this->selectedVisitId = $visitId;
        $this->phlebotomist_id = null;
        $this->assignNotes     = '';
        $this->isAssignModalOpen = true;
    }

    public function assignPhlebotomist()
    {
        $this->authorize('assign home_collections');
        $this->validate([
            'phlebotomist_id' => 'required|exists:users,id',
        ]);

        $visit = HomeCollection::where('company_id', auth()->user()->company_id)
            ->findOrFail($this->selectedVisitId);

        $visit->phlebotomist_id = $this->phlebotomist_id;
        $visit->assigned_by     = auth()->id();
        $visit->save();
        $visit->transitionStatus('Assigned', auth()->id(), null, null, $this->assignNotes);

        // Update invoice phlebotomist_id too
        $visit->invoice()->update(['phlebotomist_id' => $this->phlebotomist_id]);

        // Notify patient
        try {
            app(NotificationService::class)->notifyPatientAssigned($visit->fresh(['invoice.patient', 'phlebotomist']));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Notification failed: ' . $e->getMessage());
        }

        $this->isAssignModalOpen = false;
        session()->flash('message', 'Phlebotomist assigned successfully.');
    }

    // ==========================================
    // STATUS UPDATE (Admin side)
    // ==========================================

    public function openStatusModal($visitId, $currentStatus)
    {
        $this->authorize('assign home_collections');
        $this->statusVisitId = $visitId;
        $this->newStatus     = $currentStatus;
        $this->statusNotes   = '';
        $this->isStatusModalOpen = true;
    }

    public function updateStatus()
    {
        $this->authorize('assign home_collections');
        $this->validate([
            'newStatus'   => 'required|in:Pending,Assigned,En Route,Arrived,Collected,Dispatched,Received,Cancelled',
            'statusNotes' => 'nullable|string|max:500',
        ]);

        $visit = HomeCollection::where('company_id', auth()->user()->company_id)
            ->findOrFail($this->statusVisitId);

        $visit->transitionStatus($this->newStatus, auth()->id(), null, null, $this->statusNotes);

        $this->isStatusModalOpen = false;
        session()->flash('message', "Visit status updated to: {$this->newStatus}");
    }

    public function cancelVisit($visitId)
    {
        $this->authorize('assign home_collections');
        $visit = HomeCollection::where('company_id', auth()->user()->company_id)->findOrFail($visitId);
        $visit->transitionStatus('Cancelled', auth()->id(), null, null, 'Cancelled by admin');
        session()->flash('message', 'Visit cancelled.');
    }

    // ==========================================
    // LIVE TRACKING & GPS AUDIT
    // ==========================================

    public function openTrackingModal($visitId)
    {
        $this->trackingVisitId = $visitId;
        $this->isTrackingModalOpen = true;
    }

    public function closeTrackingModal()
    {
        $this->isTrackingModalOpen = false;
        $this->trackingVisitId = null;
    }

    // ==========================================
    // PHLEBOTOMIST CASH HANDOVER APPROVAL
    // ==========================================

    public function approveCashHandover($handoverId)
    {
        $this->authorize('assign home_collections');

        $handover = \App\Models\PhlebotomistCashHandover::with('phlebotomist')
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($handoverId);

        $handover->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Mark phlebotomist's cash payments as deposited
        \App\Models\Payment::where('collected_by', $handover->phlebotomist_id)
            ->whereDate('created_at', $handover->created_at->format('Y-m-d'))
            ->where(function ($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', 'not like', '%[Deposited at Reception%');
            })
            ->update([
                'remarks' => \Illuminate\Support\Facades\DB::raw("COALESCE(remarks, '') || ' [Deposited at Reception: Approved by Lab Admin]'")
            ]);

        session()->flash('message', "Cash handover of ₹" . number_format($handover->amount, 2) . " from {$handover->phlebotomist->name} approved and settled.");
    }

    public function rejectCashHandover($handoverId, $reason = 'Amount mismatch or cash not received')
    {
        $this->authorize('assign home_collections');

        $handover = \App\Models\PhlebotomistCashHandover::with('phlebotomist')
            ->where('company_id', auth()->user()->company_id)
            ->findOrFail($handoverId);

        $handover->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'approved_by'      => auth()->id(),
        ]);

        session()->flash('message', "Cash handover of ₹" . number_format($handover->amount, 2) . " rejected.");
    }

    // ==========================================
    // PHLEBOTOMIST ANALYTICS & SETTLEMENTS
    // ==========================================

    public function setTab($tab)
    {
        if (in_array($tab, ['visits', 'analytics'])) {
            $this->activeTab = $tab;
        }
    }

    public function setAnalyticsPeriod($period)
    {
        if (in_array($period, ['today', 'this_week', 'this_month', 'all'])) {
            $this->analyticsPeriod = $period;
        }
    }

    public function openLedgerModal($phleboId)
    {
        $this->selectedPhleboId = $phleboId;
        $this->isLedgerModalOpen = true;
    }

    public function closeLedgerModal()
    {
        $this->isLedgerModalOpen = false;
        $this->selectedPhleboId  = null;
    }

    public function openCommissionModal($phleboId)
    {
        $this->authorize('assign home_collections');
        $phlebo = User::where('company_id', auth()->user()->company_id)
            ->with('phlebotomistProfile')
            ->findOrFail($phleboId);

        $this->editCommissionPhleboId = $phleboId;
        $this->editCommissionRate     = (float) ($phlebo->phlebotomistProfile?->commission_per_visit > 0 
            ? $phlebo->phlebotomistProfile->commission_per_visit 
            : 50.00);
        $this->isCommissionModalOpen  = true;
    }

    public function saveCommissionRate()
    {
        $this->authorize('assign home_collections');
        $this->validate([
            'editCommissionRate' => 'required|numeric|min:0',
        ]);

        $profile = \App\Models\PhlebotomistProfile::firstOrCreate(
            ['user_id' => $this->editCommissionPhleboId],
            ['company_id' => auth()->user()->company_id, 'is_available' => true]
        );

        $profile->update([
            'commission_per_visit' => $this->editCommissionRate,
        ]);

        $this->isCommissionModalOpen = false;
        session()->flash('message', 'Phlebotomist commission rate updated successfully.');
    }

    public function openDirectSettleModal($phleboId, $dueAmount)
    {
        $this->authorize('assign home_collections');
        $this->directSettlePhleboId     = $phleboId;
        $this->directSettleAmount       = (float) $dueAmount;
        $this->directSettleNotes        = 'Cash handed over directly at lab reception';
        $this->isDirectSettleModalOpen  = true;
    }

    public function recordDirectSettlement()
    {
        $this->authorize('assign home_collections');
        $this->validate([
            'directSettleAmount' => 'required|numeric|min:0.01',
            'directSettleNotes'  => 'nullable|string|max:500',
        ]);

        $phlebo = User::where('company_id', auth()->user()->company_id)->findOrFail($this->directSettlePhleboId);

        \App\Models\PhlebotomistCashHandover::create([
            'company_id'      => auth()->user()->company_id,
            'phlebotomist_id' => $phlebo->id,
            'amount'          => $this->directSettleAmount,
            'status'          => 'approved',
            'notes'           => $this->directSettleNotes ?: 'Direct settlement recorded by Lab Reception',
            'approved_by'     => auth()->id(),
            'approved_at'     => now(),
        ]);

        // Mark phlebotomist's cash payments as deposited
        \App\Models\Payment::where('collected_by', $phlebo->id)
            ->where(function ($q) {
                $q->whereNull('remarks')
                  ->orWhere('remarks', 'not like', '%[Deposited at Reception%');
            })
            ->update([
                'remarks' => \Illuminate\Support\Facades\DB::raw("COALESCE(remarks, '') || ' [Deposited at Reception: Approved by Lab Admin]'")
            ]);

        $this->isDirectSettleModalOpen = false;
        session()->flash('message', "Cash handover of ₹" . number_format($this->directSettleAmount, 2) . " settled for {$phlebo->name}.");
    }

    public function openPayoutModal($phleboId, $unpaidCommission)
    {
        $this->authorize('assign home_collections');
        $phlebo = User::where('company_id', auth()->user()->company_id)->findOrFail($phleboId);

        $this->payoutPhleboId    = $phleboId;
        $this->payoutAmount      = (float) $unpaidCommission;
        $this->payoutMode        = 'Cash';
        $this->payoutReference   = '';
        $this->payoutNotes       = 'Commission payout for completed home visits';
        $this->isPayoutModalOpen = true;
    }

    public function recordCommissionPayout()
    {
        $this->authorize('assign home_collections');
        $this->validate([
            'payoutAmount'    => 'required|numeric|min:0.01',
            'payoutMode'      => 'required|string',
            'payoutReference' => 'nullable|string|max:100',
            'payoutNotes'     => 'nullable|string|max:500',
        ]);

        $companyId = auth()->user()->company_id;
        $phlebo = User::where('company_id', $companyId)->findOrFail($this->payoutPhleboId);

        $profile = $phlebo->phlebotomistProfile;
        $rate = (float) (($profile && $profile->commission_per_visit > 0) ? $profile->commission_per_visit : 50.00);

        $unsettledVisits = HomeCollection::where('company_id', $companyId)
            ->where('phlebotomist_id', $phlebo->id)
            ->whereIn('status', ['Collected', 'Dispatched', 'Received'])
            ->where('is_commission_settled', false)
            ->orderBy('collected_at', 'asc')
            ->get();

        DB::transaction(function () use ($companyId, $phlebo, $unsettledVisits, $rate) {
            $settlement = \App\Models\PhlebotomistCommissionSettlement::create([
                'company_id'      => $companyId,
                'phlebotomist_id' => $phlebo->id,
                'amount'          => $this->payoutAmount,
                'payment_date'    => now(),
                'payment_mode'    => $this->payoutMode,
                'reference_no'    => $this->payoutReference,
                'notes'           => $this->payoutNotes,
                'settled_by'      => auth()->id(),
                'visits_count'    => $unsettledVisits->count(),
            ]);

            foreach ($unsettledVisits as $visit) {
                $visit->update([
                    'is_commission_settled'    => true,
                    'commission_amount'        => $rate,
                    'commission_settled_at'    => now(),
                    'commission_settlement_id' => $settlement->id,
                ]);
            }
        });

        $this->isPayoutModalOpen = false;
        session()->flash('message', "Commission payout of ₹" . number_format($this->payoutAmount, 2) . " paid to {$phlebo->name} successfully!");
    }

    // ==========================================
    // RENDER
    // ==========================================

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $query = HomeCollection::with([
            'invoice.patient',
            'phlebotomist',
            'branch',
        ])
        ->where('company_id', $companyId);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        if ($this->filterDate) {
            $query->whereDate('scheduled_date', $this->filterDate);
        }
        if ($this->filterPhlebotomist) {
            $query->where('phlebotomist_id', $this->filterPhlebotomist);
        }
        if ($this->filterBranch) {
            $query->where('branch_id', $this->filterBranch);
        }
        if ($this->searchTerm) {
            $query->whereHas('invoice.patient', fn ($q) =>
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%')
            )->orWhere('collection_address', 'ilike', '%' . $this->searchTerm . '%');
        }

        $visits = $query->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_slot_start', 'asc')
            ->paginate(15);

        $phlebotomists = User::whereHas('roles', fn ($q) => $q->where('name', 'phlebotomist'))
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->with('phlebotomistProfile')
            ->get(['id', 'name', 'phone']);

        $branches = Branch::where('company_id', $companyId)->get(['id', 'name']);

        $statusCounts = HomeCollection::where('company_id', $companyId)
            ->whereDate('scheduled_date', $this->filterDate ?: now()->format('Y-m-d'))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $completedTodayCount = ($statusCounts['Collected'] ?? 0)
            + ($statusCounts['Dispatched'] ?? 0)
            + ($statusCounts['Received'] ?? 0);

        $trackingVisit = null;
        if ($this->isTrackingModalOpen && $this->trackingVisitId) {
            $trackingVisit = HomeCollection::with([
                'invoice.patient',
                'invoice.items',
                'phlebotomist.phlebotomistProfile',
                'branch',
                'statusLogs.changedBy',
            ])
            ->where('company_id', $companyId)
            ->find($this->trackingVisitId);
        }

        // Pending phlebotomist cash handovers requiring lab admin approval
        $pendingHandovers = \App\Models\PhlebotomistCashHandover::with(['phlebotomist'])
            ->where('company_id', $companyId)
            ->where('status', 'pending')
            ->latest()
            ->get();

        // ==========================================
        // PHLEBOTOMIST ANALYTICS CALCULATIONS
        // ==========================================
        $allPhlebotomists = User::whereHas('roles', fn ($q) => $q->where('name', 'phlebotomist'))
            ->where('company_id', $companyId)
            ->with(['phlebotomistProfile'])
            ->get();

        $phlebotomistAnalytics = [];
        $totalFieldCashCollectedAll = 0;
        $totalCashSettledAll        = 0;
        $totalPendingCashAll        = 0;
        $totalCommissionEarnedAll   = 0;
        $totalCommissionSettledAll  = 0;
        $totalCommissionPayableAll  = 0;
        $totalCompletedVisitsAll    = 0;

        foreach ($allPhlebotomists as $phlebo) {
            $profile = $phlebo->phlebotomistProfile;
            $rate = (float) (($profile && $profile->commission_per_visit > 0) ? $profile->commission_per_visit : 50.00);

            // Completed visits query (filter by period if selected)
            $completedVisitsQuery = HomeCollection::where('company_id', $companyId)
                ->where('phlebotomist_id', $phlebo->id)
                ->whereIn('status', ['Collected', 'Dispatched', 'Received']);

            if ($this->analyticsPeriod === 'today') {
                $completedVisitsQuery->whereDate('scheduled_date', now());
            } elseif ($this->analyticsPeriod === 'this_week') {
                $completedVisitsQuery->whereBetween('scheduled_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->analyticsPeriod === 'this_month') {
                $completedVisitsQuery->whereMonth('scheduled_date', now()->month)
                    ->whereYear('scheduled_date', now()->year);
            }

            $completedCount = $completedVisitsQuery->count();
            $commissionEarned = $completedCount * $rate;

            // Commission settlements / payouts for this phlebotomist
            $settlementsQuery = \App\Models\PhlebotomistCommissionSettlement::where('company_id', $companyId)
                ->where('phlebotomist_id', $phlebo->id);

            if ($this->analyticsPeriod === 'today') {
                $settlementsQuery->whereDate('payment_date', now());
            } elseif ($this->analyticsPeriod === 'this_week') {
                $settlementsQuery->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->analyticsPeriod === 'this_month') {
                $settlementsQuery->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
            }

            $commissionSettled = (float) $settlementsQuery->sum('amount');
            $commissionPayable = max(0, (float) $commissionEarned - (float) $commissionSettled);

            // Payments collected by this phlebotomist
            $paymentsQuery = \App\Models\Payment::where('company_id', $companyId)
                ->where('collected_by', $phlebo->id)
                ->with('paymentMode');

            if ($this->analyticsPeriod === 'today') {
                $paymentsQuery->whereDate('created_at', now());
            } elseif ($this->analyticsPeriod === 'this_week') {
                $paymentsQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->analyticsPeriod === 'this_month') {
                $paymentsQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            }

            $payments = $paymentsQuery->get();

            $cashCollected = $payments->filter(function ($p) {
                $mode = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
                return str_contains($mode, 'cash');
            })->sum('amount');

            $digitalCollected = $payments->filter(function ($p) {
                $mode = strtolower($p->paymentMode?->name ?? $p->remarks ?? '');
                return ! str_contains($mode, 'cash');
            })->sum('amount');

            // Handovers for this phlebotomist
            $handoversQuery = \App\Models\PhlebotomistCashHandover::where('company_id', $companyId)
                ->where('phlebotomist_id', $phlebo->id);

            if ($this->analyticsPeriod === 'today') {
                $handoversQuery->whereDate('created_at', now());
            } elseif ($this->analyticsPeriod === 'this_week') {
                $handoversQuery->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->analyticsPeriod === 'this_month') {
                $handoversQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            }

            $handovers = $handoversQuery->get();
            $approvedHandovers = $handovers->where('status', 'approved')->sum('amount');
            $pendingHandoversAmount = $handovers->where('status', 'pending')->sum('amount');

            // Cash in hand due to lab (Collected cash minus approved settled handovers)
            $cashInHandDue = max(0, (float) $cashCollected - (float) $approvedHandovers);

            $phlebotomistAnalytics[] = [
                'user'                  => $phlebo,
                'profile'               => $profile,
                'completed_visits'      => $completedCount,
                'commission_rate'       => $rate,
                'commission_earned'     => $commissionEarned,
                'commission_settled'    => $commissionSettled,
                'commission_payable'    => $commissionPayable, // "Lab ko inko kitna dena hai"
                'total_cash_collected'  => $cashCollected,
                'digital_collected'     => $digitalCollected,
                'total_collected'       => $cashCollected + $digitalCollected,
                'approved_handovers'    => $approvedHandovers,
                'pending_handovers'     => $pendingHandoversAmount,
                'cash_in_hand_due'      => $cashInHandDue, // "Kise kitna lena hai"
            ];

            $totalFieldCashCollectedAll += $cashCollected;
            $totalCashSettledAll        += $approvedHandovers;
            $totalPendingCashAll        += $cashInHandDue;
            $totalCommissionEarnedAll   += $commissionEarned;
            $totalCommissionSettledAll  += $commissionSettled;
            $totalCommissionPayableAll  += $commissionPayable;
            $totalCompletedVisitsAll    += $completedCount;
        }

        // Ledger modal data if open
        $selectedPhleboData = null;
        if ($this->isLedgerModalOpen && $this->selectedPhleboId) {
            $ledgerPhlebo = User::with('phlebotomistProfile')->find($this->selectedPhleboId);
            $ledgerVisits = HomeCollection::with(['invoice.patient', 'invoice.items.labTest'])
                ->where('company_id', $companyId)
                ->where('phlebotomist_id', $this->selectedPhleboId)
                ->whereIn('status', ['Collected', 'Dispatched', 'Received'])
                ->orderByDesc('collected_at')
                ->orderByDesc('id')
                ->get();

            $ledgerPayments = \App\Models\Payment::with(['invoice.patient', 'paymentMode'])
                ->where('company_id', $companyId)
                ->where('collected_by', $this->selectedPhleboId)
                ->orderByDesc('created_at')
                ->get();

            $ledgerHandovers = \App\Models\PhlebotomistCashHandover::with('approvedBy')
                ->where('company_id', $companyId)
                ->where('phlebotomist_id', $this->selectedPhleboId)
                ->orderByDesc('created_at')
                ->get();

            $ledgerCommissionSettlements = \App\Models\PhlebotomistCommissionSettlement::with('settledBy')
                ->where('company_id', $companyId)
                ->where('phlebotomist_id', $this->selectedPhleboId)
                ->orderByDesc('payment_date')
                ->get();

            $selectedPhleboData = [
                'user'                   => $ledgerPhlebo,
                'visits'                 => $ledgerVisits,
                'payments'               => $ledgerPayments,
                'handovers'              => $ledgerHandovers,
                'commission_settlements' => $ledgerCommissionSettlements,
            ];
        }

        return view('livewire.lab.home-visit-manager', compact(
            'visits',
            'phlebotomists',
            'branches',
            'statusCounts',
            'completedTodayCount',
            'trackingVisit',
            'pendingHandovers',
            'phlebotomistAnalytics',
            'totalFieldCashCollectedAll',
            'totalCashSettledAll',
            'totalPendingCashAll',
            'totalCommissionEarnedAll',
            'totalCommissionSettledAll',
            'totalCommissionPayableAll',
            'totalCompletedVisitsAll',
            'selectedPhleboData'
        ))->layout('layouts.app', ['title' => 'Home Visits']);
    }
}

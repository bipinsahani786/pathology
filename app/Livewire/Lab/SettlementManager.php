<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\{User, Invoice, Settlement, PaymentMode};
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class SettlementManager extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $partnerType = 'Doctor'; // Doctor, Agent, Collection Center
    public $searchPartner = '';
    public $selectedPartnerId = null;
    public $selectedPartner = null;
    public $selectedInvoices = [];
    public $payment_date;
    public $payment_mode = 'Cash';
    public $reference_no = '';
    public $amount_to_pay = 0;
    public $viewMode = 'list'; // list, process, insights
    public $notes = '';

    // Insights Filters & Stats
    public $startDate;
    public $endDate;
    public $partnerStats = [
        'total_bills' => 0,
        'total_revenue' => 0,
        'total_commission' => 0,
        'avg_bill' => 0,
        'settlement_history' => []
    ];
    public $partnerHistory = [];

    protected $queryString = ['partnerType'];

    public function mount()
    {
        $this->authorize('view settlements');
        $this->payment_date = date('Y-m-d');
        $this->startDate = date('Y-m-01'); // Start of month
        $this->endDate = date('Y-m-d');
    }

    public function updatedPartnerType()
    {
        $this->reset(['selectedPartnerId', 'selectedPartner', 'selectedInvoices', 'amount_to_pay']);
        $this->resetPage('partnersPage');
    }

    public function updatedSearchPartner()
    {
        $this->resetPage('partnersPage');
    }

    public function updatedStartDate()
    {
        if ($this->viewMode === 'insights') $this->loadPartnerInsights();
    }

    public function updatedEndDate()
    {
        if ($this->viewMode === 'insights') $this->loadPartnerInsights();
    }

    public function selectPartner($id, $mode = 'process')
    {
        $this->selectedPartnerId = $id;
        $this->selectedPartner = User::find($id);
        $this->viewMode = $mode;
        $this->reset(['selectedInvoices', 'amount_to_pay']);
        
        if ($mode === 'insights') {
            $this->loadPartnerInsights();
        }
    }

    public function loadPartnerInsights()
    {
        $companyId = auth()->user()->company_id;
        $partnerIdField = '';
        if ($this->partnerType === 'Doctor') $partnerIdField = 'referred_by_doctor_id';
        elseif ($this->partnerType === 'Agent') $partnerIdField = 'referred_by_agent_id';
        elseif ($this->partnerType === 'Collection Center') $partnerIdField = 'collection_center_id';

        $valId = ($this->partnerType === 'Collection Center') ? $this->selectedPartner->collection_center_id : $this->selectedPartnerId;

        $activeBranchId = session('active_branch_id', 'all');
        $myBranchId = auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') 
            ? ($activeBranchId === 'all' ? null : $activeBranchId) 
            : auth()->user()->branch_id;

        $query = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
            ->where($partnerIdField, $valId)
            ->where('status', '!=', 'Cancelled');

        $statsQuery = (clone $query)->whereBetween('invoice_date', [$this->startDate, $this->endDate]);

        // Define which field represents the commission/due for this partner
        $commField = '';
        if ($this->partnerType === 'Doctor') $commField = 'doctor_commission_amount';
        elseif ($this->partnerType === 'Agent') $commField = 'agent_commission_amount';
        elseif ($this->partnerType === 'Collection Center') $commField = 'total_b2b_amount';

        $this->partnerStats = [
            'total_bills' => (clone $statsQuery)->count(),
            'total_revenue' => (clone $statsQuery)->sum('total_amount'),
            'total_commission' => (clone $statsQuery)->where('payment_status', 'Paid')->sum($commField),
            'avg_bill' => (clone $statsQuery)->avg('total_amount') ?? 0,
            'settlement_history' => Settlement::where('user_id', $this->selectedPartnerId)
                ->where('company_id', $companyId)
                ->when($myBranchId, fn($q) => $q->whereHas('user', fn($u) => $u->where('branch_id', $myBranchId)))
                ->latest()
                ->take(10)
                ->get()
        ];

        // Fetch Detailed History for the Table (using the date filter)
        $this->partnerHistory = (clone $statsQuery)
            ->with(['patient', 'doctor', 'collectionCenter'])
            ->latest()
            ->take(20)
            ->get();
    }

    public function toggleInvoice($id)
    {
        if (in_array($id, $this->selectedInvoices)) {
            $this->selectedInvoices = array_diff($this->selectedInvoices, [$id]);
        } else {
            $this->selectedInvoices[] = $id;
        }
        $this->updatedSelectedInvoices();
    }

    public function updatedSelectedInvoices()
    {
        if (empty($this->selectedInvoices)) {
            $this->amount_to_pay = 0;
            return;
        }

        // Ensure we only have numeric IDs to avoid TypeErrors in whereIn
        $ids = array_values(array_filter($this->selectedInvoices, fn($v) => is_numeric($v)));
        
        if (empty($ids)) {
            $this->amount_to_pay = 0;
            return;
        }

        $invoices = Invoice::whereIn('id', $ids)->get();
        if ($this->partnerType === 'Doctor') {
            $this->amount_to_pay = $invoices->sum('doctor_commission_amount');
        } elseif ($this->partnerType === 'Agent') {
            $this->amount_to_pay = $invoices->sum('agent_commission_amount');
        } elseif ($this->partnerType === 'Collection Center') {
            // For Collection Center, the amount they owe to the Lab is the B2B amount
            $this->amount_to_pay = $invoices->sum('total_b2b_amount');
        } else {
            $this->amount_to_pay = $invoices->sum('total_amount');
        }
    }

    public function processSettlement()
    {
        $this->authorize('create settlements');
        $companyId = auth()->user()->company_id;
        $partnerIdField = $this->partnerType === 'Doctor' ? 'referred_by_doctor_id' : 'referred_by_agent_id';

        DB::beginTransaction();
        try {
            // Verify all selected invoices actually belong to this partner and company
            $ids = array_values(array_filter($this->selectedInvoices, fn($v) => is_numeric($v)));
            $validInvoices = Invoice::where('company_id', $companyId)
                ->where($partnerIdField, $this->selectedPartnerId)
                ->whereIn('id', $ids)
                ->pluck('id')
                ->toArray();

            if (empty($validInvoices)) {
                throw new \Exception("Invalid invoices selected.");
            }

            $settlement = Settlement::create([
                'company_id' => $companyId,
                'user_id' => $this->selectedPartnerId,
                'collection_center_id' => ($this->partnerType === 'Collection Center') ? $this->selectedPartner->collection_center_id : null,
                'amount' => $this->amount_to_pay,
                'payment_date' => $this->payment_date,
                'payment_mode' => $this->payment_mode,
                'reference_no' => $this->reference_no,
                'type' => $this->partnerType === 'Collection Center' ? 'CollectionCenter' : $this->partnerType,
                'status' => 'Approved', // Lab Admin initiated settlements are auto-approved
                'notes' => $this->notes,
            ]);

            // Update Invoices - Scoped to company and relationship
            $updateData = [];
            if ($this->partnerType === 'Doctor') {
                $updateData = [
                    'doctor_settlement_id' => $settlement->id, 
                    'is_doctor_settled' => true
                ];
            } elseif ($this->partnerType === 'Agent') {
                $updateData = [
                    'agent_settlement_id' => $settlement->id, 
                    'is_agent_settled' => true
                ];
            } else {
                $updateData = [
                    'cc_settlement_id' => $settlement->id, 
                    'is_cc_settled' => true
                ];
            }

            Invoice::where('company_id', $companyId)
                ->whereIn('id', $validInvoices)
                ->update($updateData);

            DB::commit();
            session()->flash('message', '✅ Settlement processed successfully!');
            $this->reset(['selectedInvoices', 'amount_to_pay', 'reference_no', 'notes']);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    public function approveSettlement($id)
    {
        $this->authorize('edit settlements');
        $settlement = Settlement::findOrFail($id);
        
        if ($settlement->status !== 'Pending') {
            session()->flash('error', 'Only pending settlements can be approved.');
            return;
        }

        $settlement->update(['status' => 'Approved']);
        session()->flash('message', 'Settlement approved successfully.');
    }

    public function rejectSettlement($id)
    {
        $this->authorize('edit settlements');
        $settlement = Settlement::findOrFail($id);
        
        if ($settlement->status !== 'Pending') {
            session()->flash('error', 'Only pending settlements can be rejected.');
            return;
        }

        $settlement->update(['status' => 'Rejected']);
        session()->flash('message', 'Settlement rejected.');
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $settledField = '';
        if ($this->partnerType === 'Doctor') $settledField = 'is_doctor_settled';
        elseif ($this->partnerType === 'Agent') $settledField = 'is_agent_settled';
        elseif ($this->partnerType === 'Collection Center') $settledField = 'is_cc_settled';

        $commField = '';
        if ($this->partnerType === 'Doctor') $commField = 'doctor_commission_amount';
        elseif ($this->partnerType === 'Agent') $commField = 'agent_commission_amount';
        elseif ($this->partnerType === 'Collection Center') $commField = 'total_b2b_amount';
        else $commField = 'total_amount';

        // Re-hydrate selected partner if missing
        if ($this->selectedPartnerId && !$this->selectedPartner) {
            $this->selectedPartner = User::find($this->selectedPartnerId);
        }

        $activeBranchId = session('active_branch_id', 'all');
        $myBranchId = auth()->user()->hasRole('lab_admin') || auth()->user()->hasRole('super_admin') 
            ? ($activeBranchId === 'all' ? null : $activeBranchId) 
            : auth()->user()->branch_id;

        // --- Analytics Calculations ---
        $stats = [
            'total_pending' => 0,
            'settled_today' => Settlement::where('company_id', $companyId)
                ->when($myBranchId, fn($q) => $q->whereHas('user', fn($u) => $u->where('branch_id', $myBranchId)))
                ->where('type', $this->partnerType === 'Collection Center' ? 'CollectionCenter' : $this->partnerType)
                ->whereDate('payment_date', date('Y-m-d'))
                ->sum('amount'),
            'partners_with_pending' => 0,
        ];

        // Global Analytics Query
        $pendingBase = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
            ->where('payment_status', 'Paid')
            ->where('status', '!=', 'Cancelled')
            ->where($settledField, false);

        $statsQuery = (clone $pendingBase);
        $stats['total_pending'] = $statsQuery->sum($commField);
        
        $stats['partners_with_pending'] = User::role($this->partnerType === 'Collection Center' ? 'collection_center' : strtolower($this->partnerType))
            ->where('company_id', $companyId)
            ->whereHas($this->partnerType === 'Collection Center' ? 'collectionCenterInvoices' : 'invoicesAs'.$this->partnerType, function($q) use ($settledField) {
                $q->where($settledField, false)->where('payment_status', 'Paid')->where('status', '!=', 'Cancelled');
            })
            ->count();

        // --- Partners Paginated List ---
        $profileRelation = $this->partnerType === 'Doctor' ? 'doctorProfile' : 'agentProfile';
        
        // --- Partners Paginated List ---
        $partners = User::role($this->partnerType === 'Collection Center' ? 'collection_center' : strtolower($this->partnerType))
            ->where('company_id', $companyId)
            ->when($myBranchId && ($this->partnerType !== 'Collection Center'), fn($q) => $q->where('branch_id', $myBranchId))
            ->withSum([($this->partnerType === 'Collection Center' ? 'collectionCenterInvoices' : 'invoicesAs'.$this->partnerType) . ' as pending_amount' => function($q) use ($settledField, $myBranchId) {
                $q->when($myBranchId, fn($q2) => $q2->where('branch_id', $myBranchId))->where($settledField, false)->where('payment_status', 'Paid')->where('status', '!=', 'Cancelled');
            }], $commField)
            ->withCount([($this->partnerType === 'Collection Center' ? 'collectionCenterInvoices' : 'invoicesAs'.$this->partnerType) . ' as invoice_count' => function($q) use ($settledField, $myBranchId) {
                $q->when($myBranchId, fn($q2) => $q2->where('branch_id', $myBranchId))->where($settledField, false)->where('payment_status', 'Paid')->where('status', '!=', 'Cancelled');
            }])
            ->when($this->searchPartner, function($q) {
                $q->where(fn($q2) => $q2->where('name', 'ilike', "%{$this->searchPartner}%")->orWhere('phone', 'ilike', "%{$this->searchPartner}%"));
            })
            ->orderBy('pending_amount', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(6, ['*'], 'partnersPage');

        // --- Pending Invoices for selected partner ---
        $pendingInvoices = [];
        if ($this->selectedPartnerId) {
            $idField = '';
            if ($this->partnerType === 'Doctor') $idField = 'referred_by_doctor_id';
            elseif ($this->partnerType === 'Agent') $idField = 'referred_by_agent_id';
            elseif ($this->partnerType === 'Collection Center') $idField = 'collection_center_id';

            $valId = ($this->partnerType === 'Collection Center') ? $this->selectedPartner->collection_center_id : $this->selectedPartnerId;

            $pendingInvoices = Invoice::where('company_id', $companyId)
                ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
                ->where('payment_status', 'Paid')
                ->where('status', '!=', 'Cancelled')
                ->where($idField, $valId)
                ->where($settledField, false)
                ->latest()
                ->get();
        }

        // --- Settlement History ---
        $settlements = Settlement::where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->whereHas('user', fn($u) => $u->where('branch_id', $myBranchId)))
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'historyPage');

        return view('livewire.lab.settlement-manager', [
            'partners' => $partners,
            'pendingInvoices' => $pendingInvoices,
            'settlements' => $settlements,
            'stats' => $stats,
            'paymentModes' => PaymentMode::where('company_id', $companyId)->where('is_active', true)->get(),
        ]);
    }
}

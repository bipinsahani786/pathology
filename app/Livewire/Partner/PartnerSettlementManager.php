<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settlement;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PartnerSettlementManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterDateFrom;
    public $filterDateTo;
    public $stats = [];
    
    // Payment Recording Fields
    public $isModalOpen = false;
    public $amount;
    public $payment_date;
    public $payment_mode = 'UPI';
    public $reference_no;
    public $notes;

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['amount', 'reference_no', 'notes']);
        $this->payment_date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function recordPayment()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        $isCC = $user->hasRole('collection_center') || $user->collection_center_id || collect($roles)->contains(fn($r) => str_ends_with($r, '_collection_center'));

        if (!$isCC) {
            abort(403, 'Only Collection Centers can record payments.');
        }

        $this->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'reference_no' => 'nullable|string',
        ]);

        Settlement::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'collection_center_id' => $user->collection_center_id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_mode' => $this->payment_mode,
            'reference_no' => $this->reference_no,
            'type' => 'CollectionCenter',
            'status' => 'Pending', // Requires Lab Admin approval
            'notes' => $this->notes,
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Payment recorded successfully. Waiting for lab approval.');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Settlement::where('user_id', $user->id);

        if ($this->search) {
            $query->where('reference_no', 'like', '%' . $this->search . '%');
        }

        if ($this->filterDateFrom) {
            $query->whereDate('payment_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('payment_date', '<=', $this->filterDateTo);
        }

        // Stats calculation
        $invoices = Invoice::where('status', '!=', 'Cancelled');
        $roles = $user->roles->pluck('name')->toArray();
        $isCC = $user->hasRole('collection_center') || $user->collection_center_id || collect($roles)->contains(fn($r) => str_ends_with($r, '_collection_center'));
        $isDoctor = $user->hasRole('doctor') || $user->doctorProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_doctor'));
        $isAgent = $user->hasRole('agent') || $user->agentProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_agent'));

        if ($isCC) {
            $invoices->where('collection_center_id', $user->collection_center_id);
        } elseif ($isDoctor) {
            $invoices->where('referred_by_doctor_id', $user->id);
        } elseif ($isAgent) {
            $invoices->where('referred_by_agent_id', $user->id);
        }

        if ($isCC) {
            // For CC, dues = total billing minus their profit (i.e. the B2B cost they owe the lab)
            $totalDuesBill = $invoices->sum('total_b2b_amount');
        } elseif ($isDoctor) {
            $totalDuesBill = $invoices->sum('doctor_commission_amount');
        } elseif ($isAgent) {
            $totalDuesBill = $invoices->sum('agent_commission_amount');
        } else {
            $totalDuesBill = $invoices->sum('total_amount');
        }

        $paidApproved = Settlement::where('user_id', $user->id)->where('status', 'Approved')->sum('amount');
        $paidPending = Settlement::where('user_id', $user->id)->where('status', 'Pending')->sum('amount');

        $this->stats = [
            'total_dues' => $totalDuesBill,
            'paid_confirmed' => $paidApproved,
            'balance' => max(0, $totalDuesBill - $paidApproved),
            'awaiting_verification' => $paidPending,
        ];

        return view('livewire.partner.partner-settlement-manager', [
            'settlements' => $query->latest()->paginate($this->perPage)
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
        $this->resetPage();
    }
}

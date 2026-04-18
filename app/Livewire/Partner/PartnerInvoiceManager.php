<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PartnerInvoiceManager extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterDateFrom;
    public $filterDateTo;
    public $filterStatus = '';
    public $role;
    public $stats = [];

    public function mount()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();
        $isCC = $user->hasRole('collection_center') || $user->collection_center_id || collect($roles)->contains(fn($r) => str_ends_with($r, '_collection_center'));
        $isDoctor = $user->hasRole('doctor') || $user->doctorProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_doctor'));
        $isAgent = $user->hasRole('agent') || $user->agentProfile || collect($roles)->contains(fn($r) => str_ends_with($r, '_agent'));

        if ($isDoctor) {
            $this->role = 'Doctor';
        } elseif ($isAgent) {
            $this->role = 'Agent';
        } elseif ($isCC) {
            $this->role = 'Collection Center';
        } else {
            abort(403, 'Unauthorized access: Role not recognized.');
        }

        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Invoice::with(['patient'])->where('status', '!=', 'Cancelled');

        if ($this->role === 'Doctor') {
            $query->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $query->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $query->where('collection_center_id', $user->collection_center_id);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', $searchTerm)
                    ->orWhereHas('patient', function ($pq) use ($searchTerm) {
                        $pq->where('name', 'like', $searchTerm)
                            ->orWhere('phone', 'like', $searchTerm);
                    });
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('invoice_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('invoice_date', '<=', $this->filterDateTo);
        }
        if ($this->filterStatus) {
            $query->where('payment_status', $this->filterStatus);
        }

        // Stats calculation
        $statsQuery = Invoice::where('status', '!=', 'Cancelled');
        if ($this->role === 'Doctor') {
            $statsQuery->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $statsQuery->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $statsQuery->where('collection_center_id', $user->collection_center_id);
        }

        $allInvoices = (clone $statsQuery)->get();
        $this->stats = [
            'total' => $allInvoices->count(),
            'total_amount' => $allInvoices->sum('total_amount'),
            'today_count' => (clone $statsQuery)->whereDate('invoice_date', today())->count(),
            'due' => $allInvoices->sum('due_amount'),
        ];

        if ($this->role === 'Collection Center') {
            $this->stats['total_profit'] = $allInvoices->sum('cc_profit_amount');
            $this->stats['today_profit'] = (clone $statsQuery)->whereDate('invoice_date', today())->sum('cc_profit_amount');
        } elseif ($this->role === 'Doctor') {
            $this->stats['total_profit'] = $allInvoices->sum('doctor_commission_amount');
            $this->stats['today_profit'] = (clone $statsQuery)->whereDate('invoice_date', today())->sum('doctor_commission_amount');
        } elseif ($this->role === 'Agent') {
            $this->stats['total_profit'] = $allInvoices->sum('agent_commission_amount');
            $this->stats['today_profit'] = (clone $statsQuery)->whereDate('invoice_date', today())->sum('agent_commission_amount');
        }

        return view('livewire.partner.partner-invoice-manager', [
            'invoices' => $query->latest()->paginate($this->perPage)
        ]);
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->format('Y-m-d');
        $this->filterStatus = '';
        $this->resetPage();
    }
}

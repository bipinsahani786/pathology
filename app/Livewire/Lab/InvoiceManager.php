<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;

class InvoiceManager extends Component
{
    use WithPagination;

    // Search & Filters
    public $search = '';
    public $filterStatus = '';        // Paid, Unpaid, Partial
    public $filterPaymentStatus = ''; // alias
    public $filterDateFrom = '';
    public $filterDateTo = '';
    public $filterCollectionType = '';
    public $filterCC = '';
    public $filterDoctor = '';
    public $filterAgent = '';
    public $filterInvoiceStatus = ''; // Active, Cancelled
    public $filterSampleStatus = '';  // Pending, Collected, etc.
    public $perPage = 15;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view invoices');
    }

    // Reset pagination when filters change
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterDateFrom()
    {
        $this->resetPage();
    }
    public function updatingFilterDateTo()
    {
        $this->resetPage();
    }
    public function updatingFilterCollectionType()
    {
        $this->resetPage();
    }
    public function updatingFilterCC()
    {
        $this->resetPage();
    }
    public function updatingFilterDoctor()
    {
        $this->resetPage();
    }
    public function updatingFilterAgent()
    {
        $this->resetPage();
    }
    public function updatingFilterInvoiceStatus()
    {
        $this->resetPage();
    }
    public function updatingFilterSampleStatus()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterDateFrom', 'filterDateTo', 'filterCollectionType', 'filterCC', 'filterDoctor', 'filterAgent', 'filterInvoiceStatus', 'filterSampleStatus']);
        $this->resetPage();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $user = auth()->user();
        $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
        $activeBranchId = session('active_branch_id', 'all');
        
        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = $user->hasAnyRole(['lab_admin', 'super_admin']) || 
                         collect($roles)->contains(fn($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin'));
        
        $myBranchId = null;
        if ($isGlobalAdmin) {
             $myBranchId = ($activeBranchId === 'all' ? null : $activeBranchId);
        } else {
             $myBranchId = $user->branch_id;
        }

        // If strict branch access is enabled, force myBranchId if it was null AND user is NOT a global admin
        if ($restrictAccess && !$myBranchId && !$isGlobalAdmin) {
            $myBranchId = $user->branch_id;
        }

        $companyId = $user->company_id;
        $query = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
            ->when($user->collection_center_id, fn($q) => $q->where('collection_center_id', $user->collection_center_id))
            ->with(['patient', 'doctor', 'collectionCenter', 'items', 'creator'])
            ->latest('invoice_date');

        // Search by invoice number, patient name, or phone
        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                    ->orWhere('barcode', 'like', "%{$s}%")
                    ->orWhereHas('patient', function ($pq) use ($s) {
                        $pq->where('name', 'like', "%{$s}%")
                            ->orWhere('phone', 'like', "%{$s}%");
                    });
            });
        }

        // Payment Status filter
        if ($this->filterStatus) {
            $query->where('payment_status', $this->filterStatus);
        }

        // Invoice Status (Active/Cancelled)
        if ($this->filterInvoiceStatus) {
            $query->where('status', $this->filterInvoiceStatus);
        }

        // Sample Status
        if ($this->filterSampleStatus) {
            $query->where('sample_status', $this->filterSampleStatus);
        }

        // Date range
        if ($this->filterDateFrom) {
            $query->whereDate('invoice_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('invoice_date', '<=', $this->filterDateTo);
        }

        // Collection center
        if ($this->filterCC) {
            $query->where('collection_center_id', $this->filterCC);
        }

        // Doctor Filter
        if ($this->filterDoctor) {
            $query->where('referred_by_doctor_id', $this->filterDoctor);
        }

        // Agent Filter
        if ($this->filterAgent) {
            $query->where('referred_by_agent_id', $this->filterAgent);
        }

        $invoices = $query->paginate($this->perPage);

        // Stats calculations with strict scoping
        $statsBase = Invoice::where('company_id', $companyId);
        if ($myBranchId) $statsBase->where('branch_id', $myBranchId);
        if ($user->collection_center_id) $statsBase->where('collection_center_id', $user->collection_center_id);

        $stats = [
            'total' => (clone $statsBase)->count(),
            'today' => (clone $statsBase)->whereDate('invoice_date', today())->count(),
            'paid' => (clone $statsBase)->where('payment_status', 'Paid')->count(),
            'due' => (clone $statsBase)->where('payment_status', '!=', 'Paid')->sum('due_amount'),
            'todayRevenue' => (clone $statsBase)->whereDate('invoice_date', today())->sum('paid_amount'),
            'totalRevenue' => (clone $statsBase)->sum('paid_amount'),
        ];

        // Lookup data filtering
        $ccQuery = \App\Models\CollectionCenter::where('company_id', $companyId);
        if ($restrictAccess && $myBranchId) {
            $ccQuery->where('branch_id', $myBranchId);
        }
        $collectionCenters = $ccQuery->get();

        $doctors = \App\Models\DoctorProfile::where('company_id', $companyId)->with('user')->get();
        $agents = \App\Models\AgentProfile::where('company_id', $companyId)->with('user')->get();

        return view('livewire.lab.invoice-manager', compact('invoices', 'stats', 'collectionCenters', 'doctors', 'agents'))
            ->layout('layouts.app', ['title' => 'Invoices']);
    }

    public function updateSampleStatus($invoiceId, $status)
    {
        $this->authorize('edit invoices');
        $invoice = Invoice::findOrFail($invoiceId);

        $invoice->update([
            'sample_status' => $status,
            'sample_collected_at' => ($status === 'Collected' && !$invoice->sample_collected_at) ? now() : $invoice->sample_collected_at
        ]);

        session()->flash('message', 'Sample status updated to ' . $status);
    }

    public function cancelInvoice($invoiceId)
    {
        try {
            $this->authorize('delete invoices');
            $invoice = Invoice::findOrFail($invoiceId);
            $result = $invoice->cancel();
            
            if ($result['status']) {
                session()->flash('message', $result['message']);
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Throwable $th) {
            session()->flash('error', 'Critical Error: ' . $th->getMessage());
        }
    }
}

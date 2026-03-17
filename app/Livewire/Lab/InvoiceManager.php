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
    public $perPage = 15;

    // Reset pagination when filters change
    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }
    public function updatingFilterDateFrom() { $this->resetPage(); }
    public function updatingFilterDateTo() { $this->resetPage(); }
    public function updatingFilterCollectionType() { $this->resetPage(); }
    public function updatingPerPage() { $this->resetPage(); }

    public function clearFilters()
    {
        $this->reset(['search', 'filterStatus', 'filterDateFrom', 'filterDateTo', 'filterCollectionType']);
        $this->resetPage();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $query = Invoice::where('company_id', $companyId)
            ->with(['patient', 'doctor', 'collectionCenter', 'items', 'creator'])
            ->latest('invoice_date');

        // Search by invoice number, patient name, or phone
        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'ilike', "%{$s}%")
                  ->orWhere('barcode', 'ilike', "%{$s}%")
                  ->orWhereHas('patient', function ($pq) use ($s) {
                      $pq->where('name', 'ilike', "%{$s}%")
                         ->orWhere('phone', 'ilike', "%{$s}%");
                  });
            });
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('payment_status', $this->filterStatus);
        }

        // Date range
        if ($this->filterDateFrom) {
            $query->whereDate('invoice_date', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('invoice_date', '<=', $this->filterDateTo);
        }

        // Collection type
        if ($this->filterCollectionType) {
            $query->where('collection_type', $this->filterCollectionType);
        }

        $invoices = $query->paginate($this->perPage);

        // Stats
        $stats = [
            'total' => Invoice::where('company_id', $companyId)->count(),
            'today' => Invoice::where('company_id', $companyId)->whereDate('invoice_date', today())->count(),
            'paid' => Invoice::where('company_id', $companyId)->where('payment_status', 'Paid')->count(),
            'due' => Invoice::where('company_id', $companyId)->where('payment_status', '!=', 'Paid')->sum('due_amount'),
            'todayRevenue' => Invoice::where('company_id', $companyId)->whereDate('invoice_date', today())->sum('paid_amount'),
        ];

        return view('livewire.lab.invoice-manager', compact('invoices', 'stats'))
            ->layout('layouts.app', ['title' => 'Invoices']);
    }
}

<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\TestReport;
use Carbon\Carbon;

class ReportManager extends Component
{
    use WithPagination;
    
    public $search = '';
    public $dateRange = 'all'; // all, today, week, month
    public $statusFilter = 'all'; // all, pending, draft, approved
    public $perPage = 15;

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $invoicesQuery = Invoice::where('company_id', $companyId)
            ->with(['patient', 'testReport'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($this->search) {
            $invoicesQuery->where(function($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('patient', function($q) {
                      $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                  });
            });
        }

        // Date filter
        if ($this->dateRange !== 'all') {
            switch ($this->dateRange) {
                case 'today':
                    $invoicesQuery->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $invoicesQuery->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'month':
                    $invoicesQuery->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        // Status filter (Custom Logic based on TestReport existence/status)
        if ($this->statusFilter !== 'all') {
            if ($this->statusFilter === 'pending') {
                $invoicesQuery->doesntHave('testReport');
            } elseif ($this->statusFilter === 'draft') {
                $invoicesQuery->whereHas('testReport', function($q) {
                    $q->where('status', 'Draft');
                });
            } elseif ($this->statusFilter === 'approved') {
                $invoicesQuery->whereHas('testReport', function($q) {
                    $q->where('status', 'Approved');
                });
            }
        }

        return view('livewire.lab.report-manager', [
            'invoices' => $invoicesQuery->paginate($this->perPage),
        ])->layout('layouts.app');
    }
}

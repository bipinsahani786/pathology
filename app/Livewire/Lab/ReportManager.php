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
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->authorize('view reports');
    }
    public $dateRange = 'all'; // all, today, week, month, custom
    public $statusFilter = 'all'; // all, pending, draft, approved
    public $perPage = 15;

    // New Filters
    public $filterDoctor = '';
    public $filterAgent = '';
    public $filterCC = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Selective Printing
    public $selectedTests = []; // Array of invoice_item_ids

    public function updatingSearch()
    {
        $this->resetPage();
    }
    
    public function updatingStatusFilter()
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

    public function updatingFilterCC()
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
    
    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function printSelected($invoiceId)
    {
        if (empty($this->selectedTests)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please select at least one test to print.']);
            return;
        }

        $testIds = implode(',', $this->selectedTests);
        $url = route('lab.reports.print', ['id' => $invoiceId, 'template' => 'modern']) . '?tests=' . $testIds;
        
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;

        $invoicesQuery = Invoice::where('company_id', $companyId)
            ->with([
                'patient.patientProfile', 
                'testReport.results', 
                'items.labTest',
                'doctor',
                'agent',
                'collectionCenter'
            ])
            ->orderBy('created_at', 'desc');

        // Search
        if ($this->search) {
            $invoicesQuery->where(function($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%")
                  ->orWhereHas('patient', function($q) {
                      $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                  });
            });
        }

        // Doctor Filter
        if ($this->filterDoctor) {
            $invoicesQuery->where('referred_by_doctor_id', $this->filterDoctor);
        }

        // Agent Filter
        if ($this->filterAgent) {
            $invoicesQuery->where('referred_by_agent_id', $this->filterAgent);
        }

        // Collection Center Filter
        if ($this->filterCC) {
            $invoicesQuery->where('collection_center_id', $this->filterCC);
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
                    $invoicesQuery->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'custom':
                    if ($this->filterDateFrom && $this->filterDateTo) {
                        $invoicesQuery->whereBetween('created_at', [
                            Carbon::parse($this->filterDateFrom)->startOfDay(),
                            Carbon::parse($this->filterDateTo)->endOfDay()
                        ]);
                    }
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

        // Dropdown lists
        $doctors = \App\Models\DoctorProfile::with('user:id,name')->get();
        $agents = \App\Models\AgentProfile::with('user:id,name')->get();
        $centers = \App\Models\CollectionCenter::all();

        return view('livewire.lab.report-manager', [
            'invoices' => $invoicesQuery->paginate($this->perPage),
            'doctors' => $doctors,
            'agents' => $agents,
            'centers' => $centers,
        ])->layout('layouts.app');
    }
}

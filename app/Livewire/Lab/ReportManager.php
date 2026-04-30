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

    public function printSelected($invoiceId, $withHeader = 1)
    {
        if (empty($this->selectedTests)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Please select at least one test to print.']);
            return;
        }

        // Printing proceeds regardless of image presence to allow for physical letterhead space
        $testIds = implode(',', $this->selectedTests);
        $url = route('lab.reports.print', ['id' => $invoiceId, 'template' => 'new'])
             . '?tests=' . $testIds
             . '&header=' . ($withHeader ? '1' : '0');
        
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function printCompleted($invoiceId, $withHeader = 1)
    {
        $invoice = Invoice::with('items')->find($invoiceId);
        $completedItemIds = $invoice->items->where('status', 'Completed')->pluck('id')->toArray();

        if (empty($completedItemIds)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No completed tests found to print.']);
            return;
        }

        $testIds = implode(',', $completedItemIds);
        $url = route('lab.reports.print', ['id' => $invoiceId, 'template' => 'new'])
             . '?tests=' . $testIds
             . '&header=' . ($withHeader ? '1' : '0');
        
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function render()
    {
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
        $invoicesQuery = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn($q) => $q->where('branch_id', $myBranchId))
            ->when($user->collection_center_id, fn($q) => $q->where('collection_center_id', $user->collection_center_id))
            ->when(!$isGlobalAdmin && ($user->hasRole('doctor') || $user->doctorProfile), fn($q) => $q->where('referred_by_doctor_id', $user->id))
            ->when(!$isGlobalAdmin && ($user->hasRole('agent') || $user->agentProfile), fn($q) => $q->where('referred_by_agent_id', $user->id))
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

        // Dropdown lists filtering
        $doctorsQuery = \App\Models\DoctorProfile::where('company_id', $companyId)->with('user:id,name');
        $agentsQuery = \App\Models\AgentProfile::where('company_id', $companyId)->with('user:id,name');
        $centersQuery = \App\Models\CollectionCenter::where('company_id', $companyId);

        if ($restrictAccess && $myBranchId) {
            $centersQuery->where('branch_id', $myBranchId);
            // Note: Doctors and Agents are currently company-wide unless specifically filtered by branch association
        }

        $doctors = $doctorsQuery->get();
        $agents = $agentsQuery->get();
        $centers = $centersQuery->get();

        return view('livewire.lab.report-manager', [
            'invoices' => $invoicesQuery->paginate($this->perPage),
            'doctors' => $doctors,
            'agents' => $agents,
            'centers' => $centers,
        ])->layout('layouts.app');
    }

    public function printReport($invoiceId, $withHeader)
    {
        if ($withHeader) {
            $header = \App\Models\Configuration::getFor('pdf_header_image');
            if (!$header) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Please upload your Letterhead (Header) in Settings before printing with header.']);
                return;
            }
        }
        
        $url = route('lab.reports.print', [$invoiceId, 'new']) . '?header=' . ($withHeader ? '1' : '0');
        $this->dispatch('open-new-tab', ['url' => $url]);
    }
}

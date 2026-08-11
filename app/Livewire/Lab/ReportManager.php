<?php

namespace App\Livewire\Lab;

use App\Models\Invoice;
use App\Models\TestReport;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ReportManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $user = auth()->user();
        
        // Direct DB check to avoid stale Spatie permission cache issues with custom roles.
        // This checks if any of the user's roles have any report-related permission.
        $hasReportAccess = $user->hasAnyPermission(['view reports', 'create reports', 'edit reports', 'generate reports']);
        
        if (!$hasReportAccess) {
            // Fallback: Check directly in DB in case Spatie cache is stale (e.g. Redis cache not cleared after role changes)
            $userRoleIds = $user->roles->pluck('id')->toArray();
            $hasReportAccess = \DB::table('role_has_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                ->whereIn('role_has_permissions.role_id', $userRoleIds)
                ->where(function ($q) {
                    $q->where('permissions.name', 'like', '%reports%');
                })
                ->exists();
        }
        
        if (!$hasReportAccess) {
            abort(403, 'You do not have permission to access reports.');
        }
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

    // Outsourced Report
    public $isOutsourcedModalOpen = false;
    public $outsourcedInvoiceId = null;
    public $outsourcedPdf; // For the file upload
    public $outsourcedPdfMode = 'crop_to_image';
    public $outsourcedCropTop = 18;
    public $outsourcedCropBottom = 5;
    public $uploadedOutsourcedPdfs = [];

    // Reorder Modal
    public $isReorderModalOpen = false;
    public $reorderInvoiceId = null;
    public $reorderItems = [];

    public function openReorderModal($invoiceId)
    {
        $this->authorize('edit reports');
        $this->reorderInvoiceId = $invoiceId;
        $this->loadReorderItems();
        $this->isReorderModalOpen = true;
    }

    public function closeReorderModal()
    {
        $this->isReorderModalOpen = false;
        $this->reorderInvoiceId = null;
        $this->reorderItems = [];
    }

    private function loadReorderItems()
    {
        if ($this->reorderInvoiceId) {
            $items = \App\Models\InvoiceItem::with('labTest')
                ->where('invoice_id', $this->reorderInvoiceId)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'asc')
                ->get();
            $this->reorderItems = $items->toArray();
        }
    }

    public function moveReorderItemUp($index)
    {
        $this->authorize('edit reports');
        if ($index > 0) {
            $temp = $this->reorderItems[$index - 1];
            $this->reorderItems[$index - 1] = $this->reorderItems[$index];
            $this->reorderItems[$index] = $temp;
            $this->saveReorderItems();
        }
    }

    public function moveReorderItemDown($index)
    {
        $this->authorize('edit reports');
        if ($index < count($this->reorderItems) - 1) {
            $temp = $this->reorderItems[$index + 1];
            $this->reorderItems[$index + 1] = $this->reorderItems[$index];
            $this->reorderItems[$index] = $temp;
            $this->saveReorderItems();
        }
    }

    private function saveReorderItems()
    {
        foreach ($this->reorderItems as $idx => $itemData) {
            \App\Models\InvoiceItem::where('id', $itemData['id'])->update(['sort_order' => $idx]);
            $this->reorderItems[$idx]['sort_order'] = $idx;
        }
    }

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
             .'?tests='.$testIds
             .'&header='.($withHeader ? '1' : '0');

        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function printCompleted($invoiceId, $withHeader = 1)
    {
        $items = \App\Models\InvoiceItem::where('invoice_id', $invoiceId)
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('id', 'asc')
                    ->get();
        $completedTestIds = [];
        
        foreach ($items as $item) {
            if ($item->labTest && $item->labTest->is_package) {
                // For packages, find inner tests that have actual results
                $innerTestIds = \App\Models\ReportResult::where('invoice_item_id', $item->id)
                    ->whereNotNull('result_value')
                    ->where('result_value', '!=', '')
                    ->pluck('lab_test_id')
                    ->unique();
                    
                foreach ($innerTestIds as $innerId) {
                    $completedTestIds[] = $item->id . '_' . $innerId;
                }
            } else {
                if ($item->status === 'Completed') {
                    $completedTestIds[] = $item->id;
                }
            }
        }
        
        $testIds = implode(',', $completedTestIds);

        if (empty($testIds)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No completed tests found in this invoice.']);

            return;
        }

        $url = route('lab.reports.print', ['id' => $invoiceId, 'template' => 'new'])
             .'?tests='.$testIds
             .'&header='.($withHeader ? '1' : '0');

        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public $hasExistingOutsourcedPdf = false;

    public function openOutsourcedModal($invoiceId)
    {
        $this->outsourcedInvoiceId = $invoiceId;
        $this->outsourcedPdf = null;
        
        $invoice = Invoice::find($invoiceId);
        $testReport = $invoice->testReport;
        
        $this->outsourcedPdfMode = \App\Models\Configuration::getFor('outsourced_pdf_mode', 'crop_to_image', $invoice->company_id, $invoice->branch_id);
        
        if ($testReport && !empty($testReport->outsourced_pdf_path)) {
            $this->outsourcedCropTop = $testReport->outsourced_crop_top ?? (int) \App\Models\Configuration::getFor('outsourced_crop_top', 18);
            $this->outsourcedCropBottom = $testReport->outsourced_crop_bottom ?? (int) \App\Models\Configuration::getFor('outsourced_crop_bottom', 8);
            $this->uploadedOutsourcedPdfs = is_array($testReport->outsourced_pdf_path) ? $testReport->outsourced_pdf_path : [$testReport->outsourced_pdf_path];
            $this->hasExistingOutsourcedPdf = count($this->uploadedOutsourcedPdfs) > 0;
        } else {
            $this->outsourcedCropTop = (int) \App\Models\Configuration::getFor('outsourced_crop_top', 18);
            $this->outsourcedCropBottom = (int) \App\Models\Configuration::getFor('outsourced_crop_bottom', 8);
            $this->uploadedOutsourcedPdfs = [];
            $this->hasExistingOutsourcedPdf = false;
        }
        
        $this->isOutsourcedModalOpen = true;
    }

    public function closeOutsourcedModal()
    {
        $this->isOutsourcedModalOpen = false;
        $this->outsourcedInvoiceId = null;
        $this->outsourcedPdf = null;
        $this->uploadedOutsourcedPdfs = [];
        $this->hasExistingOutsourcedPdf = false;
    }

    public function saveOutsourcedReport()
    {
        $this->authorize('edit reports');
        
        $rules = [
            'outsourcedPdf' => 'required|file|mimes:pdf|max:10240',
        ];
        
        if ($this->hasExistingOutsourcedPdf) {
            $rules['outsourcedPdf'] = 'nullable|file|mimes:pdf|max:10240';
        }

        $this->validate($rules);

        $invoice = Invoice::findOrFail($this->outsourcedInvoiceId);
        $testReport = $invoice->testReport;
        
        $paths = [];
        if ($testReport && !empty($testReport->outsourced_pdf_path)) {
            $paths = is_array($testReport->outsourced_pdf_path) ? $testReport->outsourced_pdf_path : [$testReport->outsourced_pdf_path];
        }
        
        if ($this->outsourcedPdf) {
            $newPath = app(\App\Services\OutsourcedReportService::class)->storePdf($this->outsourcedPdf);
            $paths[] = $newPath;
        }

        // Find or create TestReport
        $testReport = $invoice->testReport;
        if (!$testReport) {
            $testReport = TestReport::create([
                'company_id' => $invoice->company_id,
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'status' => 'Approved', // Mark approved directly
                'report_date' => now(),
            ]);
        } else {
            $testReport->update(['status' => 'Approved']);
        }

        $testReport->update([
            'outsourced_pdf_path' => $paths,
            'outsourced_crop_top' => $this->outsourcedCropTop,
            'outsourced_crop_bottom' => $this->outsourcedCropBottom,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Mark all invoice items as completed
        foreach ($invoice->items as $item) {
            if ($item->lab_test_id) {
                $item->update(['status' => 'Completed']);
            }
        }

        $this->closeOutsourcedModal();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Outsourced report generated successfully.']);

        // Auto download/print the generated report
        $this->printReport($invoice->id, 1);
    }

    public function deleteOutsourcedPdf($index)
    {
        $this->authorize('edit reports');
        
        $invoice = Invoice::findOrFail($this->outsourcedInvoiceId);
        $testReport = $invoice->testReport;
        
        if ($testReport && !empty($testReport->outsourced_pdf_path)) {
            $paths = is_array($testReport->outsourced_pdf_path) ? $testReport->outsourced_pdf_path : [$testReport->outsourced_pdf_path];
            if (isset($paths[$index])) {
                $pathToDelete = $paths[$index];
                if (\Illuminate\Support\Facades\Storage::exists($pathToDelete)) {
                    \Illuminate\Support\Facades\Storage::delete($pathToDelete);
                }
                unset($paths[$index]);
                $paths = array_values($paths); // Reindex array
                
                $testReport->update(['outsourced_pdf_path' => $paths]);
                $this->uploadedOutsourcedPdfs = $paths;
                $this->hasExistingOutsourcedPdf = count($paths) > 0;
                
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Report deleted successfully.']);
            }
        }
    }

    public function render()
    {
        $user = auth()->user();
        $restrictAccess = \App\Models\Configuration::getFor('restrict_branch_access', '1') === '1';
        $activeBranchId = session('active_branch_id', 'all');

        $roles = $user->roles->pluck('name')->toArray();
        $isGlobalAdmin = ($user->hasAnyRole(['lab_admin', 'super_admin']) ||
                         collect($roles)->contains(fn ($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin')))
                         && ! $user->hasRole('branch_admin');

        $myBranchId = null;
        if ($isGlobalAdmin) {
            $myBranchId = ($activeBranchId === 'all' ? null : $activeBranchId);
        } else {
            $myBranchId = $user->branch_id;
        }

        // If strict branch access is enabled, force myBranchId if it was null AND user is NOT a global admin
        if ($restrictAccess && ! $myBranchId && ! $isGlobalAdmin) {
            $myBranchId = $user->branch_id;
        }

        $companyId = $user->company_id;
        $invoicesQuery = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn ($q) => $q->where('branch_id', $myBranchId))
            ->when($user->collection_center_id, fn ($q) => $q->where('collection_center_id', $user->collection_center_id))
            ->when(! $isGlobalAdmin && ($user->hasRole('doctor') || $user->doctorProfile), fn ($q) => $q->where('referred_by_doctor_id', $user->id))
            ->when(! $isGlobalAdmin && ($user->hasRole('agent') || $user->agentProfile), fn ($q) => $q->where('referred_by_agent_id', $user->id))
            ->with([
                'patient.patientProfile',
                'testReport.results',
                'items.labTest',
                'doctor',
                'agent',
                'collectionCenter',
            ])
            ->orderBy('created_at', 'desc');

        // Search
        if ($this->search) {
            $invoicesQuery->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                    ->orWhere('barcode', 'like', "%{$this->search}%")
                    ->orWhereHas('patient', function ($q) {
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
                            Carbon::parse($this->filterDateTo)->endOfDay(),
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
                $invoicesQuery->whereHas('testReport', function ($q) {
                    $q->where('status', 'Draft');
                });
            } elseif ($this->statusFilter === 'approved') {
                $invoicesQuery->whereHas('testReport', function ($q) {
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
            $mode = \App\Models\Configuration::getFor('pdf_letterhead_mode', 'separate');
            $hasImage = false;
            
            if ($mode === 'full_background') {
                $hasImage = (bool) \App\Models\Configuration::getFor('pdf_letterhead_image');
            } else {
                $hasImage = (bool) \App\Models\Configuration::getFor('pdf_header_image');
            }

            if (! $hasImage) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Please upload your Letterhead/Header in Settings before printing with header.']);

                return;
            }
        }

        $url = route('lab.reports.print', [$invoiceId, 'new']).'?header='.($withHeader ? '1' : '0').'&t='.time();
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function notifyMissingPhone()
    {
        $this->dispatch('notify', ['type' => 'error', 'message' => 'WhatsApp cannot be shared because the patient phone number is missing.']);
    }
}

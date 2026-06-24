<?php

namespace App\Livewire\Lab;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

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

    public $isExportModalOpen = false;

    public $exportColumns = [
        'invoice_number' => true,
        'date' => true,
        'patient_name' => true,
        'patient_phone' => true,
        'total_amount' => true,
        'paid_amount' => true,
        'due_amount' => true,
        'payment_status' => true,
        'collection_center' => true,
    ];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if (! auth()->user()->can('view invoices') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
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

    protected function buildQuery()
    {
        $companyId = auth()->user()->company_id;
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

        if ($restrictAccess && ! $myBranchId && ! $isGlobalAdmin) {
            $myBranchId = $user->branch_id;
        }

        $query = Invoice::where('company_id', $companyId)
            ->when($myBranchId, fn ($q) => $q->where('branch_id', $myBranchId))
            ->when($user->collection_center_id, fn ($q) => $q->where('collection_center_id', $user->collection_center_id))
            ->with(['patient', 'doctor', 'collectionCenter', 'items', 'creator'])
            ->latest('invoice_date');

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

        if ($this->filterStatus) {
            if ($this->filterStatus === 'HasDues') {
                $query->where('due_amount', '>', 0);
            } else {
                $query->where('payment_status', $this->filterStatus);
            }
        }

        if ($this->filterInvoiceStatus) {
            if ($this->filterInvoiceStatus === 'Active') {
                $query->where('status', '!=', 'Cancelled');
            } else {
                $query->where('status', $this->filterInvoiceStatus);
            }
        }

        if ($this->filterSampleStatus) {
            $query->where('sample_status', $this->filterSampleStatus);
        }

        if ($this->filterDateFrom) {
            $query->where('invoice_date', '>=', \Carbon\Carbon::parse($this->filterDateFrom));
        }
        if ($this->filterDateTo) {
            $query->where('invoice_date', '<=', \Carbon\Carbon::parse($this->filterDateTo));
        }

        if ($this->filterCC) {
            $query->where('collection_center_id', $this->filterCC);
        }

        if ($this->filterDoctor) {
            $query->where('referred_by_doctor_id', $this->filterDoctor);
        }

        if ($this->filterAgent) {
            $query->where('referred_by_agent_id', $this->filterAgent);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildQuery();
        $invoices = $query->paginate($this->perPage);

        $companyId = auth()->user()->company_id;
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

        if ($restrictAccess && ! $myBranchId && ! $isGlobalAdmin) {
            $myBranchId = $user->branch_id;
        }

        // Stats calculations with strict scoping
        $statsBase = Invoice::where('company_id', $companyId)->where('status', '!=', 'Cancelled');
        if ($myBranchId) {
            $statsBase->where('branch_id', $myBranchId);
        }
        if ($user->collection_center_id) {
            $statsBase->where('collection_center_id', $user->collection_center_id);
        }

        $todayRevenue = \App\Models\Payment::whereHas('invoice', function ($q) use ($companyId, $myBranchId, $user) {
            $q->where('company_id', $companyId)
                ->where('status', '!=', 'Cancelled')
                ->when($myBranchId, fn ($sub) => $sub->where('branch_id', $myBranchId))
                ->when($user->collection_center_id, fn ($sub) => $sub->where('collection_center_id', $user->collection_center_id));
        })
        ->whereDate('created_at', today())
        ->sum('amount');

        $stats = [
            'total' => (clone $statsBase)->count(),
            'today' => (clone $statsBase)->whereDate('invoice_date', today())->count(),
            'paid' => (clone $statsBase)->where('payment_status', 'Paid')->count(),
            'due' => (clone $statsBase)->where('payment_status', '!=', 'Paid')->sum('due_amount'),
            'todayRevenue' => $todayRevenue,
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
        if (! auth()->user()->can('edit invoices') && ! auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }
        $invoice = Invoice::findOrFail($invoiceId);
        if (auth()->user()->collection_center_id && $invoice->collection_center_id !== auth()->user()->collection_center_id) {
            abort(403, 'Unauthorized.');
        }

        $invoice->update([
            'sample_status' => $status,
            'sample_collected_at' => ($status === 'Collected' && ! $invoice->sample_collected_at) ? now() : $invoice->sample_collected_at,
        ]);

        session()->flash('message', 'Sample status updated to '.$status);
    }

    public function cancelInvoice($invoiceId)
    {
        try {
            if (! auth()->user()->can('delete invoices') && ! auth()->user()->collection_center_id) {
                abort(403, 'Unauthorized.');
            }
            $invoice = Invoice::findOrFail($invoiceId);
            if (auth()->user()->collection_center_id && $invoice->collection_center_id !== auth()->user()->collection_center_id) {
                abort(403, 'Unauthorized.');
            }
            $result = $invoice->cancel();

            if ($result['status']) {
                session()->flash('message', $result['message']);
                \App\Livewire\Lab\Dashboard::flushCache();
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Throwable $th) {
            session()->flash('error', 'Critical Error: '.$th->getMessage());
        }
    }

    public function printInvoice($id, $withHeader)
    {
        if ($withHeader) {
            $template = \App\Models\Configuration::getFor('bill_template', 'classic');
            if (!in_array($template, ['halfpage', 'thermal'])) {
                $header = \App\Models\Configuration::getFor('pdf_header_image');
                if (! $header) {
                    $this->dispatch('notify', ['type' => 'error', 'message' => 'Please upload your Letterhead (Header) in Settings before printing with header.']);

                    return;
                }
            }
        }

        $route = $withHeader ? 'lab.invoice.pdf' : 'lab.invoice.pdf.plain';
        $url = route($route, $id);
        $this->dispatch('open-new-tab', ['url' => $url]);
    }

    public function openExportModal()
    {
        $this->isExportModalOpen = true;
    }

    public function exportExcel()
    {
        $this->isExportModalOpen = false;
        $invoices = $this->buildQuery()->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=invoices_report_" . date('Y-m-d_His') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = $this->exportColumns;

        $callback = function () use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            
            // Generate headers
            $rowHeader = [];
            if ($columns['invoice_number'] ?? false) $rowHeader[] = 'Invoice #';
            if ($columns['date'] ?? false) $rowHeader[] = 'Date';
            if ($columns['patient_name'] ?? false) $rowHeader[] = 'Patient Name';
            if ($columns['patient_phone'] ?? false) $rowHeader[] = 'Phone';
            if ($columns['total_amount'] ?? false) $rowHeader[] = 'Total Amount';
            if ($columns['paid_amount'] ?? false) $rowHeader[] = 'Paid Amount';
            if ($columns['due_amount'] ?? false) $rowHeader[] = 'Pending Amount';
            if ($columns['payment_status'] ?? false) $rowHeader[] = 'Status';
            if ($columns['collection_center'] ?? false) $rowHeader[] = 'Center/Branch';
            
            fputcsv($file, $rowHeader);

            foreach ($invoices as $inv) {
                $row = [];
                if ($columns['invoice_number'] ?? false) $row[] = $inv->invoice_number;
                if ($columns['date'] ?? false) $row[] = $inv->invoice_date->format('Y-m-d H:i');
                if ($columns['patient_name'] ?? false) $row[] = $inv->patient->name ?? '';
                if ($columns['patient_phone'] ?? false) $row[] = $inv->patient->phone ?? '';
                if ($columns['total_amount'] ?? false) $row[] = $inv->total_amount;
                if ($columns['paid_amount'] ?? false) $row[] = $inv->paid_amount;
                if ($columns['due_amount'] ?? false) $row[] = $inv->due_amount;
                if ($columns['payment_status'] ?? false) $row[] = $inv->payment_status;
                if ($columns['collection_center'] ?? false) $row[] = $inv->collectionCenter->name ?? ($inv->branch->name ?? '');
                
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $this->isExportModalOpen = false;
        $invoices = $this->buildQuery()->get();
        $columns = $this->exportColumns;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.invoices-pdf', compact('invoices', 'columns'))->setPaper('a4', 'landscape');
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'invoices_report_' . date('Y-m-d_His') . '.pdf');
    }
}

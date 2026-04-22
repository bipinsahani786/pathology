<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogManager extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $eventFilter = '';
    public $companyFilter = '';
    
    protected $paginationTheme = 'bootstrap';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AuditLog::with(['user', 'company']);

        if ($this->companyFilter) {
            $query->where('company_id', $this->companyFilter);
        }

        if ($this->eventFilter) {
            $query->where('event', $this->eventFilter);
        }

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->whereHas('user', function($u) {
                    $u->where('name', 'like', '%' . $this->searchTerm . '%');
                })->orWhere('auditable_type', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('event', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $logs = $query->latest()->paginate(50);
        $companies = Company::all();

        return view('livewire.admin.audit-log-manager', [
            'logs' => $logs,
            'companies' => $companies
        ])->layout('layouts.app', ['title' => 'Global Audit Logs']);
    }
}

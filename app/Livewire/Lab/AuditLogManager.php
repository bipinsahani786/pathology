<?php

namespace App\Livewire\Lab;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogManager extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $eventFilter = '';
    
    protected $paginationTheme = 'bootstrap';

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        
        $query = AuditLog::with(['user', 'company'])
            ->where('company_id', $companyId);

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

        $logs = $query->latest()->paginate(20);

        return view('livewire.lab.audit-log-manager', [
            'logs' => $logs
        ])->layout('layouts.app', ['title' => 'Audit Logs']);
    }
}

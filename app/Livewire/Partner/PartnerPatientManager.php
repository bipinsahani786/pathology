<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PartnerPatientManager extends Component
{
    use WithPagination;

    public $search = '';
    public $dateRange = 'all'; // all, today, week, month
    public $role;

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('doctor')) {
            $this->role = 'Doctor';
        } elseif ($user->hasRole('agent')) {
            $this->role = 'Agent';
        } elseif ($user->hasRole('collection_center')) {
            $this->role = 'Collection Center';
        } else {
            abort(403);
        }
    }

    public function render()
    {
        $user = Auth::user();
        $query = Invoice::with(['patient', 'items.labTest', 'testReport'])
            ->where('status', '!=', 'Cancelled');

        if ($this->role === 'Doctor') {
            $query->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $query->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $query->where('collection_center_id', $user->collection_center_id);
        }

        if ($this->search) {
            $query->whereHas('patient', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            })->orWhere('invoice_number', 'like', '%' . $this->search . '%');
        }

        if ($this->dateRange !== 'all') {
            if ($this->dateRange === 'today') {
                $query->whereDate('invoice_date', today());
            } elseif ($this->dateRange === 'week') {
                $query->whereBetween('invoice_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->dateRange === 'month') {
                $query->whereMonth('invoice_date', now()->month)
                      ->whereYear('invoice_date', now()->year);
            }
        }
 
        return view('livewire.partner.partner-patient-manager', [
            'invoices' => $query->latest()->paginate(10)
        ]);
    }

    public function updateSampleStatus($invoiceId, $status)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        
        // Security check: ensure invoice belongs to this partner
        $user = Auth::user();
        if ($this->role === 'Collection Center' && $invoice->collection_center_id != $user->collection_center_id) {
            session()->flash('error', 'Unauthorized access.');
            return;
        }

        $invoice->update([
            'sample_status' => $status,
            'sample_collected_at' => ($status === 'Collected' && !$invoice->sample_collected_at) ? now() : $invoice->sample_collected_at
        ]);

        session()->flash('message', 'Sample status updated to ' . $status);
    }
}

<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PartnerInvoiceManager extends Component
{
    use WithPagination;

    public $search = '';
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
        $query = Invoice::with(['patient'])->where('status', '!=', 'Cancelled');

        if ($this->role === 'Doctor') {
            $query->where('referred_by_doctor_id', $user->id);
        } elseif ($this->role === 'Agent') {
            $query->where('referred_by_agent_id', $user->id);
        } elseif ($this->role === 'Collection Center') {
            $query->where('collection_center_id', $user->collection_center_id);
        }

        if ($this->search) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('patient', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        return view('livewire.partner.partner-invoice-manager', [
            'invoices' => $query->latest()->paginate(10)
        ]);
    }
}

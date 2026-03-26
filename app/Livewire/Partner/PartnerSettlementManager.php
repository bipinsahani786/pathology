<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Settlement;
use Illuminate\Support\Facades\Auth;

class PartnerSettlementManager extends Component
{
    use WithPagination;

    public $dateRange = 'all';
    
    // Payment Recording Fields
    public $isModalOpen = false;
    public $amount;
    public $payment_date;
    public $payment_mode = 'UPI';
    public $reference_no;
    public $notes;

    public function mount()
    {
        $this->payment_date = date('Y-m-d');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['amount', 'reference_no', 'notes']);
        $this->payment_date = date('Y-m-d');
        $this->isModalOpen = true;
    }

    public function recordPayment()
    {
        $user = Auth::user();
        if (!$user->hasRole('collection_center')) {
            abort(403);
        }

        $this->validate([
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_mode' => 'required|string',
            'reference_no' => 'nullable|string',
        ]);

        Settlement::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'collection_center_id' => $user->collection_center_id,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_mode' => $this->payment_mode,
            'reference_no' => $this->reference_no,
            'type' => 'CollectionCenter',
            'status' => 'Pending', // Requires Lab Admin approval
            'notes' => $this->notes,
        ]);

        $this->isModalOpen = false;
        session()->flash('message', 'Payment recorded successfully. Waiting for lab approval.');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Settlement::where('user_id', $user->id);

        if ($this->dateRange !== 'all') {
            if ($this->dateRange === 'today') {
                $query->whereDate('payment_date', today());
            } elseif ($this->dateRange === 'week') {
                $query->whereBetween('payment_date', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->dateRange === 'month') {
                $query->whereMonth('payment_date', now()->month)
                      ->whereYear('payment_date', now()->year);
            }
        }

        return view('livewire.partner.partner-settlement-manager', [
            'settlements' => $query->latest()->paginate(10)
        ]);
    }
}

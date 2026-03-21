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

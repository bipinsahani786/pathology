<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use App\Models\{Invoice, Settlement, User};
use Illuminate\Support\Facades\Auth;

class PartnerDashboard extends Component
{
    public $role;
    public $stats = [
        'total_earnings' => 0,
        'settled_amount' => 0,
        'pending_balance' => 0,
        'total_invoices' => 0,
    ];

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('doctor')) {
            $this->role = 'Doctor';
            $this->loadDoctorStats($user->id);
        } elseif ($user->hasRole('agent')) {
            $this->role = 'Agent';
            $this->loadAgentStats($user->id);
        } elseif ($user->hasRole('collection_center')) {
            $this->role = 'Collection Center';
            $this->loadCollectionCenterStats($user->id);
        } else {
            return redirect()->route('lab.dashboard');
        }
    }

    private function loadDoctorStats($userId)
    {
        $invoices = Invoice::where('referred_by_doctor_id', $userId)->where('status', '!=', 'Cancelled')->get();
        
        $this->stats['total_earnings'] = $invoices->sum('doctor_commission_amount');
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->sum('amount');
        $this->stats['pending_balance'] = $this->stats['total_earnings'] - $this->stats['settled_amount'];
        $this->stats['total_invoices'] = $invoices->count();
    }

    private function loadAgentStats($userId)
    {
        $invoices = Invoice::where('referred_by_agent_id', $userId)->where('status', '!=', 'Cancelled')->get();
        
        $this->stats['total_earnings'] = $invoices->sum('agent_commission_amount');
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->sum('amount');
        $this->stats['pending_balance'] = $this->stats['total_earnings'] - $this->stats['settled_amount'];
        $this->stats['total_invoices'] = $invoices->count();
    }

    private function loadCollectionCenterStats($userId)
    {
        $user = Auth::user();
        $ccId = $user->collection_center_id;
        
        if (!$ccId) return;

        $invoices = Invoice::where('collection_center_id', $ccId)->where('status', '!=', 'Cancelled')->get();
        
        // For CC, we might need a specific commission logic if it's a franchise
        // For now, let's show total collection and settlement if handled via Settlements table
        $this->stats['total_earnings'] = $invoices->sum('total_amount'); // Total business
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->sum('amount');
        $this->stats['pending_balance'] = $this->stats['total_earnings'] - $this->stats['settled_amount'];
        $this->stats['total_invoices'] = $invoices->count();
    }

    public function render()
    {
        $user = Auth::user();
        $recentInvoices = [];
        $recentSettlements = [];

        if ($this->role === 'Doctor') {
            $recentInvoices = Invoice::where('referred_by_doctor_id', $user->id)->latest()->take(10)->get();
        } elseif ($this->role === 'Agent') {
            $recentInvoices = Invoice::where('referred_by_agent_id', $user->id)->latest()->take(10)->get();
        } elseif ($this->role === 'Collection Center') {
            $recentInvoices = Invoice::where('collection_center_id', $user->collection_center_id)->latest()->take(10)->get();
        }

        $recentSettlements = Settlement::where('user_id', $user->id)->latest()->take(5)->get();

        return view('livewire.partner.partner-dashboard', [
            'recentInvoices' => $recentInvoices,
            'recentSettlements' => $recentSettlements,
        ]);
    }
}

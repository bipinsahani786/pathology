<?php

namespace App\Livewire\Partner;

use Livewire\Component;
use App\Models\{Invoice, Settlement, User};
use Illuminate\Support\Facades\Auth;

class PartnerDashboard extends Component
{
    public $role;
    public $startDate, $endDate;
    public $stats = [
        'reports_ready' => 0,
        'pending_collection' => 0,
        'total_profit' => 0,
        'lab_dues' => 0,
        'total_billing' => 0,
        'pending_balance' => 0,
        'total_earnings' => 0,
    ];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadData();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate'])) {
            $this->loadData();
            $this->dispatch('chartDataUpdated', $this->getChartData());
        }
    }

    public function loadData()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        $isCC = $user->hasRole('collection_center') || $user->collection_center_id || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'collection'));
        $isDoctor = $user->hasRole('doctor') || $user->doctorProfile || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'doctor'));
        $isAgent = $user->hasRole('agent') || $user->agentProfile || collect($roles)->contains(fn($r) => str_contains(strtolower($r), 'agent'));

        if ($isDoctor) {
            $this->role = 'Doctor';
            $this->loadDoctorStats($user->id);
        } elseif ($isAgent) {
            $this->role = 'Agent';
            $this->loadAgentStats($user->id);
        } elseif ($isCC) {
            $this->role = 'Collection Center';
            $this->loadCollectionCenterStats($user->id);
        } else {
            // Only redirect to lab dashboard if they are actually lab staff
            if (collect($roles)->contains(fn($r) => in_array($r, ['lab_admin', 'staff', 'branch_admin']) || str_ends_with($r, '_admin') || str_ends_with($r, '_staff'))) {
                return redirect()->route('lab.dashboard');
            }
            abort(403, 'Unauthorized access to the Partner Portal.');
        }
    }

    private function loadDoctorStats($userId)
    {
        $query = Invoice::where('referred_by_doctor_id', $userId)->where('status', '!=', 'Cancelled');
        $rangeInvoices = (clone $query)->whereBetween('invoice_date', [$this->startDate, $this->endDate])->get();
        $allInvoices = (clone $query)->get();
        
        $this->stats['total_earnings'] = $allInvoices->sum('doctor_commission_amount');
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->where('status', 'Approved')->sum('amount');
        $this->stats['pending_approval_amount'] = Settlement::where('user_id', $userId)->where('status', 'Pending')->sum('amount');
        $this->stats['pending_balance'] = $this->stats['total_earnings'] - $this->stats['settled_amount'];
        $this->stats['total_invoices'] = $rangeInvoices->count();

        $this->stats['this_month_earnings'] = $rangeInvoices->sum('doctor_commission_amount');
            
        $this->stats['last_month_earnings'] = Invoice::where('referred_by_doctor_id', $userId)
            ->where('status', '!=', 'Cancelled')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('doctor_commission_amount');
    }

    private function loadAgentStats($userId)
    {
        $query = Invoice::where('referred_by_agent_id', $userId)->where('status', '!=', 'Cancelled');
        $rangeInvoices = (clone $query)->whereBetween('invoice_date', [$this->startDate, $this->endDate])->get();
        $allInvoices = (clone $query)->get();
        
        $this->stats['total_earnings'] = $allInvoices->sum('agent_commission_amount');
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->where('status', 'Approved')->sum('amount');
        $this->stats['pending_approval_amount'] = Settlement::where('user_id', $userId)->where('status', 'Pending')->sum('amount');
        $this->stats['pending_balance'] = $this->stats['total_earnings'] - $this->stats['settled_amount'];
        $this->stats['total_invoices'] = $rangeInvoices->count();

        $this->stats['this_month_earnings'] = $rangeInvoices->sum('agent_commission_amount');
            
        $this->stats['last_month_earnings'] = Invoice::where('referred_by_agent_id', $userId)
            ->where('status', '!=', 'Cancelled')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('agent_commission_amount');
    }

    private function loadCollectionCenterStats($userId)
    {
        $user = Auth::user();
        $ccId = $user->collection_center_id;
        if (!$ccId) return;

        $query = Invoice::where('collection_center_id', $ccId)->where('status', '!=', 'Cancelled');
        $rangeInvoices = (clone $query)->whereBetween('invoice_date', [$this->startDate, $this->endDate])->get();
        $allInvoices = (clone $query)->get();
        
        $this->stats['total_billing'] = $allInvoices->sum('total_amount');
        $this->stats['total_profit'] = $allInvoices->sum('cc_profit_amount');
        $this->stats['lab_dues'] = $allInvoices->sum('total_b2b_amount');
        $this->stats['total_earnings'] = $this->stats['total_profit']; // For UI consistency
        
        $this->stats['settled_amount'] = Settlement::where('user_id', $userId)->where('status', 'Approved')->sum('amount');
        $this->stats['pending_approval_amount'] = Settlement::where('user_id', $userId)->where('status', 'Pending')->sum('amount');
        $this->stats['pending_lab_payment'] = $this->stats['lab_dues'] - $this->stats['settled_amount'];
        $this->stats['pending_balance'] = $this->stats['pending_lab_payment']; // For UI consistency
        $this->stats['total_invoices'] = $rangeInvoices->count();

        $this->stats['this_month_profit'] = $rangeInvoices->sum('cc_profit_amount');
            
        $this->stats['last_month_profit'] = Invoice::where('collection_center_id', $ccId)
            ->where('status', '!=', 'Cancelled')
            ->whereMonth('invoice_date', now()->subMonth()->month)
            ->whereYear('invoice_date', now()->subMonth()->year)
            ->sum('cc_profit_amount');

        $this->stats['samples_collected'] = Invoice::where('collection_center_id', $ccId)
            ->where('status', '!=', 'Cancelled')
            ->where('sample_status', 'Collected')
            ->whereDate('sample_collected_at', today())
            ->count();

        $this->stats['reports_ready'] = Invoice::where('collection_center_id', $ccId)
            ->where('status', '!=', 'Cancelled')
            ->where('sample_status', 'Ready')
            ->count();

        $this->stats['pending_collection'] = Invoice::where('collection_center_id', $ccId)
            ->where('status', '!=', 'Cancelled')
            ->where('sample_status', 'Pending')
            ->count();
    }

    private function getChartData()
    {
        $user = Auth::user();
        $column = '';
        if ($this->role === 'Doctor') $column = 'doctor_commission_amount';
        elseif ($this->role === 'Agent') $column = 'agent_commission_amount';
        elseif ($this->role === 'Collection Center') $column = 'cc_profit_amount';

        $data = Invoice::whereBetween('invoice_date', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'Cancelled')
            ->when($this->role === 'Doctor', fn($q) => $q->where('referred_by_doctor_id', $user->id))
            ->when($this->role === 'Agent', fn($q) => $q->where('referred_by_agent_id', $user->id))
            ->when($this->role === 'Collection Center', fn($q) => $q->where('collection_center_id', $user->collection_center_id))
            ->selectRaw('DATE(invoice_date) as date, SUM('.$column.') as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(fn($d) => date('d M', strtotime($d))),
            'data' => $data->pluck('total')
        ];
    }

    public function render()
    {
        $user = Auth::user();
        $recentInvoices = [];

        if ($this->role === 'Doctor') {
            $recentInvoices = Invoice::where('referred_by_doctor_id', $user->id)->where('status', '!=', 'Cancelled')->latest()->take(10)->get();
        } elseif ($this->role === 'Agent') {
            $recentInvoices = Invoice::where('referred_by_agent_id', $user->id)->where('status', '!=', 'Cancelled')->latest()->take(10)->get();
        } elseif ($this->role === 'Collection Center') {
            $recentInvoices = Invoice::where('collection_center_id', $user->collection_center_id)->where('status', '!=', 'Cancelled')->latest()->take(10)->get();
        }

        $recentSettlements = Settlement::where('user_id', $user->id)->latest()->take(5)->get();

        return view('livewire.partner.partner-dashboard', [
            'recentInvoices' => $recentInvoices,
            'recentSettlements' => $recentSettlements,
            'chartData' => $this->getChartData()
        ]);
    }
}

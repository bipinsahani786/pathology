<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\{Invoice, LabTest, DoctorProfile, PatientProfile, CollectionCenter, User, InvoiceItem, Payment, Department, Configuration};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $fromDate;
    public $toDate;

    public function mount()
    {
        // Default to current month
        $this->fromDate = Carbon::now()->startOfMonth()->toDateString();
        $this->toDate = Carbon::now()->endOfMonth()->toDateString();
    }

    /**
     * Update filters and refresh data
     */
    public function updateFilter()
    {
        $this->dispatch('refreshCharts');
    }

    /**
     * Quick filter presets
     */
    public function setPreset($preset)
    {
        switch ($preset) {
            case 'today':
                $this->fromDate = Carbon::today()->toDateString();
                $this->toDate = Carbon::today()->toDateString();
                break;
            case 'yesterday':
                $this->fromDate = Carbon::yesterday()->toDateString();
                $this->toDate = Carbon::yesterday()->toDateString();
                break;
            case 'this_week':
                $this->fromDate = Carbon::now()->startOfWeek()->toDateString();
                $this->toDate = Carbon::now()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->fromDate = Carbon::now()->startOfMonth()->toDateString();
                $this->toDate = Carbon::now()->endOfMonth()->toDateString();
                break;
            case 'last_month':
                $this->fromDate = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $this->toDate = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                break;
            case 'last_30_days':
                $this->fromDate = Carbon::now()->subDays(30)->toDateString();
                $this->toDate = Carbon::now()->toDateString();
                break;
        }
        $this->updateFilter();
    }

    public function render()
    {
        $companyId = auth()->user()->company_id;
        $company = auth()->user()->company;
        $daysLeft = 0;
        if ($company && $company->trial_ends_at) {
            $daysLeft = now()->diffInDays($company->trial_ends_at, false);
        }
        $activeBranchId = session('active_branch_id', 'all');
        $roles = auth()->user()->roles->pluck('name')->toArray();
        $isGlobalAdmin = auth()->user()->hasAnyRole(['lab_admin', 'super_admin']) || 
                         collect($roles)->contains(fn($r) => str_ends_with($r, '_admin') || str_ends_with($r, '_super_admin') || str_contains(strtolower($r), 'admin'));

        $branchId = ($isGlobalAdmin)
            ? ($activeBranchId === 'all' ? null : $activeBranchId)
            : auth()->user()->branch_id;

        $start = Carbon::parse($this->fromDate)->startOfDay();
        $end = Carbon::parse($this->toDate)->setHour(23)->setMinute(59)->setSecond(59);

        // Cache Key based on filters
        $cacheKey = "dashboard_stats_{$companyId}_{$branchId}_" . $start->format('Ymd') . "_" . $end->format('Ymd');

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function() use ($companyId, $branchId, $start, $end) {
            // 1. Master Counts
            $stats = [
                'total_tests' => LabTest::where('company_id', $companyId)->where('is_package', false)->count(),
                'total_packages' => LabTest::where('company_id', $companyId)->where('is_package', true)->count(),
                'total_doctors' => DoctorProfile::where('company_id', $companyId)->when($branchId && !Configuration::getFor('branch_share_doctors', true), fn($q) => $q->whereHas('user', fn($u) => $u->where('branch_id', $branchId)))->count(),
                'total_patients' => User::whereHas('patientProfile', fn($q) => $q->where('company_id', $companyId))->when($branchId && !Configuration::getFor('branch_share_patients', true), fn($q) => $q->where('branch_id', $branchId))->count(),
                'total_ccs' => CollectionCenter::where('company_id', $companyId)->when($branchId, fn($q) => $q->where('branch_id', $branchId))->count(),
            ];

            // 2. Operational Stats
            $ops = [
                'pending_tests' => Invoice::where('company_id', $companyId)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->whereIn('sample_status', ['Pending', 'Collected', 'Processing'])
                    ->whereBetween('invoice_date', [$start, $end])
                    ->count(),
                'completed_tests' => Invoice::where('company_id', $companyId)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->where('sample_status', 'Ready')
                    ->whereBetween('invoice_date', [$start, $end])
                    ->count(),
                'home_visits' => Invoice::where('company_id', $companyId)
                    ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                    ->where('collection_type', 'Home Collection')
                    ->whereBetween('invoice_date', [$start, $end])
                    ->count(),
            ];

            // 3. Financial Totals
            $financials = Invoice::where('company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', '!=', 'Cancelled')
                ->whereBetween('invoice_date', [$start, $end])
                ->selectRaw('
                    SUM(total_amount) as revenue, 
                    SUM(total_amount - COALESCE(cc_profit_amount, 0) - COALESCE(doctor_commission_amount, 0) - COALESCE(agent_commission_amount, 0)) as profit, 
                    SUM(paid_amount) as collections, 
                    SUM(due_amount) as dues
                ')
                ->first();

            // 4. Rankings
            $topPackages = InvoiceItem::whereHas('invoice', fn($q) => $q->where('company_id', $companyId)->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))->whereBetween('invoice_date', [$start, $end]))
                ->where('is_package', true)
                ->select('lab_test_id', 'test_name', DB::raw('SUM(price) as total_income'), DB::raw('COUNT(*) as total_sold'))
                ->groupBy('lab_test_id', 'test_name')
                ->orderByDesc('total_income')
                ->take(5)->get();

            $topTests = InvoiceItem::whereHas('invoice', fn($q) => $q->where('company_id', $companyId)->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))->whereBetween('invoice_date', [$start, $end]))
                ->where('is_package', false)
                ->select('lab_test_id', 'test_name', DB::raw('SUM(price) as total_income'), DB::raw('COUNT(*) as total_sold'))
                ->groupBy('lab_test_id', 'test_name')
                ->orderByDesc('total_income')
                ->take(5)->get();

            $topCCs = Invoice::where('company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereBetween('invoice_date', [$start, $end])
                ->with('collectionCenter')
                ->select('collection_center_id', DB::raw('SUM(total_amount) as total_income'), DB::raw('COUNT(*) as total_bills'))
                ->groupBy('collection_center_id')
                ->orderByDesc('total_income')
                ->take(5)->get();

            $topDoctors = Invoice::where('company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereBetween('invoice_date', [$start, $end])
                ->whereNotNull('referred_by_doctor_id')
                ->with(['doctor' => fn($q) => $q->select('id', 'name')])
                ->select('referred_by_doctor_id', DB::raw('SUM(total_amount) as total_income'))
                ->groupBy('referred_by_doctor_id')
                ->orderByDesc('total_income')
                ->take(5)->get();

            $topAgents = Invoice::where('company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->whereBetween('invoice_date', [$start, $end])
                ->whereNotNull('referred_by_agent_id')
                ->with(['agent' => fn($q) => $q->select('id', 'name')])
                ->select('referred_by_agent_id', DB::raw('SUM(total_amount) as total_income'))
                ->groupBy('referred_by_agent_id')
                ->orderByDesc('total_income')
                ->take(5)->get();

            // 5. Chart Data
            $chartRawData = Invoice::where('company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->where('status', '!=', 'Cancelled')
                ->whereBetween('invoice_date', [$start, $end])
                ->select(
                    DB::raw('DATE(invoice_date) as date'), 
                    DB::raw('SUM(total_amount) as daily_revenue'), 
                    DB::raw('SUM(total_amount - COALESCE(cc_profit_amount, 0) - COALESCE(doctor_commission_amount, 0) - COALESCE(agent_commission_amount, 0)) as daily_profit')
                )
                ->groupBy('date')->orderBy('date')->get();

            $deptData = InvoiceItem::whereHas('invoice', fn($q) => $q->where('invoices.company_id', $companyId)->when($branchId, fn($q2) => $q2->where('branch_id', $branchId))->whereBetween('invoices.invoice_date', [$start, $end]))
                ->join('lab_tests', 'invoice_items.lab_test_id', '=', 'lab_tests.id')
                ->join('departments', 'lab_tests.department_id', '=', 'departments.id')
                ->select('departments.name as dept_name', DB::raw('COUNT(*) as test_count'))
                ->groupBy('dept_name')
                ->orderByDesc('test_count')
                ->get();

            $paymentData = Payment::where('payments.company_id', $companyId)
                ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
                ->whereBetween('payments.created_at', [$start, $end])
                ->join('payment_modes', 'payments.payment_mode_id', '=', 'payment_modes.id')
                ->select('payment_modes.name as mode_name', DB::raw('SUM(payments.amount) as total_collected'))
                ->groupBy('mode_name')
                ->orderByDesc('total_collected')
                ->get();

            $channelData = Invoice::where('invoices.company_id', $companyId)
                ->when($branchId, fn($q) => $q->where('invoices.branch_id', $branchId))
                ->whereBetween('invoices.invoice_date', [$start, $end])
                ->select(DB::raw("COALESCE(collection_type, 'Direct') as channel"), DB::raw('COUNT(*) as count'))
                ->groupBy('channel')
                ->get();

            return compact('stats', 'ops', 'financials', 'topPackages', 'topTests', 'topCCs', 'topDoctors', 'topAgents', 'chartRawData', 'deptData', 'paymentData', 'channelData');
        });

        // Other non-cached or lightweight data
        $staffActivity = [
            'active_admins' => User::where('company_id', $companyId)
                ->whereHas('roles', fn($q) => $q->where('name', 'like', '%admin%'))
                ->where('is_active', true)->count(),
            'active_staff' => User::where('company_id', $companyId)
                ->whereHas('roles', fn($q) => $q->where('name', 'not like', '%admin%')->whereNotIn('name', ['patient', 'doctor', 'agent']))
                ->where('is_active', true)->count(),
        ];

        // 6. CHART DATA
        $chartRawData = $data['chartRawData'];
        $deptData = $data['deptData'];
        $paymentData = $data['paymentData'];
        $channelData = $data['channelData'];

        return view('livewire.lab.dashboard', [
            'daysLeft' => $daysLeft,
            'stats' => $data['stats'],
            'ops' => $data['ops'],
            'financials' => $data['financials'],
            'topPackages' => $data['topPackages'],
            'topTests' => $data['topTests'],
            'topCCs' => $data['topCCs'],
            'topDoctors' => $data['topDoctors'],
            'topAgents' => $data['topAgents'],
            'staffActivity' => $staffActivity,
            // Chart 1
            'chartLabels' => $chartRawData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'revenueValues' => $chartRawData->pluck('daily_revenue')->toArray(),
            'profitValues' => $chartRawData->pluck('daily_profit')->toArray(),
            // Chart 2
            'deptLabels' => $deptData->pluck('dept_name')->toArray(),
            'deptCounts' => $deptData->pluck('test_count')->toArray(),
            // Chart 3
            'payLabels' => $paymentData->pluck('mode_name')->toArray(),
            'payValues' => $paymentData->pluck('total_collected')->toArray(),
            // Chart 4
            'channelLabels' => $channelData->pluck('channel')->toArray(),
            'channelValues' => $channelData->pluck('count')->toArray(),
        ])->layout('layouts.app', ['title' => 'Lab Performance Analytics']);
    }
}
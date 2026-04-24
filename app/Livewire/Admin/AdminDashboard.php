<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Company;
use App\Models\GlobalTest;
use App\Models\Plan;

class AdminDashboard extends Component
{
    public function render()
    {
        // Calculate estimated Monthly Recurring Revenue (MRR)
        $mrr = Company::with('plan')->get()->sum(function ($company) {
            return $company->plan ? $company->plan->price : 0;
        });

        // Recent registrations
        $recentLabs = Company::with('plan')->orderBy('created_at', 'desc')->take(5)->get();

        // Sales Agent Stats (Professional Model)
        $salesStats = \App\Models\SalesAgent::with(['companies.plan'])
            ->get()
            ->map(function ($agent) {
                return [
                    'agent' => $agent->name,
                    'total_labs' => $agent->companies->count(),
                    'total_revenue' => $agent->companies->sum(fn($l) => $l->plan ? $l->plan->price : 0)
                ];
            })
            ->sortByDesc('total_labs');

        // Legacy / Direct Source Stats
        $legacyStats = Company::whereNull('sales_agent_id')
            ->with('plan')
            ->get()
            ->groupBy(function($company) {
                return $company->referred_by ?: 'Direct / Website';
            })
            ->map(function ($labs, $source) {
                return [
                    'agent' => $source . ' (Direct)',
                    'total_labs' => $labs->count(),
                    'total_revenue' => $labs->sum(fn($l) => $l->plan ? $l->plan->price : 0)
                ];
            });

        $combinedStats = $salesStats->concat($legacyStats)->sortByDesc('total_labs');

        $totalCommission = \App\Models\PlatformInvoice::where('status', 'paid')->sum('agent_commission');
        $totalPayouts = \App\Models\SalesAgentPayout::sum('amount');

        return view('livewire.admin.admin-dashboard', [
            'totalLabs' => Company::count(),
            'totalGlobalTests' => GlobalTest::count(),
            'totalPlans' => Plan::count(),
            'activePlans' => Plan::where('is_active', true)->count(),
            'mrr' => $mrr,
            'recentLabs' => $recentLabs,
            'salesStats' => $combinedStats,
            'totalCommission' => $totalCommission,
            'totalPayouts' => $totalPayouts,
        ])->layout('layouts.app');
    }
}
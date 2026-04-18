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

        return view('livewire.admin.admin-dashboard', [
            'totalLabs' => Company::count(),
            'totalGlobalTests' => GlobalTest::count(),
            'totalPlans' => Plan::count(),
            'activePlans' => Plan::where('is_active', true)->count(),
            'mrr' => $mrr,
            'recentLabs' => $recentLabs,
        ])->layout('layouts.app');
    }
}
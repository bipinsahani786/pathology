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
        return view('livewire.admin.admin-dashboard', [
            'totalLabs' => Company::count(),
            'totalGlobalTests' => GlobalTest::count(),
            'totalPlans' => Plan::count(),
            'activePlans' => Plan::where('is_active', true)->count(),
        ])->layout('layouts.app');
    }
}
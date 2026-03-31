<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use App\Models\Branch;

class BranchSwitcher extends Component
{
    public $branches = [];
    public $activeBranchId;

    public function mount()
    {
        $this->loadBranches();
        $this->activeBranchId = session('active_branch_id', 'all');
    }

    public function loadBranches()
    {
        $this->branches = Branch::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function switchBranch($branchId)
    {
        if ($branchId === 'all') {
            session(['active_branch_id' => null]);
        } else {
            session(['active_branch_id' => $branchId]);
        }
        
        // Refresh the page to apply the global scope
        $this->redirect(request()->header('Referer') ?? route('lab.dashboard'));
    }

    public function render()
    {
        return view('livewire.lab.branch-switcher');
    }
}

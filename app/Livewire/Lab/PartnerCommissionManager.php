<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\LabTest;
use App\Models\PartnerTestCommission;
use Illuminate\Support\Facades\DB;

class PartnerCommissionManager extends Component
{
    use WithPagination;

    public $partner_id;
    public $partner_role; // 'doctor' or 'agent'
    public $partner_name;
    public $global_commission;

    public $searchTerm = '';
    
    // Stores existing override states
    // Structure: $commissions[$test_id] = ['type' => 'percentage', 'value' => 10]
    public $commissions = [];

    protected $paginationTheme = 'bootstrap';

    public function mount($partner_id)
    {
        $this->partner_id = $partner_id;
        $user = User::with(['doctorProfile', 'agentProfile'])->findOrFail($partner_id);
        $this->partner_name = $user->name;
        
        if ($user->hasRole('doctor')) {
            $this->partner_role = 'doctor';
            $this->global_commission = $user->doctorProfile->commission_percentage ?? 0;
            $this->authorize('edit doctors');
        } elseif ($user->hasRole('agent')) {
            $this->partner_role = 'agent';
            $this->global_commission = $user->agentProfile->commission_percentage ?? 0;
            $this->authorize('edit agents');
        } else {
            abort(403, 'Invalid partner role.');
        }

        $this->loadCommissions();
    }

    public function loadCommissions()
    {
        $existing = PartnerTestCommission::where('user_id', $this->partner_id)->get();
        foreach ($existing as $rule) {
            $this->commissions[$rule->lab_test_id] = [
                'type' => $rule->commission_type,
                'value' => $rule->commission_value,
            ];
        }
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function saveCommission($test_id)
    {
        if (!isset($this->commissions[$test_id])) return;
        
        $type = $this->commissions[$test_id]['type'] ?? 'percentage';
        $value = $this->commissions[$test_id]['value'] ?? null;

        if ($value === '' || $value === null) {
            // Remove rule if empty
            PartnerTestCommission::where('user_id', $this->partner_id)
                ->where('lab_test_id', $test_id)
                ->delete();
            unset($this->commissions[$test_id]);
            session()->flash('success', 'Override removed.');
            return;
        }

        PartnerTestCommission::updateOrCreate(
            [
                'user_id' => $this->partner_id,
                'lab_test_id' => $test_id,
            ],
            [
                'company_id' => auth()->user()->company_id,
                'commission_type' => $type,
                'commission_value' => $value,
            ]
        );

        session()->flash('success', 'Commission saved successfully.');
    }

    public function render()
    {
        $tests = LabTest::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('test_code', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('is_package', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('livewire.lab.partner-commission-manager', [
            'tests' => $tests
        ])->layout('layouts.app', ['title' => 'Test-based Commissions - ' . $this->partner_name]);
    }
}

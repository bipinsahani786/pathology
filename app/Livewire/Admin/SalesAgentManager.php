<?php

namespace App\Livewire\Admin;

use App\Models\SalesAgent;
use Livewire\Component;
use Livewire\WithPagination;

class SalesAgentManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    public $isModalOpen = false;
    public $isPayoutModalOpen = false;

    // Payout fields
    public $payoutAgentId;
    public $payoutAmount;
    public $payoutMethod = 'Bank Transfer';
    public $payoutReference;
    public $payoutNotes;

    // Form fields
    public $agentId;
    public $name;
    public $email;
    public $phone;
    public $commissionRate = 0;
    public $status = 'active';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:sales_agents,email',
        'phone' => 'nullable|string|max:20',
        'commissionRate' => 'required|numeric|min:0|max:100',
        'status' => 'required|in:active,inactive',
    ];

    public function render()
    {
        $agents = SalesAgent::withCount('companies')
            ->where(function($q) {
                $q->where('name', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'ilike', '%' . $this->searchTerm . '%')
                  ->orWhere('phone', 'ilike', '%' . $this->searchTerm . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.admin.sales-agent-manager', [
            'agents' => $agents
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['agentId', 'name', 'email', 'phone', 'commissionRate', 'status']);
        $this->isModalOpen = true;
    }

    public function editAgent($id)
    {
        $this->resetValidation();
        $agent = SalesAgent::findOrFail($id);
        $this->agentId = $agent->id;
        $this->name = $agent->name;
        $this->email = $agent->email;
        $this->phone = $agent->phone;
        $this->commissionRate = $agent->commission_rate;
        $this->status = $agent->status;
        $this->isModalOpen = true;
    }

    public function saveAgent()
    {
        $rules = $this->rules;
        if ($this->agentId) {
            $rules['email'] = 'nullable|email|unique:sales_agents,email,' . $this->agentId;
        }

        $this->validate($rules);

        SalesAgent::updateOrCreate(
            ['id' => $this->agentId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'commission_rate' => $this->commissionRate,
                'status' => $this->status,
            ]
        );

        session()->flash('success', $this->agentId ? 'Agent updated successfully.' : 'Agent created successfully.');
        $this->isModalOpen = false;
    }

    public function toggleStatus($id)
    {
        $agent = SalesAgent::findOrFail($id);
        $agent->update(['status' => $agent->status === 'active' ? 'inactive' : 'active']);
        session()->flash('success', 'Status updated.');
    }

    public function openPayoutModal($id)
    {
        $this->resetValidation();
        $agent = SalesAgent::findOrFail($id);
        $this->payoutAgentId = $id;
        $this->payoutAmount = $agent->balance;
        $this->payoutReference = '';
        $this->payoutNotes = '';
        $this->isPayoutModalOpen = true;
    }

    public function savePayout()
    {
        $this->validate([
            'payoutAmount' => 'required|numeric|min:1',
            'payoutMethod' => 'required|string',
        ]);

        \App\Models\SalesAgentPayout::create([
            'sales_agent_id' => $this->payoutAgentId,
            'amount' => $this->payoutAmount,
            'paid_at' => now(),
            'payment_method' => $this->payoutMethod,
            'transaction_reference' => $this->payoutReference,
            'notes' => $this->payoutNotes,
        ]);

        session()->flash('success', 'Payout recorded successfully.');
        $this->isPayoutModalOpen = false;
    }
}

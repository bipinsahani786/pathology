<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Services\PlanService;

class PlanManager extends Component
{
    // Form fields
    public $plan_id, $name, $price;
    public $duration_in_days = 30;
    public $is_active = true;

    // Fixed SaaS Features & Limits
    public $max_branches = 1;
    public $max_staff = 3;
    public $max_doctors = 5;
    public $max_agents = 2;
    public $max_collection_centers = 1;
    public $has_inventory = false;
    public $has_custom_invoice = false;

    public $isModalOpen = false;

    /**
     * Get an instance of the PlanService.
     */
    protected function planService(): PlanService
    {
        return app(PlanService::class);
    }

    public function create()
    {
        $this->resetFields();
        $this->resetFeaturesToDefault();
        $this->isModalOpen = true;
    }

    private function resetFeaturesToDefault()
    {
        $this->max_branches = 1;
        $this->max_staff = 3;
        $this->max_doctors = 5;
        $this->max_agents = 2;
        $this->max_collection_centers = 1;
        $this->has_inventory = false;
        $this->has_custom_invoice = false;
    }

    public function resetFields()
    {
        $this->reset(['plan_id', 'name', 'price', 'duration_in_days', 'is_active']);
        $this->resetFeaturesToDefault();
        $this->resetValidation();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function store()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'max_branches' => 'required|integer',
            'max_staff' => 'required|integer',
            'max_doctors' => 'required|integer',
            'max_agents' => 'required|integer',
            'max_collection_centers' => 'required|integer',
            'has_inventory' => 'boolean',
            'has_custom_invoice' => 'boolean',
        ]);

        // Pack fixed features into the JSON format for the service
        $finalFeaturesArr = [
            ['key' => 'branches', 'value' => $this->max_branches],
            ['key' => 'staff', 'value' => $this->max_staff],
            ['key' => 'doctors', 'value' => $this->max_doctors],
            ['key' => 'agents', 'value' => $this->max_agents],
            ['key' => 'collection_centers', 'value' => $this->max_collection_centers],
            ['key' => 'inventory', 'value' => $this->has_inventory],
            ['key' => 'custom_invoice', 'value' => $this->has_custom_invoice],
        ];

        // Use the service to handle the business logic
        $this->planService()->savePlan($validatedData, $finalFeaturesArr, $this->plan_id);

        session()->flash('message', $this->plan_id ? 'Plan Updated Successfully.' : 'Plan Created Successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $plan = $this->planService()->getPlanById($id);

        $this->plan_id = $plan->id;
        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->duration_in_days = $plan->duration_in_days;
        $this->is_active = $plan->is_active;

        // Map JSON features back to public properties
        $f = $plan->features ?? [];
        $this->max_branches = $f['branches'] ?? 1;
        $this->max_staff = $f['staff'] ?? 3;
        $this->max_doctors = $f['doctors'] ?? 5;
        $this->max_agents = $f['agents'] ?? 2;
        $this->max_collection_centers = $f['collection_centers'] ?? 1;
        $this->has_inventory = $f['inventory'] ?? false;
        $this->has_custom_invoice = $f['custom_invoice'] ?? false;

        $this->isModalOpen = true;
    }

    public function toggleStatus($id)
    {
        $this->planService()->togglePlanStatus($id);
    }

    public function delete($id)
    {
        $this->planService()->deletePlan($id);
        session()->flash('message', 'Plan Deleted Successfully.');
    }

    public function render()
    {
        // Use the service to fetch data
        $plans = $this->planService()->getAllPlans();

        return view('livewire.admin.plan-manager', [
            'plans' => $plans
        ])->layout('layouts.app', ['title' => 'Manage Plans']);
    }
}
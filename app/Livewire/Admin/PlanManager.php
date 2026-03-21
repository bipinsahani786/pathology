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

    // Array to manage dynamic JSONB features
    public array $features = [];
    public $isModalOpen = false;



    /**
     * Get an instance of the PlanService.
     */
    protected function planService(): PlanService
    {
        return app(PlanService::class);
    }

    public function addFeature()
    {
        $this->features[] = ['key' => '', 'value' => ''];
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function create()
    {
        $this->resetFields();
        $this->addFeature();
        $this->isModalOpen = true;
    }

    public function resetFields()
    {
        $this->reset(['plan_id', 'name', 'price', 'duration_in_days', 'is_active']);
        $this->features = [];
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
        ]);

        // Use the service to handle the business logic
        $this->planService()->savePlan($validatedData, $this->features, $this->plan_id);

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

        // Use the service to format features for the UI
        $this->features = $this->planService()->formatFeaturesForUi($plan->features);

        if (empty($this->features)) {
            $this->addFeature();
        }

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
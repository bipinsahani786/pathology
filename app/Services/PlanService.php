<?php

namespace App\Services;

use App\Models\Plan;

class PlanService
{
    /**
     * Get all plans ordered by descending ID.
     */
    public function getAllPlans($perPage = 10)
    {
        return Plan::orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * Get a specific plan by ID.
     */
    public function getPlanById($id)
    {
        return Plan::findOrFail($id);
    }

    /**
     * Store or update a plan.
     */
    public function savePlan(array $data, array $features, $planId = null)
    {
        $formattedFeatures = $this->formatFeaturesForJson($features);

        $planData = [
            'name' => $data['name'],
            'price' => $data['price'],
            'duration_in_days' => $data['duration_in_days'],
            'features' => $formattedFeatures,
            'is_active' => $data['is_active'],
        ];

        return Plan::updateOrCreate(['id' => $planId], $planData);
    }

    /**
     * Toggle the active status of a plan.
     */
    public function togglePlanStatus($id)
    {
        $plan = $this->getPlanById($id);
        $plan->update(['is_active' => !$plan->is_active]);
        return $plan;
    }

    /**
     * Delete a plan.
     */
    public function deletePlan($id)
    {
        $plan = $this->getPlanById($id);
        $plan->delete();
        return true;
    }

    /**
     * Format dynamic feature array to associative array for JSONB.
     */
    private function formatFeaturesForJson(array $features): array
    {
        $formattedFeatures = [];
        foreach ($features as $feature) {
            if (!empty($feature['key'])) {
                $formattedFeatures[$feature['key']] = $feature['value'];
            }
        }
        return $formattedFeatures;
    }

    /**
     * Convert JSON features back to a dynamic array structure for the UI.
     */
    public function formatFeaturesForUi(?array $features): array
    {
        $formatted = [];
        if (!empty($features)) {
            foreach ($features as $key => $value) {
                $formatted[] = ['key' => $key, 'value' => $value];
            }
        }
        return $formatted;
    }
}
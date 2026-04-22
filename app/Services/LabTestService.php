<?php

namespace App\Services;

use App\Models\LabTest;
use App\Models\GlobalTest;
use Illuminate\Support\Facades\Log;

class LabTestService
{
    /**
     * Get paginated Lab Tests with search and filtering
     */
    public function getPaginatedTests($searchTerm = null, $filterCategory = null, $perPage = 10)
    {
        $query = LabTest::with('dept');

        if (!empty($searchTerm)) {
            $query = $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', '%' . $searchTerm . '%')
                  ->orWhere('test_code', 'ilike', '%' . $searchTerm . '%');
            });
        }

        return $query->when($filterCategory, fn($q) => $q->where('department_id', $filterCategory))
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get Global Tests for the import modal
     */
    public function searchGlobalTests($globalSearch = null, $limit = 15)
    {
        return GlobalTest::with('dept')
            ->where('name', 'ilike', '%' . $globalSearch . '%')
            ->orWhere('test_code', 'ilike', '%' . $globalSearch . '%')
            ->limit($limit)
            ->get();
    }

    /**
     * Save or Update a Custom Lab Test
     */
    public function saveTest(array $data, $testId = null)
    {
        try {
            return LabTest::updateOrCreate(
            ['id' => $testId],
            [
                'company_id' => auth()->user()->company_id, // Handled by trait, but safe to pass
                'name' => $data['name'],
                'test_code' => $data['test_code'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'description' => $data['description'] ?? null,
                'interpretation' => $data['interpretation'] ?? null,
                'mrp' => $data['mrp'] ?? 0,
                'b2b_price' => $data['b2b_price'] ?? 0,
                'sample_type' => $data['sample_type'] ?? null,
                'tat_hours' => $data['tat_hours'] ?? 24,
                'parameters' => $data['parameters'] ?? [],
                'is_active' => $data['is_active'] ?? true,
            ]
            );
        }
        catch (\Exception $e) {
            Log::error('Error saving Lab Test: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Import a test from Global Master to Lab
     */
    public function importFromGlobal($globalTestId, $companyId)
    {
        $global = GlobalTest::findOrFail($globalTestId);

        $globalParams = is_array($global->default_parameters) ? $global->default_parameters : [];

        // Map parameters to ensure all keys exist for the lab
        $mappedParams = array_map(function ($p) {
            return [
                'name' => $p['param'] ?? $p['name'] ?? '',
                'unit' => $p['unit'] ?? '',
                'range_type' => $p['range_type'] ?? (isset($p['ranges']) ? 'flexible' : 'general'),
                'options' => $p['options'] ?? [],
                'ranges' => $p['ranges'] ?? [],
                'general_range' => $p['general_range'] ?? '',
                'male_range' => $p['male_range'] ?? '',
                'female_range' => $p['female_range'] ?? '',
                'normal_value' => $p['normal_value'] ?? '',
                'short_code' => $p['short_code'] ?? $p['code'] ?? '',
                'input_type' => $p['input_type'] ?? 'numeric',
                'formula' => $p['formula'] ?? '',
                'method' => $p['method'] ?? '',
            ];
        }, $globalParams);

        // Create the test
        return LabTest::create([
            'company_id' => $companyId,
            'global_test_id' => $global->id,
            'name' => $global->name,
            'method' => $global->method,
            'test_code' => $global->test_code,
            'department_id' => $global->department_id, // Inherit from global test
            'description' => $global->description ?? null,
            'interpretation' => $global->interpretation ?? null,
            'parameters' => $mappedParams,
            'mrp' => $global->mrp ?? 0,
            'b2b_price' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Toggle the active status of a test
     */
    public function toggleStatus($id)
    {
        $test = LabTest::findOrFail($id);
        $test->update(['is_active' => !$test->is_active]);
        return $test->is_active;
    }

    /**
     * Delete a test
     */
    public function deleteTest($id)
    {
        $test = LabTest::findOrFail($id);
        return $test->delete();
    }

    /**
     * Get a specific Lab Test
     */
    public function getTestById($id)
    {
        return LabTest::findOrFail($id);
    }



    // ==========================================
    // PACKAGE (PROFILES) SPECIFIC METHODS
    // ==========================================

    /**
     * 1. Get paginated list of Packages only (where is_package is true)
     */
    public function getPaginatedPackages($searchTerm = null, $perPage = 10)
    {
        $query = LabTest::where('is_package', true);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', '%' . $searchTerm . '%')
                  ->orWhere('test_code', 'ilike', '%' . $searchTerm . '%');
            });
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * 2. Search for Single Tests to add inside a Package (fetches only single tests)
     */
    public function searchSingleTestsForPackage($searchTerm, $limit = 10)
    {
        if (empty($searchTerm))
            return collect();

        $query = LabTest::where('is_package', false);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', '%' . $searchTerm . '%')
                  ->orWhere('test_code', 'ilike', '%' . $searchTerm . '%');
            });
        }

        return $query->limit($limit)->get();
    }

    /**
     * 3. Fetch Single Tests by their IDs (Used when editing an existing package)
     */
    public function getTestsByIds(array $ids)
    {
        return LabTest::with('dept')->whereIn('id', $ids)->get(['id', 'name', 'test_code', 'department_id', 'mrp']);
    }
}
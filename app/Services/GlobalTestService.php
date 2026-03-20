<?php

namespace App\Services;

use App\Models\GlobalTest;
use Illuminate\Support\Facades\Log;

class GlobalTestService
{

    /**
     * Create or Update a Global Test
     */
    public function saveTest(array $data, $id = null)
    {
        try {
            if ($id) {
                $test = GlobalTest::findOrFail($id);
                $test->update($data);
                return $test;
            }

            return GlobalTest::create($data);

        } catch (\Exception $e) {
            Log::error('Error saving Global Test: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a Global Test
     */
    public function deleteTest($id)
    {
        $test = GlobalTest::findOrFail($id);
        return $test->delete();
    }

    /**
     * Get Paginated Tests with Search and Category Filter
     *
     * @param int $perPage
     * @param string|null $search
     * @param string|null $department_id
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedTests($perPage = 10, $search = null, $department_id = null)
    {
        $query = GlobalTest::query();

        // Apply Search Filter (Using 'ilike' for PostgreSQL case-insensitive search)
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                    ->orWhere('test_code', 'ilike', '%' . $search . '%');
            });
        }

        // Apply Department Filter
        if (!empty($department_id)) {
            $query->where('department_id', $department_id);
        }

        return $query->with('dept')->orderBy('id', 'desc')->paginate($perPage);
    }


    /**
     * Retrieve a specific Global Test by its ID.
     * Throws a ModelNotFoundException if the record does not exist.
     *
     * @param int $id
     * @return \App\Models\GlobalTest
     */
    public function getTestById($id)
    {
        return GlobalTest::findOrFail($id);
    }


}
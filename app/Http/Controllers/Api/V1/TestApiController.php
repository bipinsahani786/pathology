<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LabTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestApiController extends BaseApiController
{
    /**
     * List active individual tests catalog.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $query = LabTest::where('company_id', $company->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->where('is_package', false)->orWhereNull('is_package');
            })
            ->with('dept:id,name');

        // Search by keyword
        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('test_code', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($deptId = $request->get('department_id')) {
            $query->where('department_id', $deptId);
        }

        $perPage = min((int) $request->get('per_page', 50), 100);
        $tests = $query->orderBy('name')->paginate($perPage);

        // Map tests for clean public consumption
        $items = collect($tests->items())->map(function ($test) {
            $descLower = strtolower($test->description ?? '');
            $fastingRequired = str_contains($descLower, 'fasting') || str_contains(strtolower($test->name), 'fasting');

            return [
                'id'               => $test->id,
                'name'             => $test->name,
                'test_code'        => $test->test_code,
                'department_id'    => $test->department_id,
                'department_name'  => $test->dept->name ?? $test->department ?? 'General',
                'price'            => (float) $test->mrp,
                'sample_type'      => $test->sample_type ?: 'Blood',
                'tat_hours'        => $test->tat_hours ? (int) $test->tat_hours : null,
                'fasting_required' => $fastingRequired,
                'description'      => $test->description,
            ];
        });

        return $this->success([
            'tests'        => $items,
            'current_page' => $tests->currentPage(),
            'last_page'    => $tests->lastPage(),
            'total'        => $tests->total(),
            'per_page'     => $tests->perPage(),
        ], 'Tests catalog retrieved successfully.');
    }
}

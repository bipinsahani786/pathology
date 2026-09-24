<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentApiController extends BaseApiController
{
    /**
     * List active test departments/categories.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $departments = Department::withoutGlobalScope('tenant')
            ->where(function ($query) use ($company) {
                $query->where('company_id', $company->id)
                    ->orWhere('is_system', true);
            })
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return $this->success($departments, 'Departments retrieved successfully.');
    }
}

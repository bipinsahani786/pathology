<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Branch;
use App\Models\CollectionCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BranchApiController extends BaseApiController
{
    /**
     * List active branches and collection centers of the lab.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $branches = Branch::where('company_id', $company->id)
            ->select('id', 'name', 'address', 'phone')
            ->get();

        $collectionCenters = CollectionCenter::where('company_id', $company->id)
            ->where('is_active', true)
            ->select('id', 'branch_id', 'name', 'center_code', 'address')
            ->get();

        return $this->success([
            'branches' => $branches,
            'collection_centers' => $collectionCenters,
        ], 'Branches and collection centers retrieved successfully.');
    }
}

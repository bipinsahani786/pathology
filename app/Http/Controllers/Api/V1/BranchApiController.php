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
            ->where('is_active', true)
            ->get()
            ->map(function ($b) {
                return [
                    'id'             => $b->id,
                    'name'           => $b->name,
                    'address'        => $b->address,
                    'phone'          => $b->contact_number,
                    'contact_number' => $b->contact_number,
                    'type'           => $b->type ?? 'main_lab',
                ];
            });

        $collectionCenters = CollectionCenter::where('company_id', $company->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($cc) {
                return [
                    'id'          => $cc->id,
                    'branch_id'   => $cc->branch_id,
                    'name'        => $cc->name,
                    'center_code' => $cc->center_code,
                    'address'     => $cc->address,
                    'is_main_lab' => (bool) $cc->is_main_lab,
                ];
            });

        return $this->success([
            'branches' => $branches,
            'collection_centers' => $collectionCenters,
        ], 'Branches and collection centers retrieved successfully.');
    }
}

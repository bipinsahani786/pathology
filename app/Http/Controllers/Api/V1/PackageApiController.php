<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\LabTest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageApiController extends BaseApiController
{
    /**
     * List all active test packages with included test count.
     */
    public function index(Request $request): JsonResponse
    {
        $company = $this->getCompany($request);

        $packages = LabTest::where('company_id', $company->id)
            ->where('is_active', true)
            ->where('is_package', true)
            ->orderBy('name')
            ->get();

        $data = $packages->map(function ($pkg) {
            $linkedIds = is_array($pkg->linked_test_ids)
                ? $pkg->linked_test_ids
                : json_decode($pkg->linked_test_ids, true) ?? [];

            return [
                'id'          => $pkg->id,
                'name'        => $pkg->name,
                'test_code'   => $pkg->test_code,
                'price'       => (float) $pkg->mrp,
                'sample_type' => $pkg->sample_type ?: 'Blood',
                'tat_hours'   => $pkg->tat_hours ? (int) $pkg->tat_hours : null,
                'description' => $pkg->description,
                'tests_count' => count($linkedIds),
            ];
        });

        return $this->success($data, 'Test packages retrieved successfully.');
    }

    /**
     * Get single package with all included tests and individual parameters in preserved order.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $company = $this->getCompany($request);

        $package = LabTest::where('company_id', $company->id)
            ->where('is_active', true)
            ->where('is_package', true)
            ->find($id);

        if (!$package) {
            return $this->error('Package not found or inactive.', 404);
        }

        // Get linked tests in preserved sequence
        $linkedTests = $package->getLinkedTests();

        $totalParametersCount = 0;
        $formattedTests = $linkedTests->map(function ($t) use (&$totalParametersCount) {
            $rawParams = is_array($t->parameters) ? $t->parameters : json_decode($t->parameters, true) ?? [];
            $paramList = [];

            foreach ($rawParams as $p) {
                // Ignore layout headings / separators
                if (($p['type'] ?? '') === 'heading') {
                    continue;
                }
                $paramList[] = [
                    'name'      => $p['name'] ?? '',
                    'unit'      => $p['unit'] ?? '',
                    'ref_range' => $p['ref_range'] ?? $p['reference_range'] ?? '',
                ];
            }

            $totalParametersCount += count($paramList);

            return [
                'id'               => $t->id,
                'name'             => $t->name,
                'test_code'        => $t->test_code,
                'department'       => $t->dept->name ?? $t->department ?? 'General',
                'sample_type'      => $t->sample_type ?: 'Blood',
                'parameters_count' => count($paramList),
                'parameters'       => $paramList,
            ];
        });

        return $this->success([
            'id'                     => $package->id,
            'name'                   => $package->name,
            'test_code'              => $package->test_code,
            'price'                  => (float) $package->mrp,
            'sample_type'            => $package->sample_type ?: 'Blood',
            'tat_hours'              => $package->tat_hours ? (int) $package->tat_hours : null,
            'description'            => $package->description,
            'tests_count'            => $formattedTests->count(),
            'total_parameters_count' => $totalParametersCount,
            'included_tests'         => $formattedTests,
        ], 'Package details retrieved successfully.');
    }
}

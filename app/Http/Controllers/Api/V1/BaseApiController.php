<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BaseApiController extends Controller
{
    /**
     * Get the authenticated company attached by ValidateLabApiKey middleware.
     */
    protected function getCompany(Request $request): Company
    {
        return $request->attributes->get('lab_company');
    }

    /**
     * Send a standardized successful JSON response.
     */
    protected function success($data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];

        return response()->json($response, $code);
    }

    /**
     * Send a standardized error JSON response.
     */
    protected function error(string $message, int $code = 400, $errors = []): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}

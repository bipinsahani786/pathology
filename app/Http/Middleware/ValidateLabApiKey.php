<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateLabApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Extract API key from header or query param
        $apiKey = $request->header('X-Lab-Api-Key');

        if (!$apiKey) {
            $bearer = $request->bearerToken();
            if ($bearer) {
                $apiKey = $bearer;
            }
        }

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key is missing. Please provide your API key in the X-Lab-Api-Key header.',
            ], 401);
        }

        // 2. Locate active Company by API Key
        $company = Company::with('plan')->where('api_key', $apiKey)->first();

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key provided.',
            ], 401);
        }

        // 3. Verify Lab Account Status
        if ($company->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Lab account is currently inactive.',
            ], 403);
        }

        // 4. Verify Lab Admin has toggled API to enabled
        if (!$company->api_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Website API is currently disabled in Lab Settings.',
            ], 403);
        }

        // 5. SUPERADMIN PLAN ENFORCEMENT: Verify Plan contains 'website_api' feature
        if (!$company->hasWebsiteApiFeature()) {
            return response()->json([
                'success' => false,
                'message' => 'External Website API feature is not enabled in your subscription plan. Please contact Superadmin to upgrade your plan.',
            ], 403);
        }

        // 6. Optional Allowed Origin / CORS header handling
        $origin = $request->header('Origin');
        if (!empty($company->api_allowed_origin) && !empty($origin)) {
            $allowedOrigin = rtrim(trim($company->api_allowed_origin), '/');
            $requestOrigin = rtrim(trim($origin), '/');

            // If an explicit origin is configured and does not match (and isn't wildcard *)
            if ($allowedOrigin !== '*' && strcasecmp($allowedOrigin, $requestOrigin) !== 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request origin is not authorized for this API key.',
                ], 403);
            }
        }

        // 7. Attach company to request attributes for downstream controllers
        $request->attributes->set('lab_company', $company);

        $response = $next($request);

        // Add CORS headers for web requests
        if ($origin) {
            $response->headers->set('Access-Control-Allow-Origin', $company->api_allowed_origin ?: '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Lab-Api-Key, Authorization');
        }

        return $response;
    }
}

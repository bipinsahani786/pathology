<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'branch_id', 'config_key', 'config_value'];

    public static function getFor(string $key, $default = null, $companyId = null, $branchId = null)
    {
        $companyId = $companyId ?: (auth()->user()->company_id ?? null);
        if (! $companyId) {
            return $default;
        }

        $resolveBranch = true;
        if ($branchId === 'global') {
            $branchId = null;
            $resolveBranch = false;
        }

        // If branchId is not passed but a user is logged in, default to active branch from session or user branch
        if ($resolveBranch && $branchId === null && auth()->check()) {
            $branchId = session('active_branch_id') ?: auth()->user()->branch_id;
        }

        $branchCacheKey = $branchId ?: 'global';
        $cacheKey = "config_{$companyId}_{$branchCacheKey}_{$key}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($companyId, $branchId, $key, $default) {
            // 1. Try to find branch-specific configuration
            if ($branchId) {
                $config = static::where('company_id', $companyId)
                    ->where('branch_id', $branchId)
                    ->where('config_key', $key)
                    ->first();
                if ($config !== null) {
                    return $config->config_value;
                }
            }

            // 2. Fallback to company-wide configuration (branch_id = null)
            $globalConfig = static::where('company_id', $companyId)
                ->whereNull('branch_id')
                ->where('config_key', $key)
                ->first();

            return $globalConfig ? $globalConfig->config_value : $default;
        });
    }

    /**
     * Set a config value for a specific company/branch or the current authenticated context.
     */
    public static function setFor(string $key, $value, $companyId = null, $branchId = null): void
    {
        $companyId = $companyId ?: (auth()->check() ? auth()->user()->company_id : null);
        if (! $companyId) {
            return;
        }

        if ($branchId === 'global') {
            $branchId = null;
        }

        static::updateOrCreate(
            ['company_id' => $companyId, 'branch_id' => $branchId, 'config_key' => $key],
            ['config_value' => $value]
        );

        $branchCacheKey = $branchId ?: 'global';
        \Illuminate\Support\Facades\Cache::forget("config_{$companyId}_{$branchCacheKey}_{$key}");
    }
}

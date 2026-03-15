<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'config_key', 'config_value'];

    /**
     * Get a config value for the current company.
     */
    public static function getFor(string $key, $default = null)
    {
        $companyId = auth()->user()->company_id ?? null;
        if (!$companyId) return $default;

        $config = static::where('company_id', $companyId)
            ->where('config_key', $key)
            ->first();

        return $config ? $config->config_value : $default;
    }

    /**
     * Set a config value for the current company.
     */
    public static function setFor(string $key, $value): void
    {
        $companyId = auth()->user()->company_id;

        static::updateOrCreate(
            ['company_id' => $companyId, 'config_key' => $key],
            ['config_value' => $value]
        );
    }
}

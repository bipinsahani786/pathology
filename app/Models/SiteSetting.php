<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value', 'setting_group'];

    /**
     * Get a site setting value with caching.
     */
    public static function get(string $key, $default = null): ?string
    {
        return Cache::remember("site_setting.{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('setting_key', $key)->first();
            return $setting ? $setting->setting_value : $default;
        });
    }

    /**
     * Set a site setting value and bust cache.
     */
    public static function set(string $key, $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value, 'setting_group' => $group]
        );
        Cache::forget("site_setting.{$key}");
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return static::where('setting_group', $group)
            ->pluck('setting_value', 'setting_key')
            ->toArray();
    }
}

<?php

namespace Modules\Invoices\App\Helpers;

use Modules\Invoices\App\Models\InvoiceSetting;
use Illuminate\Support\Facades\Cache;

class InvoiceHelper
{
    /**
     * Get all invoice settings.
     * Caches the result to avoid redundant database queries.
     *
     * @return \Modules\Invoices\App\Models\InvoiceSetting
     */
    public static function getSettings()
    {
        // Cache settings for 1 hour (3600 seconds)
        // If settings don't exist yet, return an empty model to avoid null errors.
        return Cache::remember('invoice_settings', 3600, function () {
            return InvoiceSetting::first() ?? new InvoiceSetting();
        });
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSetting($key, $default = null)
    {
        $settings = self::getSettings();
        return $settings->{$key} ?? $default;
    }

    /**
     * Clear the invoice settings cache.
     * Should be called after saving or updating settings.
     */
    public static function clearCache()
    {
        Cache::forget('invoice_settings');
    }
}

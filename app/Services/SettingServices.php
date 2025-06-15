<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SettingServices
{
    /**
     * Load settings from the database and set them into Laravel config.
     */
    public function loadSettingsFromDatabase(): void
    {
        if (!Schema::hasTable('settings')) {
            $this->applyFallbackSettings('settings table not found');
            return;
        }

        try {
            // Optional: Use cache to avoid querying every request
            $settings = Cache::remember('app_settings', 60, function () {
                return Setting::where('add_to_env', true)->pluck('value', 'key')->toArray();
            });

            foreach ($settings as $key => $value) {
                config()->set($key, $value); // ✅ هذا هو الصحيح
            }

            $this->validateCriticalSettings();

        } catch (\Exception $e) {
            Log::error('فشل تحميل الإعدادات: ' . $e->getMessage());
            $this->applyFallbackSettings('exception while loading');
        }
    }

    /**
     * Ensure critical configs are loaded, otherwise use fallback.
     */
    protected function validateCriticalSettings(): void
    {
        // Check Pusher keys
        if (
            empty(config('broadcasting.connections.pusher.key')) ||
            empty(config('broadcasting.connections.pusher.secret')) ||
            empty(config('broadcasting.connections.pusher.app_id'))
        ) {
            config()->set('broadcasting.default', 'log');
        }

        // Check SMTP
        if (
            empty(config('mail.mailers.smtp.host')) ||
            empty(config('mail.mailers.smtp.username')) ||
            empty(config('mail.mailers.smtp.password'))
        ) {
            config()->set('mail.default', 'log');
        }

        // Check Mail From
        if (
            empty(config('mail.from.address')) ||
            empty(config('mail.from.name'))
        ) {
            config()->set('mail.from.address', 'fallback@example.com');
            config()->set('mail.from.name', 'Fallback App');
        }
    }

    /**
     * Apply fallback config values in case of failure.
     */
    protected function applyFallbackSettings(string $reason): void
    {
        Log::warning("تفعيل fallback settings بسبب: {$reason}");

        $fallbacks = [
            'broadcasting.default' => 'log',
            'mail.default' => 'log',
            'mail.from.address' => 'fallback@example.com',
            'mail.from.name' => 'Fallback App',
        ];

        foreach ($fallbacks as $key => $value) {
            config()->set($key, $value);
        }
    }

    /**
     * Get a reference to the global Laravel config.
     */

}

<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */


    public function register()
    {
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
        if (Schema::hasTable('settings')) {
            try {
                $settings = Setting::where('add_to_env', true)->get();

                foreach ($settings as $setting) {
                    Config::set($setting->key, $setting->value);
                }

                // ✅ حماية إضافية: إذا إعدادات Pusher ناقصة، غيّر قناة البث إلى log
                if (
                    empty(config('broadcasting.connections.pusher.key')) ||
                    empty(config('broadcasting.connections.pusher.secret')) ||
                    empty(config('broadcasting.connections.pusher.app_id'))
                ) {
                    config()->set('broadcasting.default', 'log');
                }

            } catch (\Exception $e) {
                Log::error('فشل تحميل الإعدادات من قاعدة البيانات: ' . $e->getMessage());
            }
        }


        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
    }
}

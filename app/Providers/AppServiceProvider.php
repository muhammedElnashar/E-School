<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\SettingServices;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot(SettingServices $settingsService)
    {
        $settingsService->loadSettingsFromDatabase();

        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

    }
}

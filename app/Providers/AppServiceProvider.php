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
    public function register() {
//        $this->renderable(function (NotFoundHttpException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Record not found.'
//                ], 404);
//            }
//        });
//        $this->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
//            if ($request->is('api/*')) {
//                return response()->json([
//                    'message' => 'Not authenticated'
//                ], 401);
//            }
//        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        //
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();
        if (Schema::hasTable('settings')) {
            try {
                foreach (Setting::pluck('value', 'key') as $key => $value) {
                    Config::set($key, $value);
                }
            } catch (\Exception $e) {
                Log::error('Failed loading settings: ' . $e->getMessage());
            }
        }
    }
}

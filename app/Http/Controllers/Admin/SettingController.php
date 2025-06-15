<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\SettingServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $settings = Setting::paginate(10);

        return view('admin.settings.index',compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key',
            'value' => 'required|string',
            'add_to_env' => 'required|boolean',
        ]);

        Setting::create($request->only(['key', 'value','add_to_env']));

        return redirect()->route('settings.index')->with('success', 'Setting created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     */
    public function update(Request $request, Setting $setting, SettingServices $settingService)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'required|string',
            'add_to_env' => 'required|boolean',
        ]);

        // 1. تحديث الإعداد في قاعدة البيانات
        $setting->update($request->only(['key', 'value', 'add_to_env']));

        // 2. مسح كاش الإعدادات
        Cache::forget('app_settings');

        // 3. تحميل الإعدادات الجديدة إلى config()
        $settingService->loadSettingsFromDatabase();
        return redirect()->route('settings.index')->with('success', 'تم تحديث الإعداد وتفعيله فورًا.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     */
    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')->with('success', 'Setting deleted successfully.');
    }

    public function updateEnvFromSettings()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        return redirect()->back()->with('success', '.env تم تحديثه من الإعدادات المختارة بنجاح.');
    }

}

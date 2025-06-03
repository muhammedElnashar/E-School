<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $settings = Setting::paginate(5);

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
        ]);

        Setting::create($request->only(['key', 'value']));

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
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'key' => 'required|string|max:255|unique:settings,key,' . $setting->id,
            'value' => 'required|string',
        ]);

        $setting->update($request->only(['key', 'value']));

        return redirect()->route('settings.index')->with('success', 'Setting updated successfully.');
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
}

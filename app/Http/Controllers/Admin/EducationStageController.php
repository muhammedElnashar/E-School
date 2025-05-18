<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationStage;
use Illuminate\Http\Request;

class EducationStageController extends Controller
{
    public function index(Request $request)
    {
        $educationStages = EducationStage::orderByDesc('id')->paginate(10);
        return view('admin.education_stages.index', compact('educationStages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:education_stages'
        ]);

        EducationStage::create($request->only('name'));

        return redirect()->route('stages.index')->with('success', 'تم إنشاء المرحلة بنجاح.');
    }

    public function edit($id)
    {
        $stage = EducationStage::findOrFail($id);
        return response()->json($stage);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:education_stages,name,' . $id
        ]);

        $stage = EducationStage::findOrFail($id);
        $stage->update(['name' => $request->name]);

        return redirect()->route('stages.index')->with('success', 'تم تعديل المرحلة بنجاح.');
    }

    public function destroy($id)
    {
        EducationStage::findOrFail($id)->delete();
        return redirect()->route('stages.index')->with('success', 'تم حذف المرحلة بنجاح.');
    }
}

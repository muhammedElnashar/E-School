<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationStage;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderByDesc('id')->paginate(2);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:subjects']);
        Subject::create($request->only('name'));
        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }
    public function show($id)
    {
      //
    }
    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json($subject);

    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|unique:subjects,name,' . $id]);
        $subject = Subject::findOrFail($id);
        $subject->update(['name' => $request->name]);
        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
    }

    public function stagesManagement()
    {
        $subjects = Subject::with('stages')->get();
        return view('admin.subjects.add_education-stage', compact('subjects'));
    }

    public function getStages($subjectId)
    {
        $subject = Subject::with('stages')->findOrFail($subjectId);
        $allStages = EducationStage::all();

        return response()->json([
            'allStages' => $allStages,
            'selectedStages' => $subject->stages->pluck('id')->toArray(),
        ]);
    }

    public function syncStages(Request $request, $subjectId)
    {
        $request->validate([
            'stages' => 'array',
            'stages.*' => 'exists:education_stages,id',
        ]);

        $subject = Subject::findOrFail($subjectId);
        $subject->stages()->sync($request->stages ?? []);

        return response()->json(['message' => 'Stages updated successfully']);
    }
}

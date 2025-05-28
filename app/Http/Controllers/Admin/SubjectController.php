<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationStage;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderByDesc('id')->paginate(2);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
      $data=  $request->validate([
            'name' => 'required|unique:subjects',
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')){
            $path= $request->file('image')->store('images/subjects', 'images');
            $data['image'] = $path;
        }

        Subject::create($data);
        return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
    }
    public function show($id)
    {
      //
    }
    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return response()->json([
            'id' => $subject->id,
            'name' => $subject->name,
            'image_url' => asset('storage/' . $subject->image),
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|unique:subjects,name,' . $id,
            'image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

        $subject = Subject::findOrFail($id);
        if ($request->hasFile('image')){
            if ($subject->image){
                Storage::disk('images')->delete($subject->image);
            }
            $path= $request->file('image')->store('images/subjects', 'public');
            $data['image'] = $path;
        }
        $subject->update($data);
        return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
    }

    public function destroy($id)
    {
       $subject= Subject::findOrFail($id);
        if ($subject->image){
            Storage::disk('images')->delete($subject->image);
        }
        $subject->delete();
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
    // For Packages
    public function getRelatedStages($subjectId)
    {
        $subject = Subject::findOrFail($subjectId);
        $stages = $subject->stages()->select('education_stages.id', 'education_stages.name')->get();
        $stages->makeHidden('pivot');
        return response()->json($stages);
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

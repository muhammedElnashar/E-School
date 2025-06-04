<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherSubjectRequest;
use App\Http\Resources\TeacherSubjectResource;
use App\Models\Subject;
use App\Models\TeacherSubject;
use App\Models\User;

class TeacherSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */

    public function index(User $teacher)
    {
        $subjects = TeacherSubject::where('teacher_id',$teacher->id)->paginate(5);
        return view('admin.teachers.teacher-subjects',compact("subjects",'teacher'));
    }

    public function create($teacherId)
    {
        $teacher = User::findOrFail($teacherId);
        $subjects = Subject::all();

        return view('admin.teachers.teacher-subjects-create', compact('teacher', 'subjects'));

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(StoreTeacherSubjectRequest $request)
    {
            $data = $request->validated();
            $teacher_id = $data['teacher_id'];
            TeacherSubject::create($data);
            return redirect()->route('teacher.subject.index', $teacher_id)
                ->with('success', 'Subject assigned to teacher successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update()
    {
      //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     */
    public function destroy($id)
    {
            $subject = TeacherSubject::find($id);
            if (!$subject){
                return response()->json(['status' => 'error', 'message' => 'Subject not found'], 404);
            }
            $subject->delete();
            return redirect()->back()
                ->with('success', 'Subject removed from teacher successfully.');


    }
}

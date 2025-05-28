<?php

namespace App\Http\Controllers\TeacherApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentStudent;
use App\Models\LessonStudent;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
/*    public function store(StoreAssignmentRequest $request)
    {
        $teacher = auth()->user();
        $data = $request->validated();

        // جلب الطلاب المسجلين في الحصة
        $studentIds = LessonStudent::where('lesson_occurrence_id', $data['lesson_occurrence_id'])
            ->pluck('student_id');

        if ($studentIds->isEmpty()) {
            return response()->json(['message' => 'لا يوجد طلاب مسجلين في هذه الحصة.'], 400);
        }

        // تحميل الملف إذا وجد
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("assignments/lesson_{$data['lesson_occurrence_id']}", 'public');
        }

        $assignment = Assignment::create([
            'teacher_id' => $teacher->id,
            'lesson_occurrence_id' => $data['lesson_occurrence_id'],
            'text' => $data['text'] ?? null,
            'file_path' => $filePath,
        ]);

        $assignmentStudents = $studentIds->map(fn($studentId) => [
            'assignment_id' => $assignment->id,
            'student_id' => $studentId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AssignmentStudent::insert($assignmentStudents->toArray());

        return response()->json(['message' => 'تم إنشاء الواجب وإرساله بنجاح.']);
    }*/
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

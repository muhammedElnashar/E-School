<?php

namespace App\Http\Controllers\TeacherApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\AssignmentStudent;
use App\Models\LessonStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $teacher = auth()->user();
        $assignments = Assignment::where('teacher_id', $teacher->id)
            ->get();

        if ($assignments->isEmpty()) {
            return response()->json(['message' => 'No Assignments Found.'], 404);
        }

        return response()->json($assignments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(StoreAssignmentRequest $request)
    {
        $teacher = auth()->user();
        $data = $request->validated();

        $studentIds = LessonStudent::where('lesson_occurrence_id', $data['lesson_occurrence_id'])
            ->pluck('student_id');

        if ($studentIds->isEmpty()) {
            return response()->json(['message' => ' No Student Assign To This Lesson .'], 400);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("assignments", 'files');
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

        return response()->json(['message' => 'Assignment Created Successfully.']);
    }
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
     */
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $teacher = auth()->user();

        if ($assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();

        if ($request->hasFile('file')) {
            if ($assignment->file_path && Storage::disk('files')->exists($assignment->file_path)) {
                Storage::disk('files')->delete($assignment->file_path);
            }

            $data['file_path'] = $request->file('file')->store("assignments", 'files');
        }

        $assignment->update([
            'text' => $data['text'] ?? $assignment->text,
            'file_path' => $data['file_path'] ?? $assignment->file_path,
        ]);

        return response()->json(['message' => 'Assignment Updated Successfully.']);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Assignment $assignment)
    {
        $teacher = auth()->user();

        if ($assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($assignment->file_path && Storage::disk('files')->exists($assignment->file_path)) {
            Storage::disk('files')->delete($assignment->file_path);
        }

        $assignment->delete();

        return response()->json(['message' => 'Assignment Deleted Successfully.']);
    }



}

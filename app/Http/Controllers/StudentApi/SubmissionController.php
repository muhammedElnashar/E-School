<?php

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Http\Resources\AssignmentsResource;
use App\Http\Resources\SubmissionsResource;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function getAllAssignments()
    {
        $student = auth()->user();
        $assignments = Assignment::whereHas('occurrence.students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => AssignmentsResource::collection($assignments)
        ]);

    }
    public function store(StoreSubmissionRequest $request)
    {

        $student = auth()->user();
        $data = $request->validated();

        $assignment = Assignment::findOrFail($data['assignment_id']);

        $isStudentInLesson = \App\Models\LessonStudent::where('lesson_occurrence_id', $assignment->lesson_occurrence_id)
            ->where('student_id', $student->id)
            ->exists();

        if (! $isStudentInLesson) {
            return response()->json(['message' => 'You are not assigned to this lesson.'], 403);
        }

        $existing = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already submitted this assignment.'], 409);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store("submissions/assignment_{$assignment->id}", 'files');
        }

        Submission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'text' => $data['text'] ?? null,
            'file_path' => $filePath,
        ]);

        return response()->json(['message' => 'Submission created successfully.']);
    }
    public function AllStudentSubmissions()
    {
        $student = auth()->user();
        $submissions = Submission::where('student_id', $student->id)->get();
        return response()->json([
            'status' => 'success',
            'data'=> SubmissionsResource::collection($submissions)
        ]);
    }

    public function destroy($id)
    {
        $student = auth()->user();
        $submission = Submission::where('student_id', $student->id)
            ->where('id', $id)
            ->first();
        if (! $submission) {
            return response()->json(['message' => 'Submission not found.'], 404);
        }

        if ($submission->file_path && Storage::disk('files')->exists($submission->file_path)) {
            Storage::disk('files')->delete($submission->file_path);
        }
        $submission->delete();

        return response()->json(['message' => 'Submission deleted successfully.']);
    }
}

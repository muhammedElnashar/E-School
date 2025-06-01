<?php

namespace App\Http\Controllers\StudentApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubmissionRequest;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
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
}

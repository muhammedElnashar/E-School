<?php

namespace App\Http\Controllers\TeacherApi;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function showAssignmentWithSubmissions($assignmentId)
    {
        $teacher = auth()->user();

        $assignment = Assignment::where('id', $assignmentId)
            ->where('teacher_id', $teacher->id)
            ->with(['submissions.student']) // eager load student with submissions
            ->first();

        return response()->json([
            'assignment' => [
                'id' => $assignment->id,
                'text' => $assignment->text,
                'file_path' => $assignment->file_path,
                'created_at' => $assignment->created_at->toDateTimeString(),
            ],
            'submissions' => $assignment->submissions->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'student' => [
                        'id' => $submission->student->id,
                        'name' => $submission->student->name,
                        'email' => $submission->student->email,
                    ],
                    'text' => $submission->text,
                    'file_path' => $submission->file_path,
                    'grade' => $submission->grade,
                    'teacher_note' => $submission->teacher_note,
                    'submitted_at' => $submission->created_at->toDateTimeString(),
                ];
            }),
        ]);
    }
    public function updateSubmission(Request $request, $submissionId)
    {
        $teacher = auth()->user();

        $submission = Submission::with('assignment')->findOrFail($submissionId);

        if ($submission->assignment->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $data = $request->validate([
            'grade' => 'nullable|integer|min:0|max:100',
            'teacher_note' => 'nullable|string|max:2000',
        ]);

        $submission->update($data);

        return response()->json(['message' => 'Submission updated successfully']);
    }

}

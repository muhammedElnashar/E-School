<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentsController extends Controller
{
    public function allAssignment()
    {
        $assignments = Assignment::paginate(5);
        return view('admin.assignments.index', compact('assignments'));
    }

    public function destroyAssignment($id)
    {
        $assignment = Assignment::findOrFail($id);
        if ($assignment->file_path && Storage::disk('files')->exists($assignment->file_path)) {
            Storage::disk('files')->delete($assignment->file_path);
        }
        foreach ($assignment->submissions as $submission) {
            if ($submission->file_path && Storage::disk('files')->exists($submission->file_path)) {
                Storage::disk('files')->delete($submission->file_path);
            }
        }
        $assignment->delete();
        return redirect()->back()->with('success', 'Assignment deleted successfully.');
    }

    public function getAllSubmissionsForAssignments($id)
    {
        $submissions = Submission::where('assignment_id', $id)->paginate(5);
        return view('admin.assignments.submissions', compact('submissions'));
    }
    public function destroySubmission($id)
    {
        $submission = Submission::findOrFail($id);
        if ($submission->file_path && Storage::disk('files')->exists($submission->file_path)) {
            Storage::disk('files')->delete($submission->file_path);
        }
        $submission->delete();
        return redirect()->back()->with('success', 'Submission deleted successfully.');
    }


}

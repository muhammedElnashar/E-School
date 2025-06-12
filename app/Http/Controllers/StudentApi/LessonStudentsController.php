<?php

namespace App\Http\Controllers\StudentApi;

use App\Enums\LessonType;
use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignAndCancelLessonRequest;
use App\Http\Resources\LessonOccurrenceResource;
use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\LessonStudent;
use App\Models\PackageUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LessonStudentsController extends Controller
{

    public function list()
    {
        $now = Carbon::now()->toDateString();

        $futureLessonOccurrences = LessonOccurrence::where('occurrence_date', '>', $now)
            ->with('lesson')
            ->orderBy('occurrence_date', 'asc')
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => LessonOccurrenceResource::collection($futureLessonOccurrences),
        ]);
    }

    public function assign(AssignAndCancelLessonRequest $request)
    {

        $data = $request->validated();

        $user = auth()->user();

        $lessonOccurrence = LessonOccurrence::with('lesson.subject', 'lesson.educationStage')
            ->findOrFail($data['lesson_occurrence_id']);

        $lesson = $lessonOccurrence->lesson;
        $sub_id = $lesson->subject->id;
        $edu_id = $lesson->educationStage->id ?? null;

        $purchased = $user->purchases()
            ->whereHas('marketplaceItem', function ($q) use ($sub_id, $edu_id) {
                $q->where('subject_id', $sub_id)
                    ->where(function ($q2) use ($edu_id) {
                        $q2->where('education_stage_id', $edu_id)
                            ->orWhereNull('education_stage_id');
                    });
            })
            ->where('remaining_credits', '>', 0)
            ->orderBy('activated_at', 'asc')
            ->first();

        if (!$purchased) {
            return response()->json(['message' => 'You do not have enough credits for this lesson.'], 403);
        }

        $alreadyAssigned = LessonStudent::where('lesson_occurrence_id', $lessonOccurrence->id)
            ->where('student_id', $user->id)
            ->exists();

        if ($alreadyAssigned) {
            return response()->json([
                "status" => "error",
                'message' => 'You are already assigned to this lesson.'], 409);
        }
        $studentCount = LessonStudent::where('lesson_occurrence_id', $lessonOccurrence->id)->count();
        if ($lesson->lesson_type->value === \App\Enums\LessonType::Individual->value) {
            if ($studentCount >= 1) {
                return response()->json([
                    "status" => "error",
                    'message' => 'This individual lesson is already booked by another student.'
                ], 403);
            }
        } elseif ($lesson->lesson_type->value === LessonType::Group->value) {
            if ($studentCount >= 5) {
                return response()->json([
                    "status" => "error",
                    'message' => 'This group lesson is fully booked. Maximum 5 students allowed.'
                ], 403);
            }
        }
        DB::transaction(function () use ($user, $lessonOccurrence, $purchased) {
            LessonStudent::create([
                'lesson_occurrence_id' => $lessonOccurrence->id,
                'student_id' => $user->id,
                'purchase_id' => $purchased->id,
            ]);
            PackageUsage::create([
                'purchase_id' => $purchased->id,
                'lesson_id' => $lessonOccurrence->lesson_id,
            ]);

            $purchased->decrement('remaining_credits');
        });

        return response()->json(['message' => 'Lesson booked successfully.']);
    }

    public function cancel($id)
    {
        $user = auth()->user();

        $lessonOccurrence = LessonOccurrence::find($id);
        if (!$lessonOccurrence) {
            return response()->json(['message' => 'Lesson occurrence not found.'], 404);
        }

        $lessonStudent = LessonStudent::with('lessonOccurrence', 'purchase') // تحميل العلاقات المطلوبة
        ->where('lesson_occurrence_id', $lessonOccurrence->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$lessonStudent) {
            return response()->json(['message' => 'You are not assigned to this lesson.'], 404);
        }

        $occurrenceDateTime = Carbon::parse($lessonOccurrence->occurrence_date . ' ' . $lessonOccurrence->start_time);
        if (now()->diffInHours($occurrenceDateTime, false) < 12) {
            return response()->json(['message' => 'You can only cancel a lesson at least 12 hours in advance.'], 403);
        }

        DB::transaction(function () use ($lessonStudent) {
            if ($lessonStudent->purchase) {
                $lessonStudent->purchase->increment('remaining_credits');
            }
            PackageUsage::where('purchase_id', $lessonStudent->purchase_id)
                ->where('lesson_id', $lessonStudent->lessonOccurrence->lesson_id)
                ->delete();

            $lessonStudent->delete();
        });

        return response()->json(['message' => 'Lesson cancelled successfully.']);
    }

    public function myUpcomingLessons()
    {
        $now = now();

        $upcomingLessons = LessonStudent::where('student_id', auth()->id())
            ->join('lesson_occurrences', 'lesson_students.lesson_occurrence_id', '=', 'lesson_occurrences.id')
            ->join('lessons', 'lesson_occurrences.lesson_id', '=', 'lessons.id')
            ->whereRaw("
            TIMESTAMP(lesson_occurrences.occurrence_date, TIME(lessons.start_datetime)) > ?
        ", [$now])
            ->orderByRaw("
            TIMESTAMP(lesson_occurrences.occurrence_date, TIME(lessons.start_datetime))
        ")
            ->with(['Occurrence.lesson'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $upcomingLessons,
        ]);
    }


}

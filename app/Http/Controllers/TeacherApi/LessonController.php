<?php

namespace App\Http\Controllers\TeacherApi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\LessonRecurrence;
use App\Services\ZoomService;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request)
    {

        DB::beginTransaction();

        try {
            $lesson = Lesson::create([
                'teacher_id' => auth()->id(),
                'subject_id' => $request->subject_id,
                'education_stage_id' => $request->education_stage_id,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'zoom_link' => $request->zoom_link ?? null,
                'lesson_type' => $request->lesson_type,
            ]);

            $weeksCount = $request->input('recurrence.weeks_count');
            $exceptionWeeks = $request->input('recurrence.exception_weeks', []);

            $exceptionDates = $this->calculateExceptionDates($lesson->start_datetime, $exceptionWeeks);

            LessonRecurrence::create([
                'lesson_id' => $lesson->id,
                'weeks_count' => $weeksCount,
                'exception_dates' => json_encode($exceptionDates),
            ]);

            for ($i = 0; $i < $weeksCount; $i++) {
                $occurrenceDate = \Carbon\Carbon::parse($lesson->start_datetime)->addWeeks($i)->format('Y-m-d');

                if (in_array($occurrenceDate, $exceptionDates)) {
                    continue;
                }

                LessonOccurrence::create([
                    'lesson_id' => $lesson->id,
                    'occurrence_date' => $occurrenceDate,
                    'zoom_link' => $lesson->zoom_link,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'lesson' => $lesson], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'خطأ أثناء إنشاء الحصة: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();

        try {
            $lesson->update([
                'subject_id' => $request->subject_id,
                'education_stage_id' => $request->education_stage_id,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'zoom_link' => $request->zoom_link,
                'lesson_type' => $request->lesson_type,
            ]);

            $lesson->recurrence()?->delete();
            $lesson->occurrences()->delete();

            $weeksCount = $request->input('recurrence.weeks_count');
            $exceptionWeeks = $request->input('recurrence.exception_weeks', []);
            $exceptionDates = $this->calculateExceptionDates($lesson->start_datetime, $exceptionWeeks);

            $lesson->recurrence()->create([
                'weeks_count' => $weeksCount,
                'exception_dates' => $exceptionDates,
            ]);

            $this->generateLessonOccurrences($lesson, $weeksCount, $exceptionDates);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Lesson Update Successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Update Failed' . $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        $lesson = Lesson::findOrFail($id);

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson Deleted Successfully.',
        ]);
    }



    protected function calculateExceptionDates($startDate, array $exceptionWeeks): array
    {
        $start = \Carbon\Carbon::parse($startDate);
        $exceptionDates = [];

        foreach ($exceptionWeeks as $weekNumber) {
            $date = $start->copy()->addWeeks($weekNumber - 1)->format('Y-m-d');
            $exceptionDates[] = $date;
        }

        return $exceptionDates;
    }

    protected function generateLessonOccurrences(Lesson $lesson, int $weeksCount, array $exceptionDates): void
    {
        $start = \Carbon\Carbon::parse($lesson->start_datetime);

        for ($i = 0; $i < $weeksCount; $i++) {
            $occurrenceDate = $start->copy()->addWeeks($i)->format('Y-m-d');
            if (!in_array($occurrenceDate, $exceptionDates)) {
                $lesson->occurrences()->create([
                    'occurrence_date' => $occurrenceDate,
                    'zoom_link' => $lesson->zoom_link,
                ]);
            }
        }
    }

}


<?php

namespace App\Http\Controllers\TeacherApi;

use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\LessonOccurrence;
use App\Models\LessonRecurrence;
use App\Services\Agora\RtcTokenBuilder2;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $lessons = Lesson::where('teacher_id', auth()->id())
            ->with(['recurrence', 'occurrences'])
            ->get();
        if ($lessons->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No lessons found.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'lessons' =>  $lessons,
        ]);
    }
    public function store(StoreLessonRequest $request)
    {
        DB::beginTransaction();

        try {
            $channelName = 'lesson_' . uniqid('', true);

            $lesson = Lesson::create([
                'teacher_id' => auth()->id(),
                'subject_id' => $request->subject_id,
                'education_stage_id' => $request->education_stage_id ?? null,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'lesson_type' => $request->lesson_type,
                'agora_channel_name' => $channelName,
            ]);

            $weeksCount = $request->input('recurrence.weeks_count');
            $exceptionWeeks = $request->input('recurrence.exception_weeks', []);
            $exceptionDates = $this->calculateExceptionDates($lesson->start_datetime, $exceptionWeeks, $weeksCount);

            LessonRecurrence::create([
                'lesson_id' => $lesson->id,
                'weeks_count' => $weeksCount,
                'exception_dates' => json_encode($exceptionDates),
            ]);

            $this->generateLessonOccurrences($lesson, $weeksCount, $exceptionDates);

            DB::commit();

            return response()->json([
                'success' => true,
                'lesson' => $lesson->load(['recurrence', 'occurrences']),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        if ($lesson->teacher_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::beginTransaction();

        try {
            $lesson->update([
                'subject_id' => $request->subject_id,
                'education_stage_id' => $request->education_stage_id,
                'start_datetime' => $request->start_datetime,
                'end_datetime' => $request->end_datetime,
                'lesson_type' => $request->lesson_type,
            ]);

            $lesson->recurrence()?->delete();
            $lesson->occurrences()->delete();

            $weeksCount = $request->input('recurrence.weeks_count');
            $exceptionWeeks = $request->input('recurrence.exception_weeks', []);
            $exceptionDates = $this->calculateExceptionDates($lesson->start_datetime, $exceptionWeeks, $weeksCount);

            $lesson->recurrence()->create([
                'weeks_count' => $weeksCount,
                'exception_dates' => json_encode($exceptionDates),
            ]);

            $this->generateLessonOccurrences($lesson, $weeksCount, $exceptionDates);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lesson updated successfully.',
                'lesson' => $lesson->load(['recurrence', 'occurrences']),

            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    public function destroy($id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                'success' => false,
                'message' => 'Lesson not found.',
            ], 404);
        }

        if ($lesson->teacher_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $lesson->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lesson deleted successfully.',
        ]);
    }

    protected function calculateExceptionDates($startDate, array $exceptionWeeks, int $weeksCount): array
    {
        $start = Carbon::parse($startDate);
        $exceptionDates = [];

        foreach ($exceptionWeeks as $weekNumber) {
            if (!is_numeric($weekNumber) || $weekNumber < 1 || $weekNumber > $weeksCount) {
                continue;
            }
            $date = $start->copy()->addWeeks($weekNumber - 1)->toDateString();
            $exceptionDates[] = $date;
        }

        return $exceptionDates;
    }

    protected function generateLessonOccurrences(Lesson $lesson, int $weeksCount, array $exceptionDates): void
    {
        $start = Carbon::parse($lesson->start_datetime);

        for ($i = 0; $i < $weeksCount; $i++) {
            $occurrenceDate = $start->copy()->addWeeks($i)->toDateString();

            if (!in_array($occurrenceDate, $exceptionDates)) {
                $lesson->occurrences()->create([
                    'occurrence_date' => $occurrenceDate,
                ]);
            }
        }
    }

    protected function generateAgoraToken(string $channelName, int $uid, int $expireTimeInSeconds = 3600): string
    {
        $appId = config('services.agora.app_id');
        $appCertificate = config('services.agora.app_certificate');
        $role = RtcTokenBuilder2::ROLE_PUBLISHER;
        $privilegeExpiredTs = time() + $expireTimeInSeconds;

        return RtcTokenBuilder2::buildTokenWithUid(
            $appId,
            $appCertificate,
            $channelName,
            $uid,
            $role,
            $privilegeExpiredTs
        );
    }

    public function joinLesson(Request $request, Lesson $lesson)
    {
        $user = auth()->user();
        $channelName = $lesson->agora_channel_name;
        $uid = $user->id;

        $token = $this->generateAgoraToken($channelName, $uid);

        return response()->json([
            'channel_name' => $channelName,
            'token' => $token,
            'uid' => $uid,
            'app_id' => config('services.agora.app_id'),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Helpers\ErrorHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminLessonRequest;
use App\Http\Requests\UpdateAdminLessonRequest;
use App\Models\Lesson;
use App\Models\LessonRecurrence;
use App\Models\User;
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
        $lessons = Lesson::paginate(5);
        $teachers = User::where('role',RoleEnum::Teacher->value)->get(); // <-- تأكد من وجود هذا

        return view('admin.lessons.index', compact('lessons','teachers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        $teachers = \App\Models\User::where('role',RoleEnum::Teacher->value)->get();
        return view('admin.lessons.create',compact('teachers'));
    }
    // Controller method to get teacher subjects
    public function getSubjects(User $teacher)
    {
        $subjects = $teacher->subjects()->distinct('subjects.id')->get(['subjects.id', 'subjects.name']);
        return response()->json($subjects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(StoreAdminLessonRequest $request)
    {
        DB::beginTransaction();

        try {
            $channelName = 'lesson_' . uniqid('', true);

            $lesson = Lesson::create([
                'teacher_id' => $request->teacher_id,
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

            return redirect()->route("lessons.index")->with('success', 'Lesson created successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(Lesson $lesson)
    {
        $teachers = User::whereRole('teacher')->get();
        $lesson->load('recurrence');

        return view('admin.lessons.edit', compact('lesson', 'teachers'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(UpdateAdminLessonRequest $request, Lesson $lesson)
    {
        DB::beginTransaction();

        try {
            $lesson->update([
                'teacher_id' => $request->teacher_id,
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

            return redirect()->route('lessons.index')->with('success', 'Lesson updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return ErrorHandler::handle($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return redirect()->route('lessons.index')->with('success', 'Lesson deleted successfully.');
    }

    public function occurrenceLesson(Lesson $lesson)
    {
        $occurrences = $lesson->occurrences()->orderBy('occurrence_date')->get();
        $recurrence = $lesson->recurrence;

        if (!$recurrence) {
            return redirect()->route('lessons.index')->with('error', 'This lesson does not have a recurrence.');
        }

        $exceptionDates = json_decode($recurrence->exception_dates, true);
        $weeksCount = $recurrence->weeks_count;

        return view('admin.lessons.occurrences', compact('lesson', 'occurrences', 'exceptionDates', 'weeksCount'));
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
}

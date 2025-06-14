<?php

namespace App\Http\Requests;

use App\Enums\Scopes;
use App\Models\Subject;
use App\Models\TeacherSubject;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $weeksCount = $this->input('recurrence.weeks_count', 0);

        return [
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'start_datetime' => ['required', 'date_format:Y-m-d H:i:s', 'before:end_datetime'],
            'end_datetime' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_datetime'],
            'lesson_type' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
            'recurrence.weeks_count' => ['required', 'integer', 'min:1'],
            'recurrence.exception_weeks' => ['nullable', 'array'],
            'recurrence.exception_weeks.*' => ['integer', 'min:1', 'max:' . $weeksCount],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $subjectId = $this->input('subject_id');
            $educationStageId = $this->input('education_stage_id');
            $start = $this->input('start_datetime');
            $end = $this->input('end_datetime');

            if ($subjectId && $educationStageId) {
                $subject = Subject::with('stages')->find($subjectId);
                if ($subject) {
                    $hasStage = $subject->stages->contains('id', $educationStageId);
                    if (!$hasStage) {
                        $validator->errors()->add('education_stage_id', 'Education Stage is not related to this Subject.');
                    }
                }
            }

            if ($start && $end) {
                try {
                    $startTime = Carbon::parse($start);
                    $endTime = Carbon::parse($end);

                    if ($endTime->diffInMinutes($startTime) > 180) {
                        $validator->errors()->add('end_datetime', 'The duration of the session should not exceed 3 hours.');
                    }

                    // حساب بداية ونهاية الأسبوع
                    $weekStart = $startTime->copy()->startOfWeek(); // بداية الأسبوع (الاثنين)
                    $weekEnd = $startTime->copy()->endOfWeek(); // نهاية الأسبوع (الأحد)

                    // جلب المستخدم الحالي كمدرس
                    $teacher = auth()->user();

                    if ($teacher) {
                        $countLessons = \App\Models\Lesson::where('teacher_id', $teacher->id)
                            ->whereBetween('start_datetime', [$weekStart, $weekEnd])
                            ->count();

                        // إذا تعديل حصة موجودة، استثني الحصة نفسها
                        if ($this->route('lesson')) {
                            $countLessons = \App\Models\Lesson::where('teacher_id', $teacher->id)
                                ->whereBetween('start_datetime', [$weekStart, $weekEnd])
                                ->where('id', '!=', $this->route('lesson'))
                                ->count();
                        }

                        if ($countLessons >= 7) {
                            $validator->errors()->add('teacher_id', 'A teacher cannot create more than 7 classes per week.');
                        }
                    }
                } catch (\Exception $e) {
                    // تجاهل الأخطاء هنا - سيتم التحقق من التنسيق الأساسي في rules()
                }
            }

            // تحقق أن المعلم مرتبط بالمادة
            $teacher = auth()->user();
            if ($subjectId && $teacher) {
                if (!$teacher->subjects()->where('subjects.id', $subjectId)->exists()) {
                    $validator->errors()->add('subject_id', 'Teacher is not assigned to this subject.');
                }
            }

            if ($subjectId && $educationStageId && $teacher) {
                $hasSubjectAndStage = TeacherSubject::where('teacher_id', $teacher->id)
                    ->where('subject_id', $subjectId)
                    ->where('education_stage_id', $educationStageId)
                    ->exists();

                if (!$hasSubjectAndStage) {
                    $validator->errors()->add('education_stage_id', 'This subject is not assigned to the teacher in this education stage.');
                }
            }
        });
    }



    public function failedValidation(Validator $validator): array
    {
        throw new HttpResponseException(response()->json([
            'message'=> 'Create Lesson Failed',
            'Validation_errors' => $validator->errors(),
        ], 422));
    }
}

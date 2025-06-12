<?php

namespace App\Http\Requests;

use App\Enums\Scopes;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UpdateAdminLessonRequest extends FormRequest
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
        return [
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'start_datetime' => ['required', 'date_format:Y-m-d\TH:i', 'before:end_datetime'],
            'end_datetime' => ['required', 'date_format:Y-m-d\TH:i', 'after:start_datetime'],
            'lesson_type' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
            'recurrence.weeks_count' => ['required', 'integer', 'min:1'],
            'recurrence.exception_weeks' => ['nullable', 'array'],
            'recurrence.exception_weeks.*' => ['integer', 'min:1'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            try {
                $start = Carbon::parse($this->start_datetime);
                $end = Carbon::parse($this->end_datetime);

                if ($end->diffInMinutes($start) > 180) {
                    $validator->errors()->add('end_datetime', 'The duration of the session should not exceed 3 hours.');
                }
            } catch (\Exception $e) {
                // تجاهل الخطأ، سيتم التحقق من التنسيق الأساسي في rules()
            }
        });
    }
}

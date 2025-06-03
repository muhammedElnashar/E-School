<?php

namespace App\Http\Requests;

use App\Enums\Scopes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminLessonRequest extends FormRequest
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
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'start_datetime' => ['required', 'date_format:Y-m-d\TH:i', 'before:end_datetime'],
            'end_datetime' => ['required', 'date_format:Y-m-d\TH:i', 'after:start_datetime'],
            'lesson_type' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],
            'recurrence.weeks_count' => ['nullable', 'integer', 'min:1'],
            'recurrence.exception_weeks' => ['nullable', 'array'],
            'recurrence.exception_weeks.*' => ['integer', 'min:1', 'max:' . $weeksCount],
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Enums\Scopes;
use App\Models\Subject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateLessonRequest extends FormRequest
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
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
            'start_datetime' => ['required', 'date_format:Y-m-d H:i:s', 'before:end_datetime'],
            'end_datetime' => ['required', 'date_format:Y-m-d H:i:s', 'after:start_datetime'],
            'zoom_link' => ['nullable', 'url'],
            'lesson_type' => ['required', Rule::in(array_column(Scopes::cases(), 'value'))],

            'recurrence.weeks_count' => ['required', 'integer', 'min:1'],
            'recurrence.exception_weeks' => ['nullable', 'array'],
            'recurrence.exception_weeks.*' => ['integer', 'min:1'],
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $subjectId = $this->input('subject_id');
            $educationStageId = $this->input('education_stage_id');

            if ($subjectId && $educationStageId) {
                $subject = Subject::with('stages')->find($subjectId);

                if ($subject) {
                    $hasStage = $subject->stages->contains('id', $educationStageId);
                    if (!$hasStage) {
                        $validator->errors()->add('education_stage_id', 'Education Stage is not related to this Subject.');
                    }
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

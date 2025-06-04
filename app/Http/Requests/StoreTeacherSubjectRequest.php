<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;
use App\Models\TeacherSubject;

class StoreTeacherSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'teacher_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'education_stage_id' => ['nullable', 'exists:education_stages,id'],
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $teacher_id = $this->input('teacher_id'); // <-- هذا هو التصحيح
            $subject_id = $this->input('subject_id');
            $education_stage_id = $this->input('education_stage_id');

            $exists = \App\Models\TeacherSubject::where('teacher_id', $teacher_id)
                ->where('subject_id', $subject_id)
                ->where(function ($query) use ($education_stage_id) {
                    if ($education_stage_id) {
                        $query->where('education_stage_id', $education_stage_id);
                    } else {
                        $query->whereNull('education_stage_id');
                    }
                })->exists();

            if ($exists) {
                $validator->errors()->add('subject_id', 'already added this Subject and Stage For This Teacher.');
            }
        });
    }



}

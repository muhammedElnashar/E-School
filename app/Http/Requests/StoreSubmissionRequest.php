<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
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
            'assignment_id' => ['required', 'exists:assignments,id'],
            'text' => ['nullable', 'string'],
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,jpg,jpeg,png,webp|max:10240',
        ];
    }

}

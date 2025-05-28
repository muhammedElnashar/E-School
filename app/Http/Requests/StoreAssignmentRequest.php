<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'lesson_occurrence_id' => 'required|exists:lesson_occurrences,id',
            'text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // Max 10MB
        ];
    }
}

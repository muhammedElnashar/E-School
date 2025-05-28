<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManualPurchasesRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'marketplace_item_id' => 'required|exists:marketplace_items,id',
            'price'=> 'required|numeric|min:0',
            'remaining_credits'=>'required|numeric|min:0',
        ];

    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'user_id.required' => 'The student field is required.',
            'user_id.exists' => 'The selected student does not exist.',
            'marketplace_item_id.required' => 'The marketplace item field is required.',
            'marketplace_item_id.exists' => 'The selected marketplace item does not exist.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'remaining_credits.required' => 'The remaining credits field is required.',
            'remaining_credits.numeric' => 'The remaining credits must be a number.',
            'remaining_credits.min' => 'The remaining credits must be at least 0.',
        ];
    }
}

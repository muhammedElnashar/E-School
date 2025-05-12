<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyResetPasswordOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'otp' => 'required|numeric|digits:6',
            'email' => 'required|email|exists:users,email',
        ];
    }
    public function messages()
    {
        return [
            'otp.required' => 'OTP is required',
            'otp.numeric' => 'OTP must be a number',
            'otp.digits' => 'OTP must be 6 digits',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.exists' => 'Email does not exist in our records',
        ];
    }
    public function failedValidation(Validator $validator): array
    {
        throw new HttpResponseException(response()->json([
            'message'=> 'Register Failed',
            'Validation_errors' => $validator->errors(),
        ], 422));
    }
}

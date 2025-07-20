<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserLoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required'],

            // Device Info
            'device_type' => 'nullable|string',
            'device_name' => 'nullable|string',
            'platform' => 'nullable|string',
            'browser' => 'nullable|string',
            'ip_address' => 'nullable|ip',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',

            'password.required' => 'حقل كلمة المرور مطلوب.',

            'device_type.string' => 'نوع الجهاز يجب أن يكون نصًا.',
            'device_name.string' => 'اسم الجهاز يجب أن يكون نصًا.',
            'platform.string' => 'نظام التشغيل يجب أن يكون نصًا.',
            'browser.string' => 'المتصفح يجب أن يكون نصًا.',
            'ip_address.ip' => 'عنوان IP غير صالح.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            ApiResponse::validationError($errors)
        );
    }
}

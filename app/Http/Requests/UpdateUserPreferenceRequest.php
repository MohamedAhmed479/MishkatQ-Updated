<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserPreferenceRequest extends FormRequest
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
            'current_level' => 'required|in:beginner,intermediate,advanced',
            'daily_minutes' => 'required|integer|min:1|max:1440',
            'sessions_per_day' => 'required|integer|min:1|max:5',
            'preferred_times' => 'required|array',
            'tafsir_id' => 'required|integer|exists:tafsirs,id',
        ];
    }

    public function messages(): array
    {
        return [
            'current_level.required' => 'مستوى الطالب مطلوب.',
            'current_level.in' => 'مستوى الطالب يجب أن يكون: مبتدئ، متوسط، أو متقدم.',

            'daily_minutes.required' => 'عدد الدقائق اليومية مطلوب.',
            'daily_minutes.integer' => 'عدد الدقائق اليومية يجب أن يكون رقمًا صحيحًا.',
            'daily_minutes.min' => 'الحد الأدنى لعدد الدقائق اليومية هو دقيقة واحدة.',
            'daily_minutes.max' => 'الحد الأقصى لعدد الدقائق اليومية هو 1440 دقيقة.',

            'sessions_per_day.required' => 'عدد الجلسات اليومية مطلوب.',
            'sessions_per_day.integer' => 'عدد الجلسات اليومية يجب أن يكون رقمًا صحيحًا.',
            'sessions_per_day.min' => 'يجب أن تحتوي الجلسات اليومية على جلسة واحدة على الأقل.',
            'sessions_per_day.max' => 'الحد الأقصى للجلسات اليومية هو 5 جلسات.',

            'preferred_times.required' => 'الوقت المفضل مطلوب.',
            'preferred_times.array' => 'الوقت المفضل يجب أن يكون في شكل مصفوفة.',

            'tafsir_id.required' => 'اختيار التفسير مطلوب.',
            'tafsir_id.integer' => 'رقم التفسير يجب أن يكون رقمًا صحيحًا.',
            'tafsir_id.exists' => 'التفسير المختار غير موجود في قاعدة البيانات.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        throw new HttpResponseException(
            ApiResponse::validationError($errors)
        );
    }
}

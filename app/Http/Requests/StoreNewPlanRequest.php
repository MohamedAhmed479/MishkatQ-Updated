<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNewPlanRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'surah_start_id' => 'required|integer|exists:chapters,id',
            'surah_end_id' => 'required|integer|exists:chapters,id|gte:surah_start_id',
            'start_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'name.string' => 'الاسم يجب أن يكون نصًا.',
            'name.max' => 'الاسم يجب ألا يتجاوز 255 حرفًا.',

            'surah_start_id.required' => 'رقم السورة الابتدائية مطلوب.',
            'surah_start_id.integer' => 'رقم السورة الابتدائية يجب أن يكون رقمًا صحيحًا.',
            'surah_start_id.exists' => 'السورة الابتدائية غير موجودة في قاعدة البيانات.',

            'surah_end_id.required' => 'رقم السورة النهائية مطلوب.',
            'surah_end_id.integer' => 'رقم السورة النهائية يجب أن يكون رقمًا صحيحًا.',
            'surah_end_id.exists' => 'السورة النهائية غير موجودة في قاعدة البيانات.',
            'surah_end_id.gte' => 'رقم السورة النهائية يجب أن يكون مساويًا أو أكبر من رقم السورة الابتدائية.',

            'start_date.required' => 'تاريخ البدء مطلوب.',
            'start_date.date' => 'تاريخ البدء يجب أن يكون تاريخًا صالحًا.',
            'start_date.after_or_equal' => 'تاريخ البدء يجب ألا يكون قبل تاريخ اليوم.',

            'description.string' => 'الوصف يجب أن يكون نصًا.',
            'description.max' => 'الوصف يجب ألا يتجاوز 1000 حرف.',
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

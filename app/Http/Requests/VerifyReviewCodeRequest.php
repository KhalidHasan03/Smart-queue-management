<?php

namespace App\Http\Requests;

use App\Support\ReviewCode;
use Illuminate\Foundation\Http\FormRequest;

class VerifyReviewCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('code') && is_string($this->input('code'))) {
            $this->merge(['code' => ReviewCode::normalize($this->input('code'))]);
        }

        if ($this->has('phone_last_4') && is_string($this->input('phone_last_4'))) {
            $this->merge([
                'phone_last_4' => preg_replace('/\D/', '', $this->input('phone_last_4')) ?? '',
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:'.ReviewCode::LENGTH],
            'phone_last_4' => ['required', 'string', 'size:4', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Please enter the review code from your slip.',
            'code.size' => 'The review code is '.ReviewCode::LENGTH.' characters. Please check your slip.',
            'phone_last_4.required' => 'Please enter the last 4 digits of your phone number.',
            'phone_last_4.size' => 'Enter exactly the last 4 digits of your phone number.',
            'phone_last_4.digits' => 'Enter exactly the last 4 digits of your phone number.',
        ];
    }
}

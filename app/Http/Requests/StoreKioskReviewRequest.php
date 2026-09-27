<?php

namespace App\Http\Requests;

use App\Models\Review;
use App\Support\ReviewSettings;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKioskReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:4'],
            'category' => ['nullable', Rule::in(Review::CATEGORIES)],
            'comment' => ['nullable', 'string', 'max:1000'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'is_anonymous' => ['sometimes', 'boolean'],
            'honeypot' => ['nullable', 'string', 'max:0'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $rating = (int) $this->input('rating');

                if ($rating < 1 || $rating > 4) {
                    return;
                }

                if (ReviewSettings::commentRequired($rating) && blank($this->input('comment'))) {
                    $validator->errors()->add(
                        'comment',
                        'Please tell us briefly what went wrong so we can improve.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please choose one of the four faces.',
            'rating.min' => 'Please choose one of the four faces.',
            'rating.max' => 'Please choose one of the four faces.',
        ];
    }
}

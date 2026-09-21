<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;

class StoreTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('serials.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('phone') && is_string($this->input('phone'))) {
            $this->merge(['phone' => Patient::normalizePhone($this->input('phone'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^\+?[0-9]{6,20}$/'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:500'],
            'service_id' => ['required', 'exists:services,id'],
            'doctor_id' => ['required', 'exists:doctors,id'],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::assignableRoles($this->user()))],
            'is_active' => ['sometimes', 'boolean'],
            'counter_id' => ['nullable', 'exists:counters,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
        ];
    }
}

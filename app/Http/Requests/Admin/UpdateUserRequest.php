<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::assignableRoles($this->user()))],
            'is_active' => ['sometimes', 'boolean'],
            'counter_id' => ['nullable', 'exists:counters,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'doctor_id' => ['nullable', 'exists:doctors,id'],
        ];
    }
}

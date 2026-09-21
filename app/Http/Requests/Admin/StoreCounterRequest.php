<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCounterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('counters.manage') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('counter')?->id;

        return [
            'name' => ['required', 'string', 'max:255', 'unique:counters,name,'.$id],
            'room_no' => ['nullable', 'string', 'max:50'],
            'service_id' => ['required', 'exists:services,id'],
            'is_active' => ['sometimes', 'boolean'],
            'show_on_display' => ['sometimes', 'boolean'],
        ];
    }
}

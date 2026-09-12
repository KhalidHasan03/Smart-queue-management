<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('services.manage') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('service')?->id;
        return [
            'name' => ['required', 'string', 'max:255', 'unique:services,name,'.$id],
            'prefix' => ['required', 'string', 'max:5', 'alpha', 'unique:services,prefix,'.$id],
            'start_number' => ['required', 'integer', 'min:1', 'max:9999'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('prefix')) {
            $this->merge(['prefix' => strtoupper($this->input('prefix'))]);
        }
    }
}

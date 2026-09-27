<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

        // Normalise the repeated service rows so validation and the service
        // layer always receive plain service/doctor pairs, whatever the browser
        // sent. Blank rows are dropped so an untouched "add another" row does
        // not fail the whole submission.
        $services = $this->input('services');
        if (is_array($services)) {
            $clean = [];
            foreach ($services as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $serviceId = $row['service_id'] ?? null;
                $doctorId = $row['doctor_id'] ?? null;
                if (($serviceId === null || $serviceId === '') && ($doctorId === null || $doctorId === '')) {
                    continue;
                }
                $clean[] = ['service_id' => $serviceId, 'doctor_id' => $doctorId];
            }
            $this->merge(['services' => array_values($clean)]);
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
            'services' => ['required', 'array', 'min:1', 'max:10'],
            'services.*' => ['array:service_id,doctor_id'],
            'services.*.service_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('services', 'id')->where('is_active', true),
            ],
            'services.*.doctor_id' => ['required', 'integer'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                foreach ($this->servicePairs() as $index => $pair) {
                    $key = "services.$index.doctor_id";

                    if ($validator->errors()->has("services.$index.service_id")
                        || $validator->errors()->has($key)) {
                        continue;
                    }

                    // The doctor must be active and belong to the service chosen
                    // in the same row. Checked here rather than in a Rule::exists
                    // closure because the row's service id is user input and
                    // cannot be interpolated into a subquery safely.
                    $matches = DB::table('doctors')
                        ->where('id', $pair['doctor_id'])
                        ->where('service_id', $pair['service_id'])
                        ->where('is_active', true)
                        ->exists();

                    if (! $matches) {
                        $validator->errors()->add(
                            $key,
                            'Each doctor must be active and belong to the service selected in the same row.'
                        );
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'services.required' => 'Select at least one service.',
            'services.*.service_id.distinct' => 'The same service cannot be selected twice.',
            'services.*.service_id.exists' => 'One or more selected services are unavailable.',
        ];
    }

    /**
     * @return array<int, array{service_id: int, doctor_id: int}>
     */
    public function servicePairs(): array
    {
        $services = $this->input('services', []);

        if (! is_array($services)) {
            return [];
        }

        $pairs = [];
        foreach (array_values($services) as $row) {
            if (! is_array($row) || ! isset($row['service_id'], $row['doctor_id'])) {
                continue;
            }
            if ($row['service_id'] === '' || $row['doctor_id'] === '') {
                continue;
            }
            $pairs[] = [
                'service_id' => (int) $row['service_id'],
                'doctor_id' => (int) $row['doctor_id'],
            ];
        }

        return $pairs;
    }
}

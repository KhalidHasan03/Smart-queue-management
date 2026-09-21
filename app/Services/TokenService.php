<?php

namespace App\Services;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TokenService
{
    public function issue(array $patientData, int $serviceId, int $doctorId, ?int $userId = null): Token
    {
        return DB::transaction(function () use ($patientData, $serviceId, $doctorId, $userId) {
            $service = Service::where('is_active', true)->lockForUpdate()->find($serviceId)
                ?? throw ValidationException::withMessages(['service_id' => 'Invalid or inactive service.']);

            $doctor = Doctor::where('is_active', true)->where('service_id', $service->id)->firstWhere('id', $doctorId)
                ?? throw ValidationException::withMessages(['doctor_id' => 'Doctor does not belong to this service.']);

            $counter = Counter::where('is_active', true)->where('service_id', $service->id)->orderBy('id')->first()
                ?? throw ValidationException::withMessages(['service_id' => 'No active counter for this service.']);

            $phone = Patient::normalizePhone($patientData['phone'] ?? '');
            if ($phone === '') {
                throw ValidationException::withMessages(['phone' => 'Invalid phone number.']);
            }
            $patientData['phone'] = $phone;

            $patient = Patient::where('phone', $phone)->lockForUpdate()->first();
            if ($patient) {
                if (mb_strtolower(trim($patient->name)) !== mb_strtolower(trim($patientData['name'] ?? ''))) {
                    throw ValidationException::withMessages([
                        'phone' => 'This contact number already exists for another patient ('.$patient->name.').',
                    ]);
                }
            } else {
                try {
                    $patient = Patient::create($patientData);
                } catch (QueryException $e) {
                    $existing = Patient::where('phone', $phone)->lockForUpdate()->first();
                    if (! $existing) {
                        throw $e;
                    }
                    if (mb_strtolower(trim($existing->name)) !== mb_strtolower(trim($patientData['name'] ?? ''))) {
                        throw ValidationException::withMessages([
                            'phone' => 'This contact number already exists for another patient ('.$existing->name.').',
                        ]);
                    }
                    $patient = $existing;
                }
            }

            $today = Carbon::today()->toDateString();
            $maxSeq = Token::whereDate('token_date', $today)->where('service_id', $service->id)->lockForUpdate()->max('seq');
            $seq = $maxSeq ? $maxSeq + 1 : $service->start_number;
            $tokenNo = $service->prefix.'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);

            return Token::create([
                'token_date' => $today,
                'seq' => $seq,
                'token_no' => $tokenNo,
                'service_id' => $service->id,
                'doctor_id' => $doctor->id,
                'counter_id' => $counter->id,
                'patient_id' => $patient->id,
                'status' => Token::WAITING,
                'created_by' => $userId,
            ]);
        });
    }
}

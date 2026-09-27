<?php

namespace App\Services;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use App\Support\ReviewCode;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TokenService
{
    /**
     * Issue a single token. Kept as the one-token primitive so existing callers
     * are unaffected; the multi-service path uses issueMany() instead.
     */
    public function issue(array $patientData, int $serviceId, int $doctorId, ?int $userId = null): Token
    {
        return $this->issueMany($patientData, [['service_id' => $serviceId, 'doctor_id' => $doctorId]], $userId)->first();
    }

    /**
     * Issue one token per service/doctor pair for a single patient in a single
     * atomic transaction: either every requested token lands or none does.
     *
     * @param  array<int, array{service_id: int, doctor_id: int}>  $pairs
     * @return Collection<int, Token>
     */
    public function issueMany(array $patientData, array $pairs, ?int $userId = null): Collection
    {
        if ($pairs === []) {
            throw ValidationException::withMessages(['services' => 'Select at least one service.']);
        }

        return DB::transaction(function () use ($patientData, $pairs, $userId) {
            $serviceIds = array_values(array_unique(array_map(
                fn (array $pair): int => (int) $pair['service_id'],
                $pairs
            )));
            sort($serviceIds);

            // Lock every affected service row up front, in a deterministic
            // ascending order. One issue() used to lock exactly one row, so
            // cross-service deadlock was impossible; a batch that locked them
            // in caller-supplied order could deadlock against a concurrent
            // batch holding the same set in the opposite order. Ascending order
            // makes every batch acquire the same sequence, so they queue.
            $services = Service::whereIn('id', $serviceIds)
                ->where('is_active', true)
                ->lockForUpdate()
                ->orderBy('id')
                ->get()
                ->keyBy('id');

            $missing = array_diff($serviceIds, $services->keys()->all());
            if ($missing !== []) {
                throw ValidationException::withMessages(['services' => 'One or more selected services are unavailable.']);
            }

            $patient = $this->resolvePatient($patientData);
            $today = Carbon::today()->toDateString();

            $tokens = new Collection;

            foreach ($pairs as $pair) {
                $tokens->push($this->issueForService(
                    $services->get((int) $pair['service_id']),
                    (int) $pair['doctor_id'],
                    $patient,
                    $today,
                    $userId
                ));
            }

            return $tokens;
        });
    }

    /**
     * Find or create the patient for this phone number, enforcing that an
     * existing contact number is never silently reassigned to another name.
     */
    private function resolvePatient(array $patientData): Patient
    {
        $phone = Patient::normalizePhone($patientData['phone'] ?? '');
        if ($phone === '') {
            throw ValidationException::withMessages(['phone' => 'Invalid phone number.']);
        }
        $patientData['phone'] = $phone;

        $patient = Patient::where('phone', $phone)->lockForUpdate()->first();
        if ($patient) {
            $this->assertSamePatient($patient, $patientData);

            return $patient;
        }

        try {
            return Patient::create($patientData);
        } catch (QueryException $e) {
            $existing = Patient::where('phone', $phone)->lockForUpdate()->first();
            if (! $existing) {
                throw $e;
            }
            $this->assertSamePatient($existing, $patientData);

            return $existing;
        }
    }

    private function assertSamePatient(Patient $patient, array $patientData): void
    {
        if (mb_strtolower(trim($patient->name)) !== mb_strtolower(trim($patientData['name'] ?? ''))) {
            throw ValidationException::withMessages([
                'phone' => 'This contact number already exists for another patient ('.$patient->name.').',
            ]);
        }
    }

    private function issueForService(Service $service, int $doctorId, Patient $patient, string $today, ?int $userId): Token
    {
        $doctor = Doctor::where('is_active', true)
            ->where('service_id', $service->id)
            ->firstWhere('id', $doctorId)
            ?? throw ValidationException::withMessages(['services' => 'Doctor does not belong to the selected service.']);

        $counter = Counter::where('is_active', true)
            ->where('service_id', $service->id)
            ->orderBy('id')
            ->first()
            ?? throw ValidationException::withMessages(['services' => 'No active counter for the selected service.']);

        $maxSeq = Token::where('token_date', $today)
            ->where('service_id', $service->id)
            ->lockForUpdate()
            ->max('seq');
        $seq = $maxSeq !== null ? $maxSeq + 1 : $service->start_number;
        $tokenNo = $service->prefix.'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);

        return Token::create([
            'token_date' => $today,
            'seq' => $seq,
            'token_no' => $tokenNo,
            'review_code' => ReviewCode::generate(),
            'service_id' => $service->id,
            'doctor_id' => $doctor->id,
            'counter_id' => $counter->id,
            'patient_id' => $patient->id,
            'status' => Token::WAITING,
            'created_by' => $userId,
        ]);
    }
}

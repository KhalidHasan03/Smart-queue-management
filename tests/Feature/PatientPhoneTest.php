<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Services\TokenService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PatientPhoneTest extends TestCase
{
    use RefreshDatabase;

    private function issueAs(string $name, string $phone): Token
    {
        $this->seed(DatabaseSeeder::class);
        $service = Service::firstOrFail();
        $doctor = Doctor::where('service_id', $service->id)->firstOrFail();
        $admin = User::where('email', 'admin@queuecare.local')->firstOrFail();

        return (new TokenService)->issue(
            ['name' => $name, 'phone' => $phone],
            $service->id,
            $doctor->id,
            $admin->id
        );
    }

    public function test_same_name_and_phone_reuses_patient_record(): void
    {
        $t1 = $this->issueAs('Karim Uddin', '01700000011');
        $t2 = (new TokenService)->issue(
            ['name' => 'Karim Uddin', 'phone' => '01700000011'],
            $t1->service_id,
            $t1->doctor_id,
            $t1->created_by
        );

        $this->assertSame($t1->patient_id, $t2->patient_id);
        $this->assertSame(1, Patient::where('phone', '01700000011')->count());
        $this->assertNotSame($t1->token_no, $t2->token_no);
        $this->assertSame('Karim Uddin', $t1->fresh()->patient->name);
    }

    public function test_same_phone_with_different_name_is_rejected(): void
    {
        $t1 = $this->issueAs('Karim Uddin', '01700000055');

        try {
            (new TokenService)->issue(
                ['name' => 'Rahim Uddin', 'phone' => '01700000055'],
                $t1->service_id,
                $t1->doctor_id,
                $t1->created_by
            );
            $this->fail('Expected number-already-exists rejection.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('already exists', collect($e->errors())->flatten()->first());
        }

        $this->assertSame('Karim Uddin', $t1->fresh()->patient->name);
        $this->assertSame(1, Patient::where('phone', '01700000055')->count());
    }

    public function test_phone_formats_normalize_to_same_patient(): void
    {
        $t1 = $this->issueAs('Ayesha Begum', '01700000022');
        $service = Service::firstOrFail();
        $doctor = Doctor::where('service_id', $service->id)->firstOrFail();
        $t2 = (new TokenService)->issue(
            ['name' => 'ayesha begum', 'phone' => '01 700-000022'],
            $service->id,
            $doctor->id,
            $t1->created_by
        );

        $this->assertSame($t1->patient_id, $t2->patient_id);
        $this->assertSame('Ayesha Begum', $t1->fresh()->patient->name);
    }

    public function test_duplicate_phone_rejected_at_database(): void
    {
        $this->seed(DatabaseSeeder::class);
        Patient::create(['name' => 'X', 'phone' => '01700000033']);
        $this->expectException(QueryException::class);
        Patient::create(['name' => 'Y', 'phone' => '01700000033']);
    }

    public function test_patient_found_by_phone_with_history(): void
    {
        $t1 = $this->issueAs('History Guy', '01700000044');
        $found = Patient::where('phone', '01700000044')->firstOrFail();

        $this->assertSame('History Guy', $found->name);
        $this->assertSame(1, $found->tokens()->count());
        $this->assertSame($t1->id, $found->tokens()->first()->id);
    }
}

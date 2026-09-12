<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\User;
use App\Services\TokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PatientPhoneTest extends TestCase
{
    use RefreshDatabase;

    private function issueAs(string $name, string $phone): \App\Models\Token
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $service = \App\Models\Service::firstOrFail();
        $doctor = \App\Models\Doctor::where('service_id', $service->id)->firstOrFail();
        $admin = User::where('email', 'admin@queuecare.local')->firstOrFail();

        return (new TokenService)->issue(
            ['name' => $name, 'phone' => $phone],
            $service->id,
            $doctor->id,
            $admin->id
        );
    }

    public function test_same_phone_reuses_patient_record(): void
    {
        $t1 = $this->issueAs('First Visit', '01700000011');
        $t2 = (new TokenService)->issue(
            ['name' => 'Second Visit', 'phone' => '01700000011'],
            $t1->service_id,
            $t1->doctor_id,
            $t1->created_by
        );

        $this->assertSame($t1->patient_id, $t2->patient_id);
        $this->assertSame(1, Patient::where('phone', '01700000011')->count());
        $this->assertNotSame($t1->token_no, $t2->token_no);
    }

    public function test_phone_formats_normalize_to_same_patient(): void
    {
        $t1 = $this->issueAs('A', '01700000022');
        $service = \App\Models\Service::firstOrFail();
        $doctor = \App\Models\Doctor::where('service_id', $service->id)->firstOrFail();
        $t2 = (new TokenService)->issue(
            ['name' => 'A', 'phone' => '01 700-000022'],
            $service->id,
            $doctor->id,
            $t1->created_by
        );

        $this->assertSame($t1->patient_id, $t2->patient_id);
    }

    public function test_duplicate_phone_rejected_at_database(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        Patient::create(['name' => 'X', 'phone' => '01700000033']);
        $this->expectException(\Illuminate\Database\QueryException::class);
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

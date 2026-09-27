<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Services\TokenService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MultiServiceTokenTest extends TestCase
{
    use RefreshDatabase;

    private User $receptionist;

    private Service $general;

    private Service $dental;

    private Doctor $generalDoctor;

    private Doctor $dentalDoctor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->receptionist = User::where('email', 'reception@queuecare.local')->firstOrFail();
        $this->general = Service::where('prefix', 'G')->firstOrFail();
        $this->dental = Service::where('prefix', 'D')->firstOrFail();
        $this->generalDoctor = Doctor::where('service_id', $this->general->id)->firstOrFail();
        $this->dentalDoctor = Doctor::where('service_id', $this->dental->id)->firstOrFail();
    }

    private function payload(array $services, array $overrides = []): array
    {
        return array_merge([
            'name' => 'Multi Patient',
            'phone' => '01700000099',
            'services' => $services,
        ], $overrides);
    }

    public function test_one_patient_can_take_several_services_in_one_submission(): void
    {
        $response = $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
            ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
        ]));

        $tokens = Token::with('service')->orderBy('id')->get();

        $response->assertRedirect(route('tokens.issued', ['tokens' => $tokens->pluck('id')->all()]));

        $this->assertCount(2, $tokens);
        $this->assertSame(1, Patient::where('phone', '01700000099')->count(), 'Patient record must be created once');
        $this->assertSame($tokens[0]->patient_id, $tokens[1]->patient_id, 'Both tokens belong to the same patient');
        $this->assertSame(['G', 'D'], $tokens->pluck('service.prefix')->all());
        $this->assertSame('G-001', $tokens[0]->token_no);
        $this->assertSame('D-001', $tokens[1]->token_no);
    }

    public function test_each_service_lands_on_its_own_counter(): void
    {
        $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
            ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
        ]))->assertRedirect();

        $generalCounter = Counter::where('name', 'Counter-1')->firstOrFail();
        $dentalCounter = Counter::where('name', 'Counter-2')->firstOrFail();

        $this->assertSame($generalCounter->id, Token::where('token_no', 'G-001')->firstOrFail()->counter_id);
        $this->assertSame($dentalCounter->id, Token::where('token_no', 'D-001')->firstOrFail()->counter_id);
    }

    public function test_sequence_numbers_continue_across_batches_per_service(): void
    {
        $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
            ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
        ]));

        // A second registration for Dental only: the Dental series continues
        // while the General series is left untouched.
        $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload(
            [['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id]],
            ['phone' => '01700000098']
        ));

        $this->assertDatabaseHas('tokens', ['token_no' => 'G-001', 'seq' => 1]);
        $this->assertDatabaseHas('tokens', ['token_no' => 'D-001', 'seq' => 1]);
        $this->assertDatabaseHas('tokens', ['token_no' => 'D-002', 'seq' => 2]);
        $this->assertDatabaseMissing('tokens', ['token_no' => 'G-002']);
        $this->assertSame(3, Token::count());
    }

    public function test_issued_page_lists_every_token_from_the_batch(): void
    {
        $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
            ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
        ]));

        $ids = Token::orderBy('id')->pluck('id')->all();

        $response = $this->actingAs($this->receptionist)->get(route('tokens.issued', ['tokens' => $ids]));
        $response->assertOk();

        $html = $response->getContent();
        $this->assertStringContainsString('G-001', $html);
        $this->assertStringContainsString('D-001', $html);
        $this->assertStringContainsString(route('tokens.print', Token::where('token_no', 'D-001')->firstOrFail()), $html);
    }

    public function test_issued_route_is_not_shadowed_by_the_token_show_route(): void
    {
        $response = $this->actingAs($this->receptionist)->get(route('tokens.issued'));

        $response->assertOk();
        $this->assertStringNotContainsString('No query results for model', $response->getContent());
    }

    public function test_duplicate_service_in_one_batch_is_rejected(): void
    {
        $response = $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
        ]));

        $response->assertSessionHasErrors('services.1.service_id');
        $this->assertSame(0, Token::count(), 'No token may be created when the batch is invalid');
    }

    public function test_doctor_must_belong_to_the_service_in_the_same_row(): void
    {
        $response = $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->dentalDoctor->id],
        ]));

        $response->assertSessionHasErrors('services.0.doctor_id');
        $this->assertSame(0, Token::count());
    }

    public function test_at_least_one_service_is_required(): void
    {
        $this->actingAs($this->receptionist)
            ->post(route('tokens.store'), $this->payload([]))
            ->assertSessionHasErrors('services');

        $this->assertSame(0, Token::count());
    }

    public function test_batch_size_is_capped(): void
    {
        $this->actingAs($this->receptionist)
            ->post(route('tokens.store'), $this->payload([
                ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
                ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
            ], ['services' => array_fill(0, 11, [
                'service_id' => $this->general->id,
                'doctor_id' => $this->generalDoctor->id,
            ])]))
            ->assertSessionHasErrors('services');

        $this->assertSame(0, Token::count());
    }

    public function test_batch_aborts_entirely_when_one_pair_is_invalid(): void
    {
        // A good General row plus a Dental service pointed at the General doctor.
        // Nothing must be written, not even the valid first row.
        try {
            (new TokenService)->issueMany(
                ['name' => 'Atomic Patient', 'phone' => '01700000077'],
                [
                    ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
                    ['service_id' => $this->dental->id, 'doctor_id' => $this->generalDoctor->id],
                ],
                $this->receptionist->id
            );
            $this->fail('Expected the invalid doctor/service pairing to abort the batch.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('belong', collect($e->errors())->flatten()->implode(' '));
        }

        $this->assertSame(0, Token::count(), 'Batch must be all-or-nothing');
        $this->assertSame(0, Patient::count(), 'Patient must not be persisted on an aborted batch');
    }

    public function test_returning_patient_is_reused_across_a_batch(): void
    {
        $first = $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->general->id, 'doctor_id' => $this->generalDoctor->id],
        ]));
        $first->assertRedirect();

        $this->actingAs($this->receptionist)->post(route('tokens.store'), $this->payload([
            ['service_id' => $this->dental->id, 'doctor_id' => $this->dentalDoctor->id],
        ]))->assertRedirect();

        $patient = Patient::where('phone', '01700000099')->firstOrFail();

        $this->assertSame(1, Patient::where('phone', '01700000099')->count());
        $this->assertSame(2, $patient->tokens()->count());
        $this->assertTrue($patient->tokens()->get()->every(fn ($t) => $t->patient_id === $patient->id));
    }

    public function test_issue_many_rejects_an_empty_batch(): void
    {
        $this->expectException(ValidationException::class);

        (new TokenService)->issueMany(['name' => 'Nobody', 'phone' => '01700000055'], []);
    }

    public function test_create_page_renders_the_repeatable_service_rows(): void
    {
        $this->actingAs($this->receptionist)
            ->get(route('tokens.create'))
            ->assertOk()
            ->assertSee('Add another service')
            ->assertSee('tokenServices', false);
    }
}

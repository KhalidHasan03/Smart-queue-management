<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Services\TokenService;
use App\Support\ReviewCode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    public function test_generated_codes_have_the_expected_shape(): void
    {
        $code = ReviewCode::generate();

        $this->assertSame(ReviewCode::LENGTH, strlen($code));
        $this->assertMatchesRegularExpression('/^[23456789ABCDEFGHJKMNPQRSTUVWXYZ]{8}$/', $code);
    }

    public function test_generated_codes_never_contain_ambiguous_glyphs(): void
    {
        for ($i = 0; $i < 60; $i++) {
            $code = ReviewCode::generate();
            foreach (['0', 'O', '1', 'I', 'L'] as $bad) {
                $this->assertStringNotContainsString($bad, $code);
            }
        }
    }

    public function test_codes_are_unique_across_many_generations(): void
    {
        $codes = [];
        for ($i = 0; $i < 200; $i++) {
            $codes[] = ReviewCode::generate();
        }

        $this->assertCount(200, array_unique($codes));
    }

    public function test_normalize_strips_noise_and_uppercases(): void
    {
        $this->assertSame('AB2CD3EF', ReviewCode::normalize(' ab-2cd3ef '));
        $this->assertSame('AB2CD3EF', ReviewCode::normalize('a.b(2)c/d3e-f!'));
        // Anything past the fixed length is dropped, so a longer paste still
        // resolves to the same code.
        $this->assertSame('AB2CD3EF', ReviewCode::normalize('AB2CD3EFF'));
    }

    public function test_normalize_drops_ambiguous_characters(): void
    {
        // 0/O/1/I/L are not in the alphabet, so they are stripped.
        $this->assertSame('BCDEFGH', ReviewCode::normalize('01OI1LBCDEFGH'));
    }

    public function test_is_well_formed_checks_length_after_normalizing(): void
    {
        $this->assertTrue(ReviewCode::isWellFormed('AB2CD3EF'));
        $this->assertTrue(ReviewCode::isWellFormed('ab2-cd3ef'));
        $this->assertFalse(ReviewCode::isWellFormed('AB2CD3'));
        $this->assertFalse(ReviewCode::isWellFormed(''));
    }

    public function test_issuing_a_token_assigns_a_review_code(): void
    {
        $service = Service::where('prefix', 'G')->firstOrFail();
        $doctor = Doctor::where('service_id', $service->id)->firstOrFail();

        $token = app(TokenService::class)->issue(
            ['name' => 'Code Patient', 'phone' => '01711000111'],
            $service->id,
            $doctor->id,
        );

        $this->assertNotNull($token->review_code);
        $this->assertTrue(ReviewCode::isWellFormed($token->review_code));
        $this->assertTrue($token->hasReviewCode());
    }

    public function test_every_token_in_a_multi_service_batch_gets_a_distinct_code(): void
    {
        $services = Service::where('is_active', true)->get();

        $pairs = $services->map(fn ($service) => [
            'service_id' => $service->id,
            'doctor_id' => Doctor::where('service_id', $service->id)->firstOrFail()->id,
        ])->all();

        $tokens = app(TokenService::class)->issueMany(
            ['name' => 'Multi Patient', 'phone' => '01711000222'],
            $pairs,
        );

        $codes = $tokens->pluck('review_code');
        $this->assertCount($tokens->count(), $codes->unique());
        foreach ($codes as $code) {
            $this->assertTrue(ReviewCode::isWellFormed($code));
        }
    }

    public function test_review_url_points_at_the_kiosk_with_the_code(): void
    {
        $service = Service::where('prefix', 'G')->firstOrFail();
        $doctor = Doctor::where('service_id', $service->id)->firstOrFail();

        $token = app(TokenService::class)->issue(
            ['name' => 'Url Patient', 'phone' => '01711000333'],
            $service->id,
            $doctor->id,
        );

        $this->assertStringContainsString('code='.$token->review_code, $token->review_url);
        $this->assertStringContainsString(route('review.kiosk'), $token->review_url);
    }

    public function test_a_completed_token_is_not_reviewable_without_a_code(): void
    {
        $service = Service::where('prefix', 'G')->firstOrFail();
        $token = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => 1,
            'token_no' => 'G-950',
            'review_code' => null,
            'service_id' => $service->id,
            'doctor_id' => Doctor::where('service_id', $service->id)->firstOrFail()->id,
            'counter_id' => Counter::value('id'),
            'patient_id' => Patient::create(['name' => 'No Code', 'phone' => '01711000444'])->id,
            'status' => Token::COMPLETED,
            'review_status' => 'pending',
        ]);

        $this->assertFalse($token->canBeReviewed());
    }

    // ------------------------------------------------------------ printed slip

    private function receptionist(): User
    {
        return User::where('email', 'reception@queuecare.local')->firstOrFail();
    }

    public function test_the_slip_prints_the_review_code_while_the_visit_is_still_waiting(): void
    {
        $service = Service::where('prefix', 'G')->firstOrFail();

        $token = app(TokenService::class)->issue(
            ['name' => 'Slip Patient', 'phone' => '01711000555'],
            $service->id,
            Doctor::where('service_id', $service->id)->firstOrFail()->id,
        );

        $this->assertSame(Token::WAITING, $token->status);

        // The patient must leave with the code in hand, otherwise the kiosk is
        // unusable at the counter.
        $this->actingAs($this->receptionist())
            ->get(route('tokens.print', $token))
            ->assertOk()
            ->assertSee($token->review_code);
    }

    public function test_the_slip_replaces_the_code_with_the_thanks_link_once_reviewed(): void
    {
        $service = Service::where('prefix', 'G')->firstOrFail();

        $token = app(TokenService::class)->issue(
            ['name' => 'Slip Done', 'phone' => '01711000666'],
            $service->id,
            Doctor::where('service_id', $service->id)->firstOrFail()->id,
        );
        $token->update(['status' => Token::COMPLETED, 'finished_at' => now(), 'review_status' => 'submitted']);
        Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::GOOD,
            'status' => Review::STATUS_APPROVED,
        ]);

        $this->actingAs($this->receptionist())
            ->get(route('tokens.print', $token))
            ->assertOk()
            ->assertSee('Your feedback is in')
            ->assertSee(route('review.thanks', ['code' => $token->review_code]));
    }
}

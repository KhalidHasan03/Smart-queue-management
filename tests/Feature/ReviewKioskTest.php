<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Token;
use App\Models\User;
use App\Support\ReviewCode;
use App\Support\ReviewSettings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewKioskTest extends TestCase
{
    use RefreshDatabase;

    private Token $completed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $service = Service::where('prefix', 'G')->firstOrFail();

        $this->completed = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => 1,
            'token_no' => 'G-901',
            'review_code' => ReviewCode::generate(),
            'service_id' => $service->id,
            'doctor_id' => Doctor::where('service_id', $service->id)->firstOrFail()->id,
            'counter_id' => Counter::value('id'),
            'patient_id' => Patient::create([
                'name' => 'Rahim Uddin',
                'phone' => '01700000777',
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
            'review_requested_at' => now(),
        ]);
    }

    private function last4(?Token $token = null): string
    {
        $token ??= $this->completed;

        return substr((string) $token->patient->phone, -4);
    }

    private function verify(?Token $token = null, ?string $phone = null)
    {
        $token ??= $this->completed;

        return $this->postJson(route('review.verify'), [
            'code' => $token->review_code,
            'phone_last_4' => $phone ?? $this->last4($token),
        ]);
    }

    // ---------------------------------------------------------------- identity

    public function test_verify_returns_the_visit_summary_for_a_matching_code_and_phone(): void
    {
        $this->verify()
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('visit.token_no', 'G-901')
            ->assertJsonPath('visit.first_name', 'Rahim U.');
    }

    public function test_verify_masks_the_patient_name(): void
    {
        $response = $this->verify()->assertOk();

        $visit = $response->json('visit');
        $this->assertSame('Rahim U.', $visit['first_name']);
        $this->assertStringNotContainsString('Uddin', $visit['first_name']);
    }

    public function test_verify_fails_with_the_wrong_phone_last_4(): void
    {
        $this->verify(null, '0000')
            ->assertStatus(422)
            ->assertJsonValidationErrors('code');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_verify_fails_for_an_unknown_code(): void
    {
        $this->postJson(route('review.verify'), [
            'code' => 'ZZZZZZZZ',
            'phone_last_4' => '0777',
        ])->assertStatus(422)->assertJsonValidationErrors('code');
    }

    public function test_verify_rejects_a_wrong_length_code(): void
    {
        $this->postJson(route('review.verify'), [
            'code' => 'SHORT',
            'phone_last_4' => '0777',
        ])->assertStatus(422)->assertJsonValidationErrors('code');
    }

    public function test_the_failure_message_does_not_reveal_whether_a_code_exists(): void
    {
        $unknown = $this->postJson(route('review.verify'), [
            'code' => 'ZZZZZZZZ',
            'phone_last_4' => '0777',
        ])->assertStatus(422)->json('errors.code.0');

        $wrongPhone = $this->verify(null, '0000')
            ->assertStatus(422)->json('errors.code.0');

        $this->assertSame($unknown, $wrongPhone);
    }

    public function test_a_token_number_cannot_be_used_instead_of_the_review_code(): void
    {
        // Token numbers restart daily, so they must never resolve a visit.
        $this->postJson(route('review.verify'), [
            'code' => $this->completed->token_no,
            'phone_last_4' => $this->last4(),
        ])->assertStatus(422)->assertJsonValidationErrors('code');
    }

    public function test_the_same_token_number_on_a_different_day_does_not_leak(): void
    {
        // The old bug: an unfiltered token_no lookup could surface a previous
        // day's visit. Only the unique code may resolve, so this must fail.
        $yesterday = (clone $this->completed);
        $yesterday->token_date = now()->subDay();
        $yesterday->review_code = ReviewCode::generate();
        $yesterday->save();

        $this->verify($yesterday, $this->last4($yesterday))->assertOk();
    }

    // --------------------------------------------------------- privacy + reset

    public function test_a_review_is_anonymous_by_default_and_never_leaks_the_patient_name(): void
    {
        // The kiosk does not send is_anonymous unless the patient opts in, so
        // an omitted flag must still store the review as anonymous. Otherwise
        // author_name falls back to the patient's real name and publishes it.
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::EXCELLENT])->assertOk();

        $review = Review::firstOrFail();

        $this->assertTrue($review->is_anonymous);
        $this->assertSame('Anonymous', $review->author_name);

        $this->get(route('review.thanks', ['code' => $this->completed->review_code]))
            ->assertOk()
            ->assertDontSee('Rahim Uddin');
    }

    public function test_a_blank_display_name_stays_anonymous(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), [
            'rating' => Review::EXCELLENT,
            'is_anonymous' => true,
            'display_name' => null,
        ])->assertOk();

        $this->assertSame('Anonymous', Review::firstOrFail()->author_name);
    }

    public function test_opting_in_attaches_the_chosen_name(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), [
            'rating' => Review::EXCELLENT,
            'is_anonymous' => false,
            'display_name' => 'Rahim',
        ])->assertOk();

        $review = Review::firstOrFail();
        $this->assertFalse($review->is_anonymous);
        $this->assertSame('Rahim', $review->author_name);
    }

    public function test_a_filled_honeypot_is_rejected(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), [
            'rating' => Review::EXCELLENT,
            'honeypot' => 'http://spam.example',
        ])->assertStatus(422)->assertJsonValidationErrors('honeypot');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_reset_clears_the_server_side_verification(): void
    {
        $this->verify()->assertOk();

        $this->postJson(route('review.reset'))->assertOk()->assertJsonPath('ok', true);

        // After the kiosk idles out, the old proof must no longer authorise a
        // submit from the same shared browser.
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])
            ->assertStatus(422)
            ->assertJsonValidationErrors('code');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_the_kiosk_page_is_never_cached(): void
    {
        // Laravel's session middleware rewrites Cache-Control, so assert the
        // property we care about rather than an exact string.
        $response = $this->get(route('review.kiosk'))->assertOk();

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    public function test_the_thanks_page_is_never_cached(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::EXCELLENT])->assertOk();

        $response = $this->get(route('review.thanks', ['code' => $this->completed->review_code]))
            ->assertOk();

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }

    // ------------------------------------------------------------------ submit

    public function test_submit_is_rejected_without_a_prior_verify(): void
    {
        // Skipping step 1 must not let anyone post a rating.
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])
            ->assertStatus(422)
            ->assertJsonValidationErrors('code');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_submit_records_a_published_rating_for_a_good_review(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])
            ->assertOk()
            ->assertJsonPath('published', true);

        $this->assertDatabaseHas('reviews', [
            'token_id' => $this->completed->id,
            'rating' => Review::GOOD,
            'status' => Review::STATUS_APPROVED,
        ]);
    }

    public function test_a_comment_is_required_below_the_configured_threshold(): void
    {
        // Default threshold is Normal (2), so a Bad rating needs a comment.
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::BAD])
            ->assertStatus(422)
            ->assertJsonValidationErrors('comment');

        $this->postJson(route('review.submit'), [
            'rating' => Review::BAD,
            'comment' => 'Waited a very long time.',
        ])->assertOk();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_a_good_rating_does_not_require_a_comment(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])->assertOk();
    }

    public function test_the_verify_session_is_cleared_after_a_submission(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])->assertOk();

        // A replayed submit must not create a second review.
        $this->postJson(route('review.submit'), ['rating' => Review::EXCELLENT])
            ->assertStatus(422);

        $this->assertDatabaseCount('reviews', 1);
    }

    // --------------------------------------------------------------- lifecycle

    public function test_the_kiosk_can_be_disabled_from_settings(): void
    {
        $this->get(route('review.kiosk'))->assertOk();

        Setting::set(ReviewSettings::ENABLED, '0');

        $this->get(route('review.kiosk'))->assertNotFound();
        $this->postJson(route('review.verify'), [
            'code' => $this->completed->review_code,
            'phone_last_4' => $this->last4(),
        ])->assertNotFound();
    }

    public function test_auto_approval_can_be_switched_off_entirely(): void
    {
        Setting::set(ReviewSettings::AUTO_APPROVE_FROM, '5');

        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::EXCELLENT])
            ->assertOk()
            ->assertJsonPath('published', false);

        $this->assertDatabaseHas('reviews', [
            'token_id' => $this->completed->id,
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_auto_approval_threshold_is_configurable(): void
    {
        Setting::set(ReviewSettings::AUTO_APPROVE_FROM, '4');

        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::GOOD])
            ->assertJsonPath('published', false);

        $this->assertDatabaseHas('reviews', ['status' => Review::STATUS_PENDING]);
    }

    // ----------------------------------------------------------------- throttles

    public function test_verify_is_rate_limited_per_ip(): void
    {
        $limit = ReviewSettings::throttlePerMinute();

        for ($i = 0; $i < $limit; $i++) {
            $this->postJson(route('review.verify'), [
                'code' => 'ZZZZZZZZ',
                'phone_last_4' => '0777',
            ])->assertStatus(422);
        }

        $this->postJson(route('review.verify'), [
            'code' => 'ZZZZZZZZ',
            'phone_last_4' => '0777',
        ])->assertStatus(429);
    }

    // -------------------------------------------------------------------- thanks

    public function test_the_thanks_page_requires_a_real_code(): void
    {
        $this->get(route('review.thanks', ['code' => 'ZZZZZZZZ']))->assertNotFound();
    }

    public function test_the_thanks_page_needs_an_existing_review(): void
    {
        // The code is valid and the visit is completed, but no review exists.
        $this->get(route('review.thanks', ['code' => $this->completed->review_code]))
            ->assertNotFound();
    }

    public function test_the_thanks_page_shows_a_published_review(): void
    {
        $this->verify();
        $this->postJson(route('review.submit'), ['rating' => Review::EXCELLENT])->assertOk();

        $this->get(route('review.thanks', ['code' => $this->completed->review_code]))
            ->assertOk()
            ->assertSee('Excellent')
            ->assertSee('🤩');
    }

    // ------------------------------------------------------------------- setup

    public function test_the_setup_page_requires_display_manage(): void
    {
        $operator = User::where('email', 'operator@queuecare.local')->firstOrFail();
        $this->actingAs($operator)->get(route('reviews.setup'))->assertForbidden();

        $display = User::where('email', 'display@queuecare.local')->firstOrFail();
        $this->actingAs($display)->get(route('reviews.setup'))->assertOk();
    }

    public function test_saving_the_setup_persists_every_knob(): void
    {
        $display = User::where('email', 'display@queuecare.local')->firstOrFail();

        $this->actingAs($display)->put(route('reviews.setup.update'), [
            ReviewSettings::field(ReviewSettings::ENABLED) => '1',
            ReviewSettings::field(ReviewSettings::HEADLINE) => 'How did we do?',
            ReviewSettings::field(ReviewSettings::INSTRUCTION) => 'Scan the code on your slip',
            ReviewSettings::field(ReviewSettings::IDLE_SECS) => 45,
            ReviewSettings::field(ReviewSettings::AUTO_APPROVE_FROM) => 4,
            ReviewSettings::field(ReviewSettings::REQUIRE_COMMENT_BELOW) => 1,
            ReviewSettings::field(ReviewSettings::THROTTLE_PER_MIN) => 20,
        ])->assertRedirect();

        $this->assertSame('How did we do?', Setting::get(ReviewSettings::HEADLINE));
        $this->assertSame(45, ReviewSettings::idleSeconds());
        $this->assertSame(4, ReviewSettings::autoApproveFrom());
        $this->assertSame(1, ReviewSettings::requireCommentBelow());
        $this->assertSame(20, ReviewSettings::throttlePerMinute());

        $this->actingAs($display)->get(route('review.kiosk'))
            ->assertOk()
            ->assertSee('How did we do?');
    }

    public function test_the_setup_page_validates_its_ranges(): void
    {
        $display = User::where('email', 'display@queuecare.local')->firstOrFail();

        $this->actingAs($display)->put(route('reviews.setup.update'), [
            ReviewSettings::field(ReviewSettings::ENABLED) => '1',
            ReviewSettings::field(ReviewSettings::HEADLINE) => '',
            ReviewSettings::field(ReviewSettings::INSTRUCTION) => 'x',
            ReviewSettings::field(ReviewSettings::IDLE_SECS) => 9999,
            ReviewSettings::field(ReviewSettings::AUTO_APPROVE_FROM) => 42,
            ReviewSettings::field(ReviewSettings::REQUIRE_COMMENT_BELOW) => 99,
            ReviewSettings::field(ReviewSettings::THROTTLE_PER_MIN) => 0,
        ])->assertSessionHasErrors([
            ReviewSettings::field(ReviewSettings::HEADLINE),
            ReviewSettings::field(ReviewSettings::IDLE_SECS),
            ReviewSettings::field(ReviewSettings::AUTO_APPROVE_FROM),
            ReviewSettings::field(ReviewSettings::REQUIRE_COMMENT_BELOW),
            ReviewSettings::field(ReviewSettings::THROTTLE_PER_MIN),
        ]);
    }

    public function test_turning_the_kiosk_off_via_the_setup_form(): void
    {
        $display = User::where('email', 'display@queuecare.local')->firstOrFail();

        $this->actingAs($display)->put(route('reviews.setup.update'), [
            ReviewSettings::field(ReviewSettings::ENABLED) => '0',
            ReviewSettings::field(ReviewSettings::HEADLINE) => 'How was your visit?',
            ReviewSettings::field(ReviewSettings::INSTRUCTION) => 'Enter your code',
            ReviewSettings::field(ReviewSettings::IDLE_SECS) => 20,
            ReviewSettings::field(ReviewSettings::AUTO_APPROVE_FROM) => 3,
            ReviewSettings::field(ReviewSettings::REQUIRE_COMMENT_BELOW) => 2,
            ReviewSettings::field(ReviewSettings::THROTTLE_PER_MIN) => 10,
        ])->assertRedirect();

        $this->assertFalse(ReviewSettings::enabled());
        $this->get(route('review.kiosk'))->assertNotFound();
    }
}

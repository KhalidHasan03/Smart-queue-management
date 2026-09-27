<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Notifications\NegativeReviewNotification;
use App\Support\Rbac;
use App\Support\ReviewCode;
use App\Support\ReviewSettings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
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
                'name' => 'Review Patient',
                'phone' => '01700000777',
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
            'review_requested_at' => now(),
        ]);
    }

    private function admin(): User
    {
        return User::where('email', 'admin@queuecare.local')->firstOrFail();
    }

    private function last4(?Token $token): string
    {
        return substr((string) $token->patient->phone, -4);
    }

    /** Step 1: verify code + phone. */
    private function verify(?Token $token = null, ?string $phone = null): TestResponse
    {
        $token ??= $this->completed;

        return $this->postJson(route('review.verify'), [
            'code' => $token->review_code,
            'phone_last_4' => $phone ?? $this->last4($token),
        ]);
    }

    /** Steps 1+2 in one go: verify then submit. */
    private function rate(?Token $token = null, int $rating = Review::GOOD, array $extra = []): TestResponse
    {
        $token ??= $this->completed;
        $this->verify($token);

        $payload = array_merge(['rating' => $rating], $extra);
        // Ratings at or below the require-comment threshold need a comment.
        if ($rating <= ReviewSettings::requireCommentBelow() && blank($extra['comment'] ?? null)) {
            $payload['comment'] = 'A short note for the team.';
        }

        return $this->postJson(route('review.submit'), $payload);
    }

    public function test_the_kiosk_is_public_and_shows_four_emoji_options(): void
    {
        $response = $this->get(route('review.kiosk'));

        $response->assertOk();
        $response->assertSee('Bad');
        $response->assertSee('Normal');
        $response->assertSee('Good');
        $response->assertSee('Excellent');
        $response->assertSee('😞');
        $response->assertSee('😐');
        $response->assertSee('🙂');
        $response->assertSee('🤩');
        $this->assertStringNotContainsString('★', $response->getContent());
    }

    public function test_a_completed_visit_can_be_reviewed_without_logging_in(): void
    {
        // Good (3) is the default auto-approve threshold, so it publishes at once.
        $response = $this->rate($this->completed, Review::GOOD);

        $response->assertOk()->assertJsonPath('published', true);

        $this->assertDatabaseHas('reviews', [
            'token_id' => $this->completed->id,
            'rating' => Review::GOOD,
            'status' => Review::STATUS_APPROVED,
            'is_approved' => true,
        ]);

        $this->assertDatabaseHas('tokens', [
            'id' => $this->completed->id,
            'review_status' => Review::STATUS_APPROVED,
        ]);
    }

    public function test_unhappy_ratings_wait_for_moderation_and_notify_admins(): void
    {
        Notification::fake();

        $response = $this->rate($this->completed, Review::NORMAL);

        $response->assertOk()->assertJsonPath('published', false);

        $this->assertDatabaseHas('reviews', [
            'token_id' => $this->completed->id,
            'rating' => Review::NORMAL,
            'status' => Review::STATUS_PENDING,
            'is_approved' => false,
        ]);
        $this->assertDatabaseHas('tokens', ['id' => $this->completed->id, 'review_status' => 'submitted']);

        Notification::assertSentTo($this->admin(), NegativeReviewNotification::class);
    }

    public function test_happy_ratings_publish_without_notifying_admins(): void
    {
        Notification::fake();

        $this->rate($this->completed, Review::EXCELLENT);

        Notification::assertNothingSent();
        $this->assertDatabaseHas('reviews', ['token_id' => $this->completed->id, 'status' => Review::STATUS_APPROVED]);
    }

    public function test_the_scale_is_capped_at_four_options(): void
    {
        $this->verify();

        $this->postJson(route('review.submit'), ['rating' => 5])->assertStatus(422);
        $this->postJson(route('review.submit'), ['rating' => 0])->assertStatus(422);

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_a_visit_can_only_be_reviewed_once(): void
    {
        $this->rate($this->completed, Review::GOOD);

        // A second verify must fail because the visit is no longer reviewable.
        $this->verify()->assertStatus(422);
        $this->verify()->assertJsonValidationErrors('code');

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_the_notification_renders_the_emoji_scale(): void
    {
        $review = Review::factory()->forToken($this->completed)->rating(Review::NORMAL)->create();

        $mail = (new NegativeReviewNotification($review))->toMail($this->admin());

        $this->assertStringContainsString('Normal', $mail->subject);
        $this->assertStringContainsString('😐', $mail->subject);
        $this->assertStringNotContainsString('/5', $mail->subject);
    }

    public function test_a_visit_that_is_not_completed_is_not_reviewable(): void
    {
        $this->completed->update(['status' => Token::SERVING, 'review_status' => 'pending']);

        $this->verify()->assertStatus(422);
    }

    public function test_a_visit_finished_by_a_service_staff_member_is_still_ratable(): void
    {
        $token = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => 2,
            'token_no' => 'G-902',
            'review_code' => ReviewCode::generate(),
            'service_id' => $this->completed->service_id,
            'doctor_id' => $this->completed->doctor_id,
            'counter_id' => $this->completed->counter_id,
            'patient_id' => Patient::create([
                'name' => 'Staff Finished Patient',
                'phone' => '01700000555',
            ])->id,
            'status' => Token::CALLING,
            'called_at' => now(),
        ]);

        $staff = User::where('email', 'staff@queuecare.local')->firstOrFail();

        $this->actingAs($staff)->post(route('staff.process', $token))->assertRedirect();
        $this->actingAs($staff)->post(route('staff.process', $token))->assertRedirect();

        $this->assertSame(Token::COMPLETED, $token->fresh()->status);
        $this->assertSame('pending', $token->fresh()->review_status);
        $this->assertNotNull($token->fresh()->review_requested_at);

        $this->rate($token, Review::GOOD)->assertOk();

        $this->assertDatabaseHas('reviews', ['token_id' => $token->id, 'rating' => Review::GOOD]);
    }

    public function test_the_thanks_page_shows_the_submitted_rating(): void
    {
        $this->rate($this->completed, Review::GOOD);

        $response = $this->get(route('review.thanks', ['code' => $this->completed->review_code]));

        $response->assertOk();
        $response->assertSee('Good');
        $response->assertSee('🙂');
    }

    public function test_anonymous_reviews_hide_the_patient_name(): void
    {
        $this->rate($this->completed, Review::BAD, [
            'is_anonymous' => 1,
            'comment' => 'Please call me about my visit.',
        ]);

        $review = Review::where('token_id', $this->completed->id)->firstOrFail();

        $this->assertTrue($review->is_anonymous);
        $this->assertSame('Anonymous', $review->author_name);
    }

    public function test_moderation_transitions_keep_token_and_flags_in_sync(): void
    {
        $this->rate($this->completed, Review::NORMAL);

        $review = Review::where('token_id', $this->completed->id)->firstOrFail();
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('admin.reviews.approve', $review));
        $this->assertSame(Review::STATUS_APPROVED, $review->fresh()->status);
        $this->assertTrue($review->fresh()->is_approved);
        $this->assertNotNull($review->fresh()->reviewed_at);
        $this->assertDatabaseHas('tokens', ['id' => $this->completed->id, 'review_status' => 'approved']);

        $this->actingAs($admin)->patch(route('admin.reviews.reject', $review));
        $this->assertSame(Review::STATUS_REJECTED, $review->fresh()->status);
        $this->assertFalse($review->fresh()->is_approved);
        $this->assertDatabaseHas('tokens', ['id' => $this->completed->id, 'review_status' => 'rejected']);

        $this->actingAs($admin)->patch(route('admin.reviews.restore', $review));
        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
        $this->assertDatabaseHas('tokens', ['id' => $this->completed->id, 'review_status' => 'pending']);
    }

    public function test_deleting_a_review_makes_the_visit_reviewable_again(): void
    {
        $this->rate($this->completed, Review::GOOD);

        $review = Review::where('token_id', $this->completed->id)->firstOrFail();

        // Delete returns to whichever list the moderator came from, so the same
        // row buttons work on the admin list and on the counter page.
        $this->actingAs($this->admin())
            ->from(route('admin.reviews.index'))
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect(route('admin.reviews.index'));

        $this->assertDatabaseCount('reviews', 0);
        $this->assertDatabaseHas('tokens', ['id' => $this->completed->id, 'review_status' => 'pending']);

        $this->rate($this->completed, Review::EXCELLENT)->assertOk();

        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_a_viewer_cannot_moderate(): void
    {
        $review = Review::factory()->forToken($this->completed)->create();

        $viewer = User::where('email', 'reception@queuecare.local')->firstOrFail();
        Rbac::saveOverrides([
            'receptionist' => ['dashboard.view', 'serials.view', 'reviews.view'],
        ]);
        $viewer->refresh();

        $this->actingAs($viewer)->get(route('admin.reviews.index'))->assertOk();
        $this->actingAs($viewer)->patch(route('admin.reviews.approve', $review))->assertForbidden();
        $this->actingAs($viewer)->delete(route('admin.reviews.destroy', $review))->assertForbidden();

        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
    }

    public function test_the_export_respects_every_filter(): void
    {
        $review = Review::factory()->forToken($this->completed)->rating(Review::EXCELLENT)->approved()->create();

        $rejectedCsv = $this->actingAs($this->admin())
            ->get(route('admin.reviews.export', ['status' => 'rejected']))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('"Token No"', $rejectedCsv);
        $this->assertStringNotContainsString($review->token->token_no, $rejectedCsv);

        $approvedCsv = $this->actingAs($this->admin())
            ->get(route('admin.reviews.export', ['status' => 'approved']))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString($review->token->token_no, $approvedCsv);
        $this->assertStringContainsString('Excellent', $approvedCsv);
        $this->assertStringNotContainsString('/5', $approvedCsv);
    }

    public function test_the_landing_review_endpoints_are_gone(): void
    {
        $this->postJson('/reviews/verify', [])->assertNotFound();
        $this->postJson('/reviews', [])->assertNotFound();
        $this->getJson('/reviews')->assertNotFound();
    }

    public function test_the_admin_list_renders_emoji_not_stars(): void
    {
        Review::factory()->forToken($this->completed)->rating(Review::NORMAL)->create();

        $response = $this->actingAs($this->admin())->get(route('admin.reviews.index'));

        $response->assertOk();
        $response->assertSee('😐');
        $response->assertDontSee('★');
    }
}

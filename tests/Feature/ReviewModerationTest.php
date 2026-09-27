<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AdminReviewController;
use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Support\Rbac;
use App\Support\ReviewCode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    private Counter $counterOne;

    private Counter $counterTwo;

    private Token $mine;

    private Token $theirs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $general = Service::where('prefix', 'G')->firstOrFail();
        $doctor = Doctor::where('service_id', $general->id)->firstOrFail();

        $this->counterOne = Counter::where('name', 'Counter-1')->firstOrFail();
        $this->counterTwo = Counter::where('name', 'Counter-2')->firstOrFail();

        // One completed visit per counter, each with a pending negative review.
        $this->mine = $this->completedToken($general, $doctor, $this->counterOne, 'My Counter Patient');
        $this->theirs = $this->completedToken($general, $doctor, $this->counterTwo, 'Other Counter Patient');
    }

    private int $phoneSeq = 0;

    private int $seq = 0;

    private function completedToken(Service $service, Doctor $doctor, Counter $counter, string $name): Token
    {
        $this->phoneSeq++;
        $this->seq++;

        return Token::create([
            'token_date' => now()->toDateString(),
            'seq' => $this->seq,
            'token_no' => $service->prefix.'-'.(900 + $this->seq),
            'review_code' => ReviewCode::generate(),
            'service_id' => $service->id,
            'doctor_id' => $doctor->id,
            'counter_id' => $counter->id,
            'patient_id' => Patient::create([
                'name' => $name,
                'phone' => '0171'.str_pad((string) $this->phoneSeq, 6, '0', STR_PAD_LEFT),
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
            'review_requested_at' => now(),
        ]);
    }

    private function pendingReview(Token $token, int $rating = Review::BAD): Review
    {
        return Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => $rating,
            'comment' => 'Long wait',
            'status' => Review::STATUS_PENDING,
        ]);
    }

    private function operator(): User
    {
        return User::where('email', 'operator@queuecare.local')->firstOrFail();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@queuecare.local')->firstOrFail();
    }

    // ------------------------------------------------------------ counter scope

    public function test_an_operator_can_approve_feedback_from_their_own_counter(): void
    {
        $review = $this->pendingReview($this->mine);

        $this->actingAs($this->operator())
            ->patch(route('admin.reviews.approve', $review))
            ->assertRedirect();

        $this->assertSame(Review::STATUS_APPROVED, $review->fresh()->status);
    }

    public function test_an_operator_cannot_approve_feedback_from_another_counter(): void
    {
        $review = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())
            ->patch(route('admin.reviews.approve', $review))
            ->assertForbidden();

        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
    }

    public function test_an_operator_cannot_reject_feedback_from_another_counter(): void
    {
        $review = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())
            ->patch(route('admin.reviews.reject', $review))
            ->assertForbidden();

        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
    }

    public function test_an_admin_can_moderate_any_counter(): void
    {
        $review = $this->pendingReview($this->theirs);

        $this->actingAs($this->admin())
            ->patch(route('admin.reviews.approve', $review))
            ->assertRedirect();

        $this->assertSame(Review::STATUS_APPROVED, $review->fresh()->status);
    }

    // ------------------------------------------------------------------- bulk

    public function test_bulk_approve_skips_reviews_outside_the_operators_counter(): void
    {
        $mine = $this->pendingReview($this->mine);
        $theirs = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())
            ->post(route('admin.reviews.bulk'), [
                'action' => 'approve',
                'ids' => [$mine->id, $theirs->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(Review::STATUS_APPROVED, $mine->fresh()->status);
        $this->assertSame(Review::STATUS_PENDING, $theirs->fresh()->status);
    }

    public function test_bulk_moderation_does_not_abort_the_whole_request_on_a_foreign_review(): void
    {
        // This is the regression guard for the bug where guardCounterScope used
        // abort() (HttpException) which bulk() did not catch, so a single
        // foreign id made the entire request 403 and approved nothing.
        $mine = $this->pendingReview($this->mine);
        $theirs = $this->pendingReview($this->theirs);

        $response = $this->actingAs($this->operator())->post(route('admin.reviews.bulk'), [
            'action' => 'approve',
            'ids' => [$mine->id, $theirs->id],
        ]);

        $response->assertRedirect();
        $this->assertSame(Review::STATUS_APPROVED, $mine->fresh()->status);
    }

    public function test_bulk_reject_respects_counter_scope(): void
    {
        $mine = $this->pendingReview($this->mine);
        $theirs = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())->post(route('admin.reviews.bulk'), [
            'action' => 'reject',
            'ids' => [$mine->id, $theirs->id],
        ])->assertRedirect();

        $this->assertSame(Review::STATUS_REJECTED, $mine->fresh()->status);
        $this->assertSame(Review::STATUS_PENDING, $theirs->fresh()->status);
    }

    // ----------------------------------------------------------------- rbac

    public function test_an_operator_lacks_the_delete_permission(): void
    {
        // The app authorises through hasPermission()/the permission middleware,
        // not Laravel's Gate, so assert on the model directly.
        $this->assertFalse($this->operator()->hasPermission('reviews.delete'));
        $this->assertTrue($this->operator()->hasPermission('reviews.manage'));
    }

    public function test_an_operator_cannot_delete_or_restore_a_review(): void
    {
        $review = $this->pendingReview($this->mine);

        $this->actingAs($this->operator())
            ->delete(route('admin.reviews.destroy', $review))
            ->assertForbidden();

        $this->actingAs($this->operator())
            ->patch(route('admin.reviews.restore', $review))
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', ['id' => $review->id]);
    }

    public function test_an_operator_cannot_reach_the_review_list(): void
    {
        // The list needs reviews.view, which operators do not hold; they work
        // their own queue instead.
        $this->actingAs($this->operator())
            ->get(route('admin.reviews.index'))
            ->assertForbidden();
    }

    public function test_a_viewer_cannot_moderate(): void
    {
        $review = $this->pendingReview($this->mine);

        // Strip the operator's manage permission down to view-only.
        Rbac::saveOverrides([
            'operator' => ['dashboard.view', 'serials.view', 'queue.view', 'reviews.view'],
        ]);
        $this->actingAs($this->operator()->fresh())
            ->patch(route('admin.reviews.approve', $review))
            ->assertForbidden();
    }

    public function test_reception_cannot_moderate(): void
    {
        $receptionist = User::where('email', 'reception@queuecare.local')->firstOrFail();
        $review = $this->pendingReview($this->mine);

        $this->actingAs($receptionist)
            ->patch(route('admin.reviews.approve', $review))
            ->assertForbidden();
    }

    public function test_an_admin_can_delete_a_review_and_the_visit_becomes_reviewable_again(): void
    {
        $review = $this->pendingReview($this->mine);
        $review->moderate(Review::STATUS_APPROVED);

        $this->actingAs($this->admin())
            ->delete(route('admin.reviews.destroy', $review))
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
        $this->assertTrue($this->mine->fresh()->canBeReviewed());
    }

    public function test_an_admin_can_restore_a_rejected_review_to_the_queue(): void
    {
        $review = $this->pendingReview($this->mine);
        $review->moderate(Review::STATUS_REJECTED);

        $this->actingAs($this->admin())
            ->patch(route('admin.reviews.restore', $review))
            ->assertRedirect();

        $this->assertSame(Review::STATUS_PENDING, $review->fresh()->status);
    }

    // ----------------------------------------------------------------- stats

    public function test_stats_ignore_legacy_completed_visits_without_a_review_code(): void
    {
        // A visit migrated before review codes existed: completed and pending
        // but not ratable, so it must not count as awaiting feedback.
        Token::create([
            'token_date' => now()->subDays(5)->toDateString(),
            'seq' => 9,
            'token_no' => 'G-800',
            'review_code' => null,
            'service_id' => Service::where('prefix', 'G')->value('id'),
            'doctor_id' => Doctor::where('service_id', Service::where('prefix', 'G')->value('id'))->value('id'),
            'counter_id' => $this->counterOne->id,
            'patient_id' => Patient::create(['name' => 'Legacy', 'phone' => '01700000888'])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now()->subDays(5),
            'review_status' => 'pending',
        ]);

        $review = $this->pendingReview($this->mine);

        $stats = app(AdminReviewController::class)->stats();

        $this->assertSame(2, $stats['eligible']);
        $this->assertSame(1, $stats['reviewed']);
        $this->assertSame(1, $stats['unreviewed']);
        $this->assertEquals(50, $stats['response_rate']);
        $this->assertSame(1, $stats['pending']);
    }

    public function test_stats_count_the_rating_distribution(): void
    {
        $this->pendingReview($this->mine, Review::BAD);
        $this->pendingReview($this->theirs, Review::NORMAL);

        $stats = app(AdminReviewController::class)->stats();

        $this->assertSame(1, $stats['distribution'][Review::BAD]);
        $this->assertSame(1, $stats['distribution'][Review::NORMAL]);
    }

    // ----------------------------------------------------------------- queue

    // ----------------------------------------------------------------- markup

    /**
     * The moderation list wraps its table in one bulk <form>. Row actions used
     * to be their own nested <form> elements, which is invalid HTML: browsers
     * discard the nested start tag, so the first inner </form> closed the bulk
     * form early. The row-1 "Approve" button then submitted PATCH to the bulk
     * URL (MethodNotAllowed) and the bulk action bar fell outside any form.
     *
     * These tests assert the rendered markup itself, because every other test
     * here calls the routes directly and would never see that breakage.
     */
    /**
     * Inner HTML of the bulk <form> (opening tag and closing tag excluded, so a
     * nested form would be unambiguous).
     */
    private function bulkFormHtml(string $body): string
    {
        $url = route('admin.reviews.bulk');
        $start = strpos($body, 'action="'.$url.'"');

        $this->assertNotFalse($start, 'The bulk form was not rendered.');

        $open = strrpos(substr($body, 0, $start), '<form');
        $this->assertNotFalse($open);

        $openEnd = strpos($body, '>', $open);
        $end = strpos($body, '</form>', $start);

        $this->assertNotFalse($openEnd);
        $this->assertNotFalse($end, 'The bulk form was never closed.');

        return substr($body, $openEnd + 1, $end - $openEnd - 1);
    }

    public function test_the_review_list_never_nests_forms(): void
    {
        $this->pendingReview($this->mine);

        $body = $this->actingAs($this->admin())
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->getContent();

        // No nested form may appear inside the bulk form; that is what silently
        // truncated it in the browser.
        $bulk = $this->bulkFormHtml($body);

        $this->assertStringNotContainsString(
            '<form',
            $bulk,
            'A nested <form> was emitted inside the bulk form, which browsers drop.'
        );
    }

    public function test_the_bulk_action_bar_and_checkboxes_live_inside_the_bulk_form(): void
    {
        $review = $this->pendingReview($this->mine);

        $body = $this->actingAs($this->admin())
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->getContent();

        $bulk = $this->bulkFormHtml($body);

        // The selection checkboxes and the bulk buttons must both be enclosed,
        // otherwise the bulk buttons submit nothing or without any ids.
        $this->assertStringContainsString('name="ids[]" value="'.$review->id.'"', $bulk);
        $this->assertStringContainsString('name="action" value="approve"', $bulk);
        $this->assertStringContainsString('name="action" value="reject"', $bulk);
    }

    public function test_row_action_buttons_carry_their_own_route_and_method(): void
    {
        $review = $this->pendingReview($this->mine);

        $body = $this->actingAs($this->admin())
            ->get(route('admin.reviews.index'))
            ->assertOk()
            ->getContent();

        $bulk = $this->bulkFormHtml($body);

        // formaction + a button-supplied _method replaces the per-row forms.
        $this->assertStringContainsString('formaction="'.route('admin.reviews.approve', $review).'"', $bulk);
        $this->assertStringContainsString('formaction="'.route('admin.reviews.reject', $review).'"', $bulk);
        $this->assertStringContainsString('formaction="'.route('admin.reviews.destroy', $review).'"', $bulk);
        $this->assertStringContainsString('name="_method" value="DELETE"', $bulk);
    }

    public function test_a_row_action_button_submits_to_its_own_route_the_way_a_browser_sends_it(): void
    {
        $review = $this->pendingReview($this->mine);

        // A submit button contributes its own name/value to the POST body, so
        // this is exactly what pressing the row's Approve button sends.
        $this->actingAs($this->operator())
            ->post(route('admin.reviews.approve', $review), ['_method' => 'PATCH'])
            ->assertRedirect();

        $this->assertSame(Review::STATUS_APPROVED, $review->fresh()->status);
    }

    public function test_the_bulk_button_submits_action_and_ids_to_the_bulk_route(): void
    {
        $mine = $this->pendingReview($this->mine);
        $theirs = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())
            ->post(route('admin.reviews.bulk'), [
                'action' => 'approve',
                'ids' => [$mine->id, $theirs->id],
            ])
            ->assertRedirect();

        $this->assertSame(Review::STATUS_APPROVED, $mine->fresh()->status);

        // The foreign review is still scoped out rather than moderated.
        $this->assertSame(Review::STATUS_PENDING, $theirs->fresh()->status);
    }

    public function test_the_queue_page_shows_pending_feedback_for_the_operators_counter(): void
    {
        $mine = $this->pendingReview($this->mine);
        $theirs = $this->pendingReview($this->theirs);

        $this->actingAs($this->operator())
            ->get(route('queue.index'))
            ->assertOk()
            ->assertSee('Long wait');
    }
}

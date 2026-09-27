<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Support\ReviewCode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CounterReviewPageTest extends TestCase
{
    use RefreshDatabase;

    private Counter $counter;

    private Service $service;

    private Doctor $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);

        $this->service = Service::where('prefix', 'G')->firstOrFail();
        $this->doctor = Doctor::where('service_id', $this->service->id)->firstOrFail();
        $this->counter = Counter::where('name', 'Counter-1')->firstOrFail();
    }

    private function operator(): User
    {
        return User::where('email', 'operator@queuecare.local')->firstOrFail();
    }

    private function createCompletedToken(): Token
    {
        return Token::create([
            'token_date' => now()->toDateString(),
            'seq' => (Token::max('seq') ?? 0) + 1,
            'token_no' => $this->service->prefix.'-'.(900 + ((Token::max('seq') ?? 0) + 1)),
            'review_code' => ReviewCode::generate(),
            'service_id' => $this->service->id,
            'doctor_id' => $this->doctor->id,
            'counter_id' => $this->counter->id,
            'patient_id' => Patient::create([
                'name' => 'Test',
                'phone' => '017'.rand(1000000, 9999999),
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
        ]);
    }

    public function test_operator_can_access_their_counter_reviews_page(): void
    {
        $this->actingAs($this->operator())
            ->get(route('queue.reviews'))
            ->assertOk();
    }

    public function test_counter_reviews_page_shows_reviews_for_own_counter(): void
    {
        $token = $this->createCompletedToken();
        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::GOOD,
            'comment' => 'Great service',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($this->operator())
            ->get(route('queue.reviews'))
            ->assertOk()
            ->assertSee('Great service');
    }

    public function test_counter_reviews_page_hides_feedback_from_other_counters(): void
    {
        $other = Counter::where('name', 'Counter-2')->firstOrFail();
        $token = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => (Token::max('seq') ?? 0) + 1,
            'token_no' => $this->service->prefix.'-'.(950 + ((Token::max('seq') ?? 0) + 1)),
            'review_code' => ReviewCode::generate(),
            'service_id' => $this->service->id,
            'doctor_id' => $this->doctor->id,
            'counter_id' => $other->id,
            'patient_id' => Patient::create([
                'name' => 'Other',
                'phone' => '018'.rand(1000000, 9999999),
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
        ]);
        Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::BAD,
            'comment' => 'Somebody elses problem',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($this->operator())
            ->get(route('queue.reviews'))
            ->assertOk()
            ->assertDontSee('Somebody elses problem');
    }

    public function test_the_counter_page_offers_only_manage_and_view_not_admin_actions(): void
    {
        $this->actingAs($this->operator())
            ->get(route('queue.reviews'))
            ->assertOk()
            ->assertDontSee(route('reviews.setup'))
            ->assertDontSee('Export CSV');
    }

    public function test_an_operator_can_open_the_detail_page_for_their_own_counter(): void
    {
        $token = $this->createCompletedToken();
        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::EXCELLENT,
            'comment' => 'Spotless',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($this->operator())
            ->get(route('admin.reviews.show', $review))
            ->assertOk()
            ->assertSee('Spotless');
    }

    public function test_an_operator_cannot_open_the_detail_page_for_another_counter(): void
    {
        $other = Counter::where('name', 'Counter-2')->firstOrFail();
        $token = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => (Token::max('seq') ?? 0) + 1,
            'token_no' => $this->service->prefix.'-'.(960 + ((Token::max('seq') ?? 0) + 1)),
            'review_code' => ReviewCode::generate(),
            'service_id' => $this->service->id,
            'doctor_id' => $this->doctor->id,
            'counter_id' => $other->id,
            'patient_id' => Patient::create([
                'name' => 'Other',
                'phone' => '019'.rand(1000000, 9999999),
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
        ]);
        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::BAD,
            'comment' => 'Not yours',
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($this->operator())
            ->get(route('admin.reviews.show', $review))
            ->assertForbidden();
    }

    public function test_an_operator_without_a_counter_sees_no_feedback(): void
    {
        $operator = $this->operator();
        $operator->update(['counter_id' => null]);

        $this->actingAs($operator->fresh())
            ->get(route('queue.reviews'))
            ->assertOk();
    }

    public function test_the_sidebar_links_the_kiosk_for_counter_operators(): void
    {
        $this->actingAs($this->operator())
            ->get(route('queue.index'))
            ->assertOk()
            ->assertSee(route('review.kiosk'), false);
    }

    public function test_approving_from_the_counter_page_records_the_operator(): void
    {
        $token = $this->createCompletedToken();
        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::GOOD,
            'status' => Review::STATUS_PENDING,
        ]);

        $this->actingAs($this->operator())
            ->from(route('queue.reviews'))
            ->patch(route('admin.reviews.approve', $review))
            ->assertRedirect(route('queue.reviews'));

        $review->refresh();
        $this->assertSame(Review::STATUS_APPROVED, $review->status);
        $this->assertTrue($review->moderator->is($this->operator()));
    }
}

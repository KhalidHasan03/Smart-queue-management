<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Review;
use App\Models\Token;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            // Token/Patient have no factories in this project, so the review
            // attaches to an existing completed visit.
            'token_id' => fn () => Token::query()->inRandomOrder()->value('id'),
            'patient_id' => fn () => Patient::query()->inRandomOrder()->value('id'),
            'rating' => fake()->randomElement([Review::BAD, Review::NORMAL, Review::GOOD, Review::EXCELLENT]),
            'category' => fake()->randomElement(Review::CATEGORIES),
            'comment' => fake()->boolean(70) ? fake()->sentence(12) : null,
            'display_name' => fake()->boolean(60) ? fake()->name() : null,
            'is_anonymous' => fake()->boolean(30),
            'is_approved' => false,
            'status' => Review::STATUS_PENDING,
            'reviewed_at' => null,
        ];
    }

    public function forToken(Token $token): static
    {
        return $this->state(fn () => [
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
        ]);
    }

    public function rating(int $rating): static
    {
        return $this->state(fn () => ['rating' => $rating]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => Review::STATUS_APPROVED,
            'is_approved' => true,
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => Review::STATUS_REJECTED,
            'is_approved' => false,
            'reviewed_at' => now(),
        ]);
    }
}

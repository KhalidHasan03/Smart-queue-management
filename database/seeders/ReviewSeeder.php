<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Token;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Demo feedback for the token-linked review screens.
     *
     * Run explicitly so the default DatabaseSeeder (used by the test suite)
     * keeps its exact token/patient counts:
     *   php artisan db:seed --class=ReviewSeeder
     */
    public function run(): void
    {
        $tokens = Token::query()
            ->where('status', Token::COMPLETED)
            ->with('patient')
            ->orderByDesc('token_date')
            ->limit(12)
            ->get();

        if ($tokens->isEmpty()) {
            $this->command?->warn('No completed tokens found — nothing to seed.');

            return;
        }

        $samples = [
            [Review::EXCELLENT, 'staff', 'Doctor was very patient and explained everything clearly.'],
            [Review::GOOD, 'wait_time', 'Slightly long wait but the staff kept us updated.'],
            [Review::EXCELLENT, 'facility', 'Clean and organised waiting area.'],
            [Review::NORMAL, 'service', 'Service was fine, reception could be more welcoming.'],
            [Review::GOOD, 'service', 'Got help quickly and the counter was nearby.'],
            [Review::BAD, 'wait_time', 'Waited far longer than the given time.'],
        ];

        foreach ($tokens as $index => $token) {
            [$rating, $category, $comment] = $samples[$index % count($samples)];

            $approved = $index % 3 === 0;

            Review::updateOrCreate(
                ['token_id' => $token->id, 'patient_id' => $token->patient_id],
                [
                    'rating' => $rating,
                    'category' => $category,
                    'comment' => $comment,
                    'display_name' => $token->patient?->name,
                    'is_anonymous' => $index % 4 === 1,
                    'is_approved' => $approved,
                    'status' => $approved ? Review::STATUS_APPROVED : Review::STATUS_PENDING,
                    'reviewed_at' => $approved ? now() : null,
                ],
            );
        }

        $this->command?->info('Seeded reviews for '.$tokens->count().' completed tokens.');
    }
}

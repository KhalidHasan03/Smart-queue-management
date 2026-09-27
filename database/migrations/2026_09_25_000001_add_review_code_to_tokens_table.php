<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A review code is the patient's key to the feedback kiosk. Token numbers
     * restart every day per service (G-001 is a different person each day), so
     * they cannot identify a visit on their own. This column is globally
     * unique and is what the kiosk resolves.
     *
     * Nullable so the table gains instantly; historic rows are backfilled only
     * where a visit is still reviewable, and the rest stay NULL (a unique
     * index permits any number of NULLs in MySQL/MariaDB).
     */
    public function up(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            if (! Schema::hasColumn('tokens', 'review_code')) {
                $table->string('review_code', 8)->nullable()->unique()->after('token_no');
            }
        });

        $this->backfill();
    }

    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropUnique(['review_code']);
            $table->dropColumn('review_code');
        });
    }

    private function backfill(): void
    {
        $alphabet = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';
        $max = strlen($alphabet) - 1;

        DB::table('tokens')
            ->whereNull('review_code')
            ->where('status', 'completed')
            ->where('review_status', 'pending')
            ->orderBy('id')
            ->chunkById(200, function ($tokens) use ($alphabet, $max) {
                foreach ($tokens as $token) {
                    $attempts = 0;

                    do {
                        $code = '';
                        for ($i = 0; $i < 8; $i++) {
                            $code .= $alphabet[random_int(0, $max)];
                        }
                        $attempts++;
                    } while (
                        $attempts < 5
                        && DB::table('tokens')->where('review_code', $code)->exists()
                    );

                    DB::table('tokens')->where('id', $token->id)->update(['review_code' => $code]);
                }
            });
    }
};

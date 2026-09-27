<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Four-option emoji scale: 1 Bad, 2 Normal, 3 Good, 4 Excellent.
        // Historic 5-star rows collapse onto the top option.
        DB::table('reviews')->where('rating', '>', 4)->update(['rating' => 4]);

        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_approved');
            $table->timestamp('reviewed_at')->nullable()->after('status');
            $table->index('status');
            $table->index('rating');
        });

        // Existing approved rows keep their published state.
        DB::table('reviews')->where('is_approved', true)->update(['status' => 'approved']);

        Schema::table('tokens', function (Blueprint $table) {
            $table->enum('review_status', ['none', 'pending', 'submitted', 'approved', 'rejected'])
                ->default('none')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('reviews')->where('status', 'approved')->update(['is_approved' => true]);

        Schema::table('tokens', function (Blueprint $table) {
            $table->enum('review_status', ['none', 'pending', 'submitted'])->default('none')->change();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['rating']);
            $table->dropColumn(['status', 'reviewed_at']);
        });
    }
};

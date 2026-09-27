<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->boolean('is_open')->default(true)->after('is_active')->index();
            $table->timestamp('opened_at')->nullable()->after('is_open');
            $table->timestamp('closed_at')->nullable()->after('opened_at');
        });

        // No existing actor can be credited for the default state, so the first
        // open is simply stamped as of the migration.
        DB::table('counters')
            ->where('is_open', true)
            ->update(['opened_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('counters', function (Blueprint $table) {
            $table->dropIndex(['is_open']);
            $table->dropColumn(['is_open', 'opened_at', 'closed_at']);
        });
    }
};

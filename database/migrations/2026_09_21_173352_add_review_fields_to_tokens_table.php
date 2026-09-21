<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->enum('review_status', ['none', 'pending', 'submitted'])->default('none')->after('status');
            $table->timestamp('review_requested_at')->nullable()->after('review_status');
        });
    }

    public function down(): void
    {
        Schema::table('tokens', function (Blueprint $table) {
            $table->dropColumn(['review_status', 'review_requested_at']);
        });
    }
};

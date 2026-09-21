<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->enum('category', ['service', 'wait_time', 'staff', 'facility', 'overall'])->nullable()->after('rating');
            $table->boolean('is_anonymous')->default(true)->after('display_name');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['category', 'is_anonymous']);
        });
    }
};

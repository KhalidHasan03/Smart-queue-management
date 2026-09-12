<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('receptionist')->after('email');
            $table->foreignId('counter_id')->nullable()->after('role')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('counter_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'counter_id', 'is_active']);
        });
    }
};

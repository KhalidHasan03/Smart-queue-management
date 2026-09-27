<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('reviews', 'moderated_by')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            // Who pressed approve/reject. Null for auto-approved reviews, which
            // never pass through a person.
            $table->foreignId('moderated_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('reviews', 'moderated_by')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
        });
    }
};

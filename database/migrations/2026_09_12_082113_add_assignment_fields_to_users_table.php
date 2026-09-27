<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // constrained() is required, not just cosmetic: without it these
            // are plain unsigned columns and the down() below has no foreign
            // key to drop, which fails with "Can't DROP FOREIGN KEY" (1091).
            $table->foreignId('service_id')->nullable()->after('counter_id')->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->after('service_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Guarded, because this migration was already applied to some
        // databases before up() declared the constraints. Those databases have
        // the columns but no foreign keys, so an unconditional drop fails with
        // MySQL error 1091. The companion migration
        // 2026_09_25_000002_add_missing_user_assignment_foreign_keys backfills
        // the keys, and this drops whichever ones actually exist.
        $hasForeignKey = function (string $column): bool {
            $row = DB::selectOne(
                'SELECT COUNT(*) AS aggregate FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL',
                ['users', $column]
            );

            return (int) $row->aggregate > 0;
        };

        if ($hasForeignKey('service_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('users_service_id_foreign');
            });
        }

        if ($hasForeignKey('doctor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('users_doctor_id_foreign');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['service_id', 'doctor_id']);
        });
    }
};

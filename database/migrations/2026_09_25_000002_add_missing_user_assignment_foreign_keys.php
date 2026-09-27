<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Repairs databases that ran 2026_09_12_082113 before its up() declared the
 * constraints. That migration created `users.service_id` and
 * `users.doctor_id` as plain unsigned columns, so the intended foreign keys
 * were never created even though its down() tried to drop them.
 *
 * Each constraint is added only when missing, so this is a no-op on a database
 * that already got them from the corrected original migration.
 */
return new class extends Migration
{
    protected function hasForeignKey(string $column): bool
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS aggregate FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            ['users', $column]
        );

        return (int) $row->aggregate > 0;
    }

    public function up(): void
    {
        if (! Schema::hasColumn('users', 'service_id') || ! Schema::hasColumn('users', 'doctor_id')) {
            return;
        }

        if (! $this->hasForeignKey('service_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('service_id')->references('id')->on('services')->nullOnDelete();
            });
        }

        if (! $this->hasForeignKey('doctor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('doctor_id')->references('id')->on('doctors')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $constraints = [
            'service_id' => 'users_service_id_foreign',
            'doctor_id' => 'users_doctor_id_foreign',
        ];

        foreach ($constraints as $column => $name) {
            if ($this->hasForeignKey($column)) {
                Schema::table('users', fn (Blueprint $table) => $table->dropForeign($name));
            }
        }
    }
};

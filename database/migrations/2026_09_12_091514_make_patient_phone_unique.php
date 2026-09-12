<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $normalize = fn (string $phone): string => self::normalize($phone);

        foreach (DB::table('patients')->orderBy('id')->cursor() as $patient) {
            $normalized = $normalize($patient->phone);
            if ($normalized !== $patient->phone) {
                DB::table('patients')->where('id', $patient->id)->update(['phone' => $normalized]);
            }
        }

        $dupes = DB::table('patients')
            ->select('phone', DB::raw('MIN(id) as keep_id'))
            ->groupBy('phone')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($dupes as $dupe) {
            $ids = DB::table('patients')->where('phone', $dupe->phone)
                ->where('id', '!=', $dupe->keep_id)->pluck('id');
            DB::table('tokens')->whereIn('patient_id', $ids)->update(['patient_id' => $dupe->keep_id]);
            DB::table('patients')->whereIn('id', $ids)->delete();
        }

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->unique('phone');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->index('phone');
        });
    }

    private static function normalize(string $phone): string
    {
        $phone = trim($phone);
        $plus = str_starts_with($phone, '+');
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        return ($plus ? '+' : '').$digits;
    }
};

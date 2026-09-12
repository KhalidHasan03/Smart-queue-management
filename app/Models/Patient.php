<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = ['name', 'phone', 'age', 'gender', 'address'];

    protected static function booted(): void
    {
        static::saving(function (Patient $patient) {
            $patient->phone = static::normalizePhone($patient->phone ?? '');
        });
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = trim($phone);
        $plus = str_starts_with($phone, '+');
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        return ($plus ? '+' : '').$digits;
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}

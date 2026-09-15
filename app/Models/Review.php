<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'token_id',
        'patient_id',
        'rating',
        'comment',
        'display_name',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}

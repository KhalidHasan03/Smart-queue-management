<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'token_id',
        'patient_id',
        'rating',
        'category',
        'comment',
        'display_name',
        'is_anonymous',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_anonymous' => 'boolean',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = ['name', 'specialization', 'service_id', 'room_no', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}

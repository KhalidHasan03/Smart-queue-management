<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'prefix', 'start_number', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public function counters()
    {
        return $this->hasMany(Counter::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }
}

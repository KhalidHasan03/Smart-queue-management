<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = ['name', 'room_no', 'service_id', 'is_active', 'show_on_display', 'current_token_id'];

    protected $casts = ['is_active' => 'boolean', 'show_on_display' => 'boolean'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function operators()
    {
        return $this->hasMany(User::class);
    }

    public function currentToken()
    {
        return $this->belongsTo(Token::class, 'current_token_id');
    }
}

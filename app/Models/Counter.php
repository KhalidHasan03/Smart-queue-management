<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    protected $fillable = [
        'name', 'room_no', 'service_id', 'is_active', 'is_open', 'show_on_display', 'current_token_id',
        'opened_at', 'closed_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_open' => 'boolean',
        'show_on_display' => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

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

    /**
     * Counters that are taking patients right now: enabled and switched on.
     * This is the set that decides whether a waiting patient is visible.
     */
    public function scopeServing(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('is_open', true);
    }

    public function isOpen(): bool
    {
        return (bool) $this->is_open;
    }

    public function markOpen(): self
    {
        $this->forceFill([
            'is_open' => true,
            'opened_at' => now(),
            'closed_at' => null,
        ])->save();

        return $this;
    }

    public function markClosed(): self
    {
        $this->forceFill([
            'is_open' => false,
            'closed_at' => now(),
        ])->save();

        return $this;
    }

    /**
     * Service ids that currently have at least one counter taking patients.
     * A waiting token is only shown once its service is in this list, so a
     * closed counter hides its queue without stranding patients who could
     * still be called by a sibling counter on the same service.
     *
     * @return array<int, int>
     */
    public static function servingServiceIds(): array
    {
        return static::query()->serving()->distinct()->pluck('service_id')
            ->map(fn ($id) => (int) $id)->all();
    }
}

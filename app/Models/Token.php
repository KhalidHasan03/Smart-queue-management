<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public const WAITING = 'waiting';

    public const CALLING = 'calling';

    public const SERVING = 'serving';

    public const COMPLETED = 'completed';

    public const SKIPPED = 'skipped';

    public const CANCELLED = 'cancelled';

    public const STATUSES = [
        self::WAITING,
        self::CALLING,
        self::SERVING,
        self::COMPLETED,
        self::SKIPPED,
        self::CANCELLED,
    ];

    protected $fillable = [
        'token_date', 'seq', 'token_no', 'review_code',
        'service_id', 'doctor_id', 'counter_id', 'patient_id',
        'status', 'called_at', 'started_at', 'finished_at',
        'notes', 'created_by',
        'review_status', 'review_requested_at',
    ];

    protected $casts = [
        'token_date' => 'date',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'review_requested_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function counter()
    {
        return $this->belongsTo(Counter::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::WAITING, self::CALLING, self::SERVING], true);
    }

    /**
     * A waiting patient is only visible while their service has a counter that
     * is switched on. Scope is per service rather than per counter because
     * QueueService::next() pulls the next patient by service_id — closing one
     * desk must not hide patients a sibling desk could still call.
     */
    public function scopeAtOpenCounter(Builder $query): Builder
    {
        $query->whereIn(
            'service_id',
            Counter::query()->serving()->distinct()->select('service_id')
        );

        return $query;
    }

    /**
     * True when this token can be pulled by an open counter right now.
     */
    public function isVisibleOnQueue(): bool
    {
        return $this->status === self::WAITING
            && in_array((int) $this->service_id, Counter::servingServiceIds(), true);
    }

    /**
     * The kiosk link for this visit. The review code is the key: token numbers
     * restart daily per service, so only the code identifies one visit.
     */
    public function getReviewUrlAttribute(): string
    {
        return route('review.kiosk', ['code' => $this->review_code]);
    }

    public function hasReviewCode(): bool
    {
        return is_string($this->review_code) && $this->review_code !== '';
    }

    public function canBeReviewed(): bool
    {
        return $this->status === self::COMPLETED
            && $this->review_status === 'pending'
            && ! $this->review?->exists
            && $this->hasReviewCode();
    }
}

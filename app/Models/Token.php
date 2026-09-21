<?php

namespace App\Models;

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
        'token_date', 'seq', 'token_no',
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

    public function getReviewUrlAttribute(): string
    {
        return route('review.show', $this->token_no);
    }

    public function canBeReviewed(): bool
    {
        return $this->status === self::COMPLETED
            && $this->review_status === 'pending'
            && ! $this->review?->exists;
    }
}

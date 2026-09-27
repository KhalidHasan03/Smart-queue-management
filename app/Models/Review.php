<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public const BAD = 1;

    public const NORMAL = 2;

    public const GOOD = 3;

    public const EXCELLENT = 4;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    /**
     * The four patient-facing options, in display order.
     */
    public const RATINGS = [
        self::BAD => ['label' => 'Bad', 'emoji' => '😞', 'color' => 'rose'],
        self::NORMAL => ['label' => 'Normal', 'emoji' => '😐', 'color' => 'amber'],
        self::GOOD => ['label' => 'Good', 'emoji' => '🙂', 'color' => 'teal'],
        self::EXCELLENT => ['label' => 'Excellent', 'emoji' => '🤩', 'color' => 'emerald'],
    ];

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
    ];

    public const CATEGORIES = ['service', 'wait_time', 'staff', 'facility', 'overall'];

    /** Ratings at or below this value are escalated to admins. */
    public const NEGATIVE_THRESHOLD = self::NORMAL;

    protected $fillable = [
        'token_id',
        'patient_id',
        'rating',
        'category',
        'comment',
        'display_name',
        'is_anonymous',
        'is_approved',
        'status',
        'reviewed_at',
        'moderated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'is_anonymous' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /** Whoever pressed approve/reject. Null for auto-approved reviews. */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }

    /**
     * Restrict to the feedback a user is allowed to moderate. Admins see
     * everything; everyone else only sees their own counter's visits.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        $counterId = $user->counter_id ?: $user->counter?->id;

        if (! $counterId) {
            // No counter assigned means no scope to widen into — show nothing
            // rather than silently exposing the whole clinic's feedback.
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('token', fn ($q) => $q->where('counter_id', $counterId));
    }

    public function getRatingEmojiAttribute(): string
    {
        return self::RATINGS[$this->rating]['emoji'] ?? '🙂';
    }

    public function getRatingLabelAttribute(): string
    {
        return self::RATINGS[$this->rating]['label'] ?? 'Unknown';
    }

    public function getRatingColorAttribute(): string
    {
        return self::RATINGS[$this->rating]['color'] ?? 'slate';
    }

    /** Name safe to show publicly: respects the anonymous flag. */
    public function getAuthorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous';
        }

        return $this->display_name ?: ($this->patient?->name ?: 'Anonymous');
    }

    public function isNegative(): bool
    {
        return $this->rating <= self::NEGATIVE_THRESHOLD;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Move the review through the moderation state machine, keeping the
     * legacy is_approved flag and the owning token in sync.
     *
     * The acting user is recorded so an approve/reject is always attributable.
     * Passing an explicit actor lets tests (and any future system action) stay
     * deterministic; otherwise the authenticated user is used.
     */
    public function moderate(string $status, ?User $actor = null): self
    {
        if (! in_array($status, self::STATUSES, true)) {
            $status = self::STATUS_PENDING;
        }

        $actor ??= auth()->user();

        $this->forceFill([
            'status' => $status,
            'is_approved' => $status === self::STATUS_APPROVED,
            'reviewed_at' => now(),
            'moderated_by' => $actor?->getKey(),
        ])->save();

        $this->token?->update(['review_status' => $status]);

        return $this;
    }
}

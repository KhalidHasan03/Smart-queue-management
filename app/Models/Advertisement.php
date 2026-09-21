<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    public const TYPE_VIDEO = 'video';

    public const TYPE_IMAGE = 'image';

    public const TYPE_YOUTUBE = 'youtube';

    public const TYPE_TEXT = 'text';

    public const TYPES = [self::TYPE_VIDEO, self::TYPE_IMAGE, self::TYPE_YOUTUBE, self::TYPE_TEXT];

    protected $fillable = [
        'title',
        'description',
        'media_type',
        'media_path',
        'youtube_url',
        'duration_secs',
        'is_active',
        'is_live',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_live' => 'boolean',
        'duration_secs' => 'integer',
        'sort_order' => 'integer',
    ];

    public function getYoutubeIdAttribute(): ?string
    {
        return $this->youtube_url ? self::youtubeIdFromUrl($this->youtube_url) : null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://www.youtube.com/embed/'.$this->youtube_id.'?rel=0'
            : null;
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? asset('storage/'.$this->media_path) : null;
    }

    public static function youtubeIdFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function getMediaTypeLabelAttribute(): string
    {
        return self::TYPES[$this->media_type] ?? $this->media_type;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

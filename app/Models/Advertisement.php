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
        'youtube_embed',
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
            ? 'https://www.youtube.com/embed/'.$this->youtube_id.'?rel=0&autoplay=1&mute=1&playsinline=1'
            : null;
    }

    public function getHasEmbedAttribute(): bool
    {
        return $this->media_type === self::TYPE_YOUTUBE && ! empty($this->youtube_embed);
    }

    public function getYoutubeThumbnailAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://i.ytimg.com/vi/'.$this->youtube_id.'/hqdefault.jpg'
            : null;
    }

    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? asset('storage/'.$this->media_path) : null;
    }

    /**
     * MIME type for the stored file so the display can render <video type>,
     * <img> fallbacks without relying on the web server's extension mapping.
     */
    public function getMediaMimeAttribute(): ?string
    {
        if (! $this->media_path) {
            return null;
        }

        $extension = strtolower(pathinfo($this->media_path, PATHINFO_EXTENSION));

        return match ($this->media_type) {
            self::TYPE_VIDEO => $extension === 'webm' ? 'video/webm' : 'video/mp4',
            self::TYPE_IMAGE => match ($extension) {
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'bmp' => 'image/bmp',
                default => 'image/jpeg',
            },
            default => null,
        };
    }

    public static function youtubeIdFromUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('~(?:youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Accept either a plain YouTube URL or a full <iframe> embed snippet and
     * return a safe, normalized pair. Only YouTube-hosted video sources are
     * allowed; the embed code is rebuilt from the extracted video id so no
     * attribute the admin pastes ever reaches the rendered HTML.
     *
     * @return array{url: string, embed: string}|null
     */
    public static function sanitizeEmbedFromInput(?string $input): ?array
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);

        if ($input === '') {
            return null;
        }

        // The embed may arrive HTML-entity-escaped (e.g. re-submitted textarea
        // content read back as &lt;iframe&gt; / &amp;). Decode it first so both
        // iframe detection and URL extraction see the real characters.
        $input = html_entity_decode($input, ENT_QUOTES | ENT_HTML5);

        $src = $input;

        if (stripos($input, '<iframe') !== false) {
            if (! preg_match('/<iframe\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1[^>]*>/is', $input, $m)) {
                return null;
            }
            $src = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5);
        }

        $videoId = self::youtubeIdFromUrl($src);

        if (! $videoId) {
            return null;
        }

        $embedUrl = 'https://www.youtube-nocookie.com/embed/'.$videoId.'?rel=0&autoplay=1&mute=1&playsinline=1';

        return [
            'url' => 'https://www.youtube.com/watch?v='.$videoId,
            'embed' => sprintf(
                '<iframe src="%s" style="border:0;width:100%%;height:100%%;aspect-ratio:16/9" class="h-full w-full" title="YouTube video" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; accelerometer; gyroscope" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
                $embedUrl
            ),
        ];
    }

    protected function getMediaTypeLabelAttribute(): string
    {
        return match ($this->media_type) {
            self::TYPE_VIDEO => 'Video',
            self::TYPE_IMAGE => 'Image',
            self::TYPE_YOUTUBE => 'YouTube',
            self::TYPE_TEXT => 'Text',
            default => ucfirst($this->media_type),
        };
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

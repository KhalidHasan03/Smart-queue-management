<?php

namespace App\Support;

use App\Models\Review;
use App\Models\Setting;

/**
 * All kiosk + review-policy knobs live in the existing settings key/value
 * table, so they can be tuned without a migration. Defaults are applied here
 * and mirrored in the Review Kiosk Setup form.
 */
class ReviewSettings
{
    public const ENABLED = 'reviews.kiosk_enabled';

    public const HEADLINE = 'reviews.kiosk_headline';

    public const INSTRUCTION = 'reviews.kiosk_instruction';

    public const IDLE_SECS = 'reviews.kiosk_idle_secs';

    public const AUTO_APPROVE_FROM = 'reviews.auto_approve_from';

    public const REQUIRE_COMMENT_BELOW = 'reviews.require_comment_below';

    public const THROTTLE_PER_MIN = 'reviews.throttle_per_min';

    /**
     * HTML form field names cannot contain a dot — PHP rewrites
     * `reviews.kiosk_headline` to `reviews_kiosk_headline` on submit. So the
     * setup form uses these short, dot-free names and the controller maps them
     * back to the real setting keys.
     */
    public const FIELDS = [
        self::ENABLED => 'kiosk_enabled',
        self::HEADLINE => 'kiosk_headline',
        self::INSTRUCTION => 'kiosk_instruction',
        self::IDLE_SECS => 'kiosk_idle_secs',
        self::AUTO_APPROVE_FROM => 'auto_approve_from',
        self::REQUIRE_COMMENT_BELOW => 'require_comment_below',
        self::THROTTLE_PER_MIN => 'throttle_per_min',
    ];

    public const DEFAULTS = [
        self::ENABLED => '1',
        self::HEADLINE => 'How was your visit?',
        self::INSTRUCTION => 'Enter the review code printed on your slip, then confirm the last 4 digits of your phone.',
        self::IDLE_SECS => '20',
        self::AUTO_APPROVE_FROM => '3',
        self::REQUIRE_COMMENT_BELOW => '2',
        self::THROTTLE_PER_MIN => '10',
    ];

    public static function enabled(): bool
    {
        return (bool) (int) static::get(self::ENABLED);
    }

    public static function headline(): string
    {
        return static::get(self::HEADLINE) ?: 'How was your visit?';
    }

    public static function instruction(): string
    {
        return static::get(self::INSTRUCTION) ?: 'Enter the review code printed on your slip.';
    }

    public static function idleSeconds(): int
    {
        return max(5, min(300, (int) static::get(self::IDLE_SECS)));
    }

    /**
     * Ratings at or above this value publish immediately. A threshold of 5 is
     * above the maximum rating, which disables auto-approval entirely.
     */
    public static function autoApproveFrom(): int
    {
        $value = (int) static::get(self::AUTO_APPROVE_FROM);

        return max(1, min(5, $value));
    }

    /**
     * Force a comment when the rating is at or below this value. 0 disables.
     */
    public static function requireCommentBelow(): int
    {
        return max(0, min(4, (int) static::get(self::REQUIRE_COMMENT_BELOW)));
    }

    public static function throttlePerMinute(): int
    {
        return max(3, min(60, (int) static::get(self::THROTTLE_PER_MIN)));
    }

    public static function autoApproveThresholdReached(int $rating): bool
    {
        $threshold = static::autoApproveFrom();

        return $threshold <= Review::EXCELLENT && $rating >= $threshold;
    }

    public static function commentRequired(int $rating): bool
    {
        $threshold = static::requireCommentBelow();

        return $threshold > 0 && $rating <= $threshold;
    }

    public static function get(string $key): ?string
    {
        return Setting::get($key, static::DEFAULTS[$key] ?? null);
    }

    /**
     * The dot-free form field name for a setting key.
     */
    public static function field(string $key): string
    {
        return self::FIELDS[$key] ?? $key;
    }
}

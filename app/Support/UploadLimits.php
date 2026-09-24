<?php

namespace App\Support;

/**
 * Resolves the effective maximum file size for uploads from PHP's own
 * configuration. Validation must never promise more than the server can
 * actually accept: PHP's `post_max_size` caps the *entire* multipart body
 * (file + boundaries + form fields), so the per-file validation limit is
 * clamped below it with a safety margin. Otherwise a file that "passes"
 * validation would be killed by PHP with a PostTooLargeException before
 * Laravel ever runs.
 */
class UploadLimits
{
    public static function maxKb(int $wantedKb): int
    {
        $constraint = null;

        foreach ([ini_get('post_max_size'), ini_get('upload_max_filesize')] as $value) {
            $bytes = self::bytes($value);
            if ($bytes <= 0) {
                continue; // unset / '-1' / disabled = no practical limit
            }
            $constraint = $constraint === null ? $bytes : min($constraint, $bytes);
        }

        if ($constraint === null) {
            return $wantedKb;
        }

        // ~3 MB headroom for the multipart envelope and other form fields.
        $safeKb = (int) floor(($constraint - 3 * 1024 * 1024) / 1024);

        return max(1024, min($wantedKb, $safeKb));
    }

    private static function bytes(int|string $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        if ($value === '' || $value === '-1' || $value === '0') {
            return -1;
        }

        if (preg_match('/^(\d+)\s*([kmg])?b?$/', $value, $m)) {
            return (int) $m[1] * match ($m[2]) {
                'k' => 1024,
                'm' => 1024 * 1024,
                'g' => 1024 * 1024 * 1024,
                default => 1,
            };
        }

        return (int) $value;
    }
}
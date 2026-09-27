<?php

namespace App\Support;

use App\Models\Token;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReviewCode
{
    /**
     * Ambiguous glyphs (0/O, 1/I/L) are omitted so a code read off a printed
     * slip can be retyped on a touchscreen keypad without ambiguity.
     */
    public const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    public const LENGTH = 8;

    private const MAX_ATTEMPTS = 8;

    /**
     * Generate a code that is not already taken. The alphabet is 31 symbols
     * (0/O and 1/I/L dropped) and the code is 8 long, so the space is about
     * 8.5e11 — collisions are vanishingly unlikely, but the retry keeps the
     * unique index honest under any circumstance rather than trusting the odds.
     */
    public static function generate(): string
    {
        $max = strlen(self::ALPHABET) - 1;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            $code = self::random($max);

            if (! self::taken($code)) {
                return $code;
            }
        }

        throw new RuntimeException('Unable to generate a unique review code.');
    }

    /**
     * Normalise patient-typed input: uppercase, strip anything outside the
     * alphabet, truncate to the fixed length.
     */
    public static function normalize(?string $input): string
    {
        $clean = strtoupper((string) $input);
        $clean = preg_replace('/[^'.self::ALPHABET.']/', '', $clean) ?? '';

        return substr($clean, 0, self::LENGTH);
    }

    public static function isWellFormed(?string $input): bool
    {
        return strlen(self::normalize($input)) === self::LENGTH;
    }

    public static function taken(string $code): bool
    {
        return DB::table('tokens')->where('review_code', $code)->exists();
    }

    public static function forToken(Token $token): string
    {
        if (is_string($token->review_code) && $token->review_code !== '') {
            return $token->review_code;
        }

        throw new RuntimeException('Token '.$token->token_no.' has no review code.');
    }

    private static function random(int $max): string
    {
        $code = '';

        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }
}

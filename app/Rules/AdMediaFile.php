<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

/**
 * Validates an advertisement media upload by its original extension or its
 * detected MIME type, whichever matches. PHP's `fileinfo` extension is
 * unreliable for many real-world video containers (a perfectly valid .mp4 can
 * be reported as `application/octet-stream` or `video/x-matroska`), so we do
 * not rely on MIME detection alone like the built-in `mimes` rule does.
 *
 * We deliberately accept only browser-playable formats (MP4/WebM for video) so
 * anything an admin uploads is guaranteed to render on the display.
 */
class AdMediaFile implements Rule
{
    /**
     * @param  string[]  $extensions  Allowed file extensions (lowercase).
     * @param  string  $family  'video' or 'image'.
     * @param  string[]  $mimeTypes  Allowed detected MIME types (lowercase).
     */
    public function __construct(
        private readonly array $extensions,
        private readonly string $family,
        private readonly array $mimeTypes = [],
    ) {
    }

    public function passes($attribute, $value): bool
    {
        if ($value === null) {
            return true; // "required"/"nullable" rules handle empty input
        }

        if (! $value instanceof UploadedFile || ! $value->isValid()) {
            return false;
        }

        $extension = strtolower(trim((string) $value->getClientOriginalExtension()));

        if (in_array($extension, $this->extensions, true)) {
            // Accept by extension even when the detected MIME is unreliable —
            // but not when it clearly belongs to another media family (e.g. an
            // image disguised as an .mp4).
            $mime = strtolower(trim((string) $value->getMimeType()));
            if ($mime !== '' && str_starts_with($mime, 'image/') && $this->family !== 'image') {
                return false;
            }
            if ($mime !== '' && str_starts_with($mime, 'video/') && $this->family !== 'video') {
                return false;
            }

            return true;
        }

        return $this->mimeTypes !== [] && in_array(strtolower(trim((string) $value->getMimeType())), $this->mimeTypes, true);
    }

    public function message(): string
    {
        $formats = array_unique(array_merge($this->extensions, array_map(
            fn (string $mime) => str_replace($this->family.'/', '', $mime),
            $this->mimeTypes
        )));

        return 'The :attribute must be a valid '.$this->family.' file ('.implode(', ', $formats).').';
    }
}
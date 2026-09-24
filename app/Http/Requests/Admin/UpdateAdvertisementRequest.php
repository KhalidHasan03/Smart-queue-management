<?php

namespace App\Http\Requests\Admin;

use App\Models\Advertisement;
use App\Rules\AdMediaFile;
use App\Support\UploadLimits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('adverts.manage') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $input = $this->all();

        // Text ads store their on-screen copy in the `description` column; the
        // form posts it as `text_content`.
        if (($input['media_type'] ?? '') === Advertisement::TYPE_TEXT) {
            $input['description'] = trim((string) ($input['text_content'] ?? ''));
            $input['text_content'] = $input['description'];
        }

        $this->merge($input);
    }

    public function rules(): array
    {
        $mediaType = $this->input('media_type');

        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required_if:media_type,text', 'nullable', 'string', 'max:2000'],
            'media_type' => ['required', Rule::in(Advertisement::TYPES)],
            'media_file' => $this->mediaFileRules($mediaType),
            'youtube_embed' => $this->youtubeEmbedRules(),
            'duration_secs' => ['nullable', 'integer', 'min:3', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    private function youtubeEmbedRules(): array
    {
        return [
            'required_if:media_type,youtube',
            'nullable',
            'string',
            'max:5000',
            function ($attribute, $value, $fail) {
                if ($value !== null && trim((string) $value) !== '' && Advertisement::sanitizeEmbedFromInput($value) === null) {
                    $fail('The YouTube embed link must be a YouTube video URL or a YouTube iframe embed code.');
                }
            },
        ];
    }

    private function mediaFileRules(string $mediaType): array
    {
        $advertisement = $this->route('advertisement');

        return match ($mediaType) {
            Advertisement::TYPE_VIDEO => $this->mediaFileRule(
                'video',
                ['mp4', 'webm'],
                ['video/mp4', 'video/webm'],
                $this->requiresNewFile($advertisement, Advertisement::TYPE_VIDEO),
                UploadLimits::maxKb(51200)
            ),
            Advertisement::TYPE_IMAGE => $this->mediaFileRule(
                'image',
                ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'],
                ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp'],
                $this->requiresNewFile($advertisement, Advertisement::TYPE_IMAGE),
                5120
            ),
            default => ['nullable'],
        };
    }

    /**
     * A file becomes mandatory when the admin switches a media type into a
     * file-based one without an existing stored file of that type — otherwise
     * the saved advertisement would render as a broken image/video box.
     */
    private function requiresNewFile(?Advertisement $advertisement, string $family): bool
    {
        if ($advertisement === null) {
            return true;
        }

        if ($advertisement->media_type !== $family) {
            return true;
        }

        return blank($advertisement->media_path);
    }

    private function mediaFileRule(string $family, array $extensions, array $mimeTypes, bool $required, int $maxKb): array
    {
        $rules = [
            'file',
            "max:{$maxKb}",
            function ($attribute, $value, $fail) use ($family) {
                if ($value === null) {
                    return;
                }
                if (! $value instanceof \Illuminate\Http\UploadedFile || ! $value->isValid()) {
                    $limit = ini_get('upload_max_filesize') ?: 'unknown';
                    $fail("The {$family} file was not uploaded. The server upload limit is {$limit}.");
                }
            },
            new AdMediaFile($extensions, $family, $mimeTypes),
        ];

        if ($required) {
            array_unshift($rules, 'required');
        } else {
            array_unshift($rules, 'nullable');
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'media_file.required' => 'A media file is required because you changed the media type (or the current ad has no stored file). Upload a new one or keep the existing media type.',
            'media_file.max' => 'The uploaded file is too large. This server accepts files up to :max KB.',
        ];
    }
}

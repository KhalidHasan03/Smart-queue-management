<?php

namespace App\Http\Requests\Admin;

use App\Models\Advertisement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdvertisementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('adverts.manage') ?? false;
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
        return match ($mediaType) {
            Advertisement::TYPE_VIDEO => ['nullable', 'file', 'mimes:mp4,webm,mov,ogg', 'max:51200'],
            Advertisement::TYPE_IMAGE => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            default => ['nullable'],
        };
    }
}

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
            'youtube_url' => ['required_if:media_type,youtube', 'nullable', 'url', 'max:500'],
            'duration_secs' => ['nullable', 'integer', 'min:3', 'max:600'],
            'is_active' => ['sometimes', 'boolean'],
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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdvertisementRequest;
use App\Http\Requests\Admin\UpdateAdvertisementRequest;
use App\Models\Advertisement;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::orderBy('sort_order')->orderBy('id')->paginate(100);
        $config = $this->config();

        return view('admin.advertisements.index', compact('advertisements', 'config'));
    }

    public function create()
    {
        return view('admin.advertisements.create', [
            'defaultDuration' => $this->config()['duration_secs'],
        ]);
    }

    public function store(StoreAdvertisementRequest $request)
    {
        $data = $request->safe()->except(['media_file', 'is_active', 'youtube_embed', 'youtube_url']);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) Advertisement::max('sort_order') + 1;
        $data = $this->applyYoutubeEmbed($data, $request);
        $data['duration_secs'] = $this->resolveDuration($data['duration_secs'] ?? null);

        $advertisement = Advertisement::create($data);
        $this->handleMediaUpload($request, $advertisement);

        // The first advertisement becomes the live one automatically so the playlist
        // never starts empty on the patient display — but only if it is active.
        if ($data['is_active'] && Advertisement::where('is_live', true)->doesntExist()) {
            $advertisement->update(['is_live' => true]);
        }

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement created.');
    }

    public function edit(Advertisement $advertisement)
    {
        return view('admin.advertisements.edit', [
            'advertisement' => $advertisement,
            'defaultDuration' => $this->config()['duration_secs'],
        ]);
    }

    public function update(UpdateAdvertisementRequest $request, Advertisement $advertisement)
    {
        $data = $request->safe()->except(['media_file', 'is_active', 'youtube_embed', 'youtube_url']);
        $data['is_active'] = $request->boolean('is_active');
        $data = $this->applyYoutubeEmbed($data, $request);
        $data['duration_secs'] = $this->resolveDuration($data['duration_secs'] ?? null);

        $advertisement->update($data);
        $this->handleMediaUpload($request, $advertisement);

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement updated.');
    }

    public function destroy(Advertisement $advertisement)
    {
        $this->deleteMediaFile($advertisement);
        $advertisement->delete();

        // If the live advertisement was deleted, promote the first active one so the
        // patient display playlist keeps a working item.
        if (Advertisement::where('is_live', true)->doesntExist()) {
            $this->promoteLive();
        }

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement deleted.');
    }

    public function toggle(Advertisement $advertisement)
    {
        $advertisement->is_active = ! $advertisement->is_active;
        $advertisement->save();

        if (! $advertisement->is_active && $advertisement->is_live) {
            $advertisement->is_live = false;
            $advertisement->save();
            $this->promoteLive();
        }

        // Resuming an ad in single mode: make sure the display still has a live item.
        if ($advertisement->is_active && Advertisement::where('is_live', true)->doesntExist()) {
            $this->promoteLive();
        }

        return back()->with('success', 'Advertisement updated.');
    }

    public function setLive(Advertisement $advertisement)
    {
        Advertisement::query()->update(['is_live' => false]);
        $advertisement->update(['is_live' => true, 'is_active' => true]);

        return back()->with('success', 'Advertisement is now live on the patient display.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        foreach (array_values($request->input('order', [])) as $index => $id) {
            Advertisement::whereKey($id)->update(['sort_order' => $index]);
        }

        return back()->with('success', 'Display order saved.');
    }

    public function settings()
    {
        return view('admin.advertisements.settings', ['config' => $this->config()]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'is_enabled' => ['sometimes', 'boolean'],
            'mode' => ['required', Rule::in(['cycle', 'single'])],
            'duration_secs' => ['required', 'integer', 'min:3', 'max:600'],
            'position' => ['sometimes', Rule::in(['right', 'left', 'bottom'])],
        ]);

        Setting::set('advert.enabled', $request->boolean('is_enabled') ? '1' : '0');
        Setting::set('advert.mode', $request->input('mode'));
        Setting::set('advert.duration_secs', (string) $request->input('duration_secs'));
        Setting::set('advert.position', $request->input('position', 'right'));

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement settings saved.');
    }

    protected function applyYoutubeEmbed(array $data, Request $request): array
    {
        if (($data['media_type'] ?? '') !== Advertisement::TYPE_YOUTUBE) {
            $data['youtube_embed'] = null;
            $data['youtube_url'] = null;

            return $data;
        }

        $sanitized = Advertisement::sanitizeEmbedFromInput($request->input('youtube_embed'));

        if ($sanitized) {
            $data['youtube_embed'] = $sanitized['embed'];
            $data['youtube_url'] = $sanitized['url'];
        } else {
            $data['youtube_embed'] = null;
            $data['youtube_url'] = null;
        }

        return $data;
    }

    protected function handleMediaUpload(Request $request, Advertisement $advertisement): void
    {
        $usesFile = in_array($advertisement->media_type, [
            Advertisement::TYPE_IMAGE,
            Advertisement::TYPE_VIDEO,
        ], true);

        if ($usesFile) {
            if ($request->hasFile('media_file')) {
                $this->deleteMediaFile($advertisement);
                $mediaPath = $this->storeMediaFile($request->file('media_file'), $advertisement->media_type);
                $advertisement->update(['media_path' => $mediaPath]);
            }
        } else {
            $this->deleteMediaFile($advertisement);
        }
    }

    /**
     * Store the uploaded media under a randomized name with a safe extension
     * derived from the validated upload — never from PHP's MIME guesser, which
     * routinely names real videos ".bin" and makes them unplayable on the
     * display.
     */
    protected function storeMediaFile(UploadedFile $file, string $mediaType): string
    {
        $extension = strtolower(trim($file->getClientOriginalExtension()));

        $safeExtension = match ($mediaType) {
            Advertisement::TYPE_VIDEO => in_array($extension, ['mp4', 'webm'], true) ? $extension : 'mp4',
            Advertisement::TYPE_IMAGE => in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true)
                ? ($extension === 'jpeg' ? 'jpg' : $extension)
                : 'jpg',
            default => 'jpg',
        };

        return $file->storeAs('ads', Str::random(40).'.'.$safeExtension, 'public');
    }

    protected function resolveDuration(?int $durationSecs): int
    {
        $global = (int) Setting::get('advert.duration_secs', '15');

        return max(3, $durationSecs ?? $global);
    }

    protected function deleteMediaFile(Advertisement $advertisement): void
    {
        if ($advertisement->media_path) {
            Storage::disk('public')->delete($advertisement->media_path);
            $advertisement->update(['media_path' => null]);
        }
    }

    protected function promoteLive(): void
    {
        Advertisement::active()->orderBy('sort_order')->orderBy('id')
            ->limit(1)->get()->each->update(['is_live' => true]);
    }

    protected function config(): array
    {
        return [
            'is_enabled' => Setting::get('advert.enabled', '1') === '1',
            'mode' => Setting::get('advert.mode', 'cycle'),
            'duration_secs' => (int) Setting::get('advert.duration_secs', '15'),
            'position' => Setting::get('advert.position', 'right'),
        ];
    }
}

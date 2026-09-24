<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvertisementCrudTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $email): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::where('email', $email)->firstOrFail();
    }

    public function test_store_text_ad_persists_onscreen_copy(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'Welcome notice',
            'media_type' => Advertisement::TYPE_TEXT,
            'text_content' => 'Please keep your token with you',
            'duration_secs' => 10,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::where('title', 'Welcome notice')->firstOrFail();

        $this->assertSame('Please keep your token with you', $ad->description);
        $this->assertTrue($ad->is_active);
    }

    public function test_store_text_ad_requires_copy(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->from(route('admin.advertisements.create'))
            ->post(route('admin.advertisements.store'), [
                'title' => 'Empty text ad',
                'media_type' => Advertisement::TYPE_TEXT,
            ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('description');

        $this->assertDatabaseMissing('advertisements', ['title' => 'Empty text ad']);
    }

    public function test_update_text_ad_round_trips_onscreen_copy(): void
    {
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Old text',
            'description' => 'Old copy',
            'media_type' => Advertisement::TYPE_TEXT,
            'duration_secs' => 8,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.advertisements.update', $ad), [
            'title' => 'Old text',
            'media_type' => Advertisement::TYPE_TEXT,
            'text_content' => 'New copy',
            'duration_secs' => 12,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $this->assertSame('New copy', $ad->fresh()->description);
        $this->assertSame(12, $ad->fresh()->duration_secs);
    }

    public function test_index_shows_quick_add_form_for_admins(): void
    {
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::where('title', 'Welcome to Queue-Pro')->firstOrFail();

        $this->actingAs($admin)->get(route('admin.advertisements.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.advertisements.edit', $ad))->assertOk();
    }

    public function test_rejects_video_with_non_browser_extension(): void
    {
        $admin = $this->login('admin@queuecare.local');

        // Browsers cannot reliably play MKV/AVI/... containers, so only MP4 and
        // WebM are accepted for uploaded video ads now.
        $this->actingAs($admin)->from(route('admin.advertisements.create'))
            ->post(route('admin.advertisements.store'), [
                'title' => 'MKV film',
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_file' => UploadedFile::fake()->create('clip.mkv', 600, 'video/x-matroska'),
                'duration_secs' => 15,
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('media_file');

        $this->assertDatabaseMissing('advertisements', ['title' => 'MKV film']);
    }

    public function test_store_video_with_mp4_passes(): void
    {
        Storage::fake('public');
        $admin = $this->login('admin@queuecare.local');

        $file = UploadedFile::fake()->create('clip.mp4', 600, 'video/mp4');

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'MP4 probe',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_file' => $file,
            'duration_secs' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::where('title', 'MP4 probe')->firstOrFail();

        $this->assertNotNull($ad->media_path);
        $this->assertStringEndsWith('.mp4', $ad->media_path);
        $this->assertTrue(Storage::disk('public')->exists($ad->media_path));
    }

    public function test_store_video_with_webm_keeps_safe_extension(): void
    {
        Storage::fake('public');
        $admin = $this->login('admin@queuecare.local');

        $file = UploadedFile::fake()->create('clip.webm', 600, 'video/webm');

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'WebM probe',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_file' => $file,
            'duration_secs' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::where('title', 'WebM probe')->firstOrFail();

        $this->assertStringEndsWith('.webm', $ad->media_path);
    }

    public function test_rejects_video_with_disallowed_extension(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->from(route('admin.advertisements.create'))
            ->post(route('admin.advertisements.store'), [
                'title' => 'Bad file',
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_file' => UploadedFile::fake()->create('notes.txt', 10),
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('media_file');

        $this->assertDatabaseMissing('advertisements', ['title' => 'Bad file']);
    }

    public function test_rejects_non_video_content_disguised_as_mp4(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->from(route('admin.advertisements.create'))
            ->post(route('admin.advertisements.store'), [
                'title' => 'Fake video',
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_file' => UploadedFile::fake()->create('fake.mp4', 10, 'image/gif'),
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('media_file');

        $this->assertDatabaseMissing('advertisements', ['title' => 'Fake video']);
    }

    public function test_update_video_replaces_media_file(): void
    {
        Storage::fake('public');
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Video ad', 'media_type' => Advertisement::TYPE_VIDEO,
            'media_path' => 'ads/old.mp4', 'duration_secs' => 10, 'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.advertisements.update', $ad), [
            'title' => 'Video ad',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_file' => UploadedFile::fake()->create('new.webm', 600, 'video/webm'),
            'duration_secs' => 10,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $this->assertNotNull($ad->fresh()->media_path);
        $this->assertStringNotContainsString('old.mp4', $ad->fresh()->media_path);
        $this->assertStringEndsWith('.webm', $ad->fresh()->media_path);
    }

    public function test_update_youtube_ad_with_iframe_embed_passes(): void
    {
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Spot',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def',
            'youtube_embed' => '<iframe src="https://www.youtube.com/embed/abc123def"></iframe>',
            'duration_secs' => 15,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.advertisements.update', $ad), [
            'title' => 'Spot',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_embed' => '<iframe src="https://www.youtube-nocookie.com/embed/syFZfO_wfMQ?rel=0&amp;autoplay=1&amp;mute=1&amp;playsinline=1" style="border:0;width:100%;height:100%;aspect-ratio:16/9" class="h-full w-full" title="YouTube video" allow="autoplay; fullscreen; picture-in-picture; encrypted-media; accelerometer; gyroscope" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
            'duration_secs' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $fresh = $ad->fresh();
        $this->assertSame('https://www.youtube.com/watch?v=syFZfO_wfMQ', $fresh->youtube_url);
        $this->assertStringContainsString('syFZfO_wfMQ', $fresh->youtube_embed);
    }

    public function test_update_youtube_ad_accepts_entity_escaped_embed(): void
    {
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Spot',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_url' => 'https://www.youtube.com/watch?v=abc123def',
            'youtube_embed' => '<iframe src="https://www.youtube.com/embed/abc123def"></iframe>',
            'duration_secs' => 15,
            'is_active' => true,
        ]);

        // Re-submitting the textarea content back to the server with HTML
        // entities still intact must not break the update — the sanitizer
        // decodes them before extracting the video id.
        $escaped = '&lt;iframe src=&quot;https://www.youtube-nocookie.com/embed/syFZfO_wfMQ?rel=0&amp;autoplay=1&amp;mute=1&amp;playsinline=1&quot; style=&quot;border:0;width:100%;height:100%;aspect-ratio:16/9&quot;&gt;&lt;/iframe&gt;';

        $this->actingAs($admin)->put(route('admin.advertisements.update', $ad), [
            'title' => 'Spot edited',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_embed' => $escaped,
            'duration_secs' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $fresh = $ad->fresh();
        $this->assertSame('Spot edited', $fresh->title);
        $this->assertSame('https://www.youtube.com/watch?v=syFZfO_wfMQ', $fresh->youtube_url);
        $this->assertStringContainsString('syFZfO_wfMQ', $fresh->youtube_embed);
    }

    public function test_sanitize_embed_decodes_entity_escaped_iframe(): void
    {
        $escaped = '&lt;iframe src=&quot;https://www.youtube-nocookie.com/embed/jNQXAC9IVRw?rel=0&amp;autoplay=1&quot; allowfullscreen&gt;&lt;/iframe&gt;';

        $result = Advertisement::sanitizeEmbedFromInput($escaped);

        $this->assertNotNull($result);
        $this->assertSame('https://www.youtube.com/watch?v=jNQXAC9IVRw', $result['url']);
        $this->assertStringContainsString('jNQXAC9IVRw', $result['embed']);
    }

    public function test_store_rejects_oversized_video_with_validation_error(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->from(route('admin.advertisements.create'))
            ->post(route('admin.advertisements.store'), [
                'title' => 'Too big',
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_file' => UploadedFile::fake()->create('big.mp4', 70000, 'video/mp4'),
                'duration_secs' => 15,
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('media_file');

        $this->assertDatabaseMissing('advertisements', ['title' => 'Too big']);
    }

    public function test_update_rejects_oversized_video_with_validation_error(): void
    {
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Video ad',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_path' => 'ads/old.mp4',
            'duration_secs' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->from(route('admin.advertisements.edit', $ad))
            ->put(route('admin.advertisements.update', $ad), [
                'title' => 'Video ad',
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_file' => UploadedFile::fake()->create('big.webm', 70000, 'video/webm'),
                'duration_secs' => 10,
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.edit', $ad))
            ->assertSessionHasErrors('media_file');

        $this->assertSame('ads/old.mp4', $ad->fresh()->media_path);
    }

    public function test_switching_image_to_video_without_file_is_rejected(): void
    {
        Storage::fake('public');
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Logo',
            'media_type' => Advertisement::TYPE_IMAGE,
            'media_path' => 'ads/logo.jpg',
            'duration_secs' => 10,
            'is_active' => true,
        ]);

        // Switching a file-based ad into another file-based type must demand a
        // matching upload, otherwise the display would render an image as video.
        $this->actingAs($admin)->from(route('admin.advertisements.edit', $ad))
            ->put(route('admin.advertisements.update', $ad), [
                'title' => 'Logo',
                'media_type' => Advertisement::TYPE_VIDEO,
                'duration_secs' => 10,
                'is_active' => 1,
            ])->assertRedirect(route('admin.advertisements.edit', $ad))
            ->assertSessionHasErrors('media_file');

        $this->assertSame(Advertisement::TYPE_IMAGE, $ad->fresh()->media_type);
    }

    public function test_switching_type_with_matching_upload_passes(): void
    {
        Storage::fake('public');
        $admin = $this->login('admin@queuecare.local');
        $ad = Advertisement::create([
            'title' => 'Logo',
            'media_type' => Advertisement::TYPE_IMAGE,
            'media_path' => 'ads/logo.jpg',
            'duration_secs' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->put(route('admin.advertisements.update', $ad), [
            'title' => 'Logo',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_file' => UploadedFile::fake()->create('promo.webm', 600, 'video/webm'),
            'duration_secs' => 10,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $fresh = $ad->fresh();

        $this->assertSame(Advertisement::TYPE_VIDEO, $fresh->media_type);
        $this->assertStringEndsWith('.webm', $fresh->media_path);
        $this->assertStringNotContainsString('logo.jpg', $fresh->media_path);
    }

    public function test_blank_duration_falls_back_to_global_setting(): void
    {
        $admin = $this->login('admin@queuecare.local');
        Setting::set('advert.duration_secs', '20');

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'No duration',
            'media_type' => Advertisement::TYPE_TEXT,
            'text_content' => 'Copy',
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $this->assertSame(20, Advertisement::where('title', 'No duration')->firstOrFail()->duration_secs);
    }

    public function test_display_api_exposes_media_mime_for_videos(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);
        Advertisement::create([
            'title' => 'Promo',
            'media_type' => Advertisement::TYPE_VIDEO,
            'media_path' => 'ads/promo.mp4',
            'duration_secs' => 15,
            'is_active' => true,
        ]);

        $this->get(route('display.api'))
            ->assertOk()
            ->assertJsonFragment([
                'media_type' => Advertisement::TYPE_VIDEO,
                'media_mime' => 'video/mp4',
                'video' => asset('storage/ads/promo.mp4'),
            ]);
    }

    public function test_media_type_label_returns_human_label(): void
    {
        foreach ([
            Advertisement::TYPE_VIDEO => 'Video',
            Advertisement::TYPE_IMAGE => 'Image',
            Advertisement::TYPE_YOUTUBE => 'YouTube',
            Advertisement::TYPE_TEXT => 'Text',
        ] as $type => $label) {
            $ad = new Advertisement(['media_type' => $type]);
            $this->assertSame($label, $ad->media_type_label);
        }
    }

    public function test_inactive_first_ad_is_not_promoted_live(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'Paused first ad',
            'media_type' => Advertisement::TYPE_TEXT,
            'text_content' => 'Copy',
            'duration_secs' => 10,
            'is_active' => 0,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::where('title', 'Paused first ad')->firstOrFail();

        $this->assertFalse($ad->is_active);
        $this->assertFalse($ad->is_live);
    }

    public function test_active_first_ad_is_promoted_live(): void
    {
        $admin = $this->login('admin@queuecare.local');
        Advertisement::query()->delete();

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'First active ad',
            'media_type' => Advertisement::TYPE_TEXT,
            'text_content' => 'Copy',
            'duration_secs' => 10,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $this->assertTrue(Advertisement::where('title', 'First active ad')->first()->is_live);
    }

    public function test_pausing_live_ad_promotes_next_active(): void
    {
        $admin = $this->login('admin@queuecare.local');
        Advertisement::query()->delete();

        $live = Advertisement::create([
            'title' => 'Live ad', 'media_type' => Advertisement::TYPE_TEXT,
            'description' => 'a', 'is_active' => true, 'is_live' => true, 'sort_order' => 1,
        ]);
        $other = Advertisement::create([
            'title' => 'Backup ad', 'media_type' => Advertisement::TYPE_TEXT,
            'description' => 'b', 'is_active' => true, 'is_live' => false, 'sort_order' => 2,
        ]);

        $this->actingAs($admin)->post(route('admin.advertisements.toggle', $live))
            ->assertRedirect();

        $this->assertFalse($live->fresh()->is_active);
        $this->assertFalse($live->fresh()->is_live);
        $this->assertTrue($other->fresh()->is_live);
    }

    public function test_display_operator_cannot_change_advert_settings(): void
    {
        $display = $this->login('display@queuecare.local');

        $this->actingAs($display)->get(route('admin.advertisements.settings'))->assertForbidden();

        $this->actingAs($display)->patch(route('admin.advertisements.settings.update'), [
            'mode' => 'single',
            'duration_secs' => 30,
            'position' => 'bottom',
        ])->assertForbidden();

        $this->assertSame('cycle', Setting::get('advert.mode', 'cycle'));
    }

    public function test_admin_can_change_advert_settings(): void
    {
        $admin = $this->login('admin@queuecare.local');

        $this->actingAs($admin)->get(route('admin.advertisements.settings'))->assertOk();

        $this->actingAs($admin)->patch(route('admin.advertisements.settings.update'), [
            'mode' => 'single',
            'duration_secs' => 30,
            'position' => 'bottom',
        ])->assertRedirect(route('admin.advertisements.index'));

        $this->assertSame('single', Setting::get('advert.mode'));
        $this->assertSame('bottom', Setting::get('advert.position'));
        $this->assertSame('30', Setting::get('advert.duration_secs'));
    }

    public function test_display_layout_respects_position_setting(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Default (right): 4 + 8 split with advert on the right.
        $html = $this->get(route('display'))->assertOk()->getContent();
        $this->assertStringContainsString('data-advert-position="right"', $html);
        $this->assertStringContainsString('lg:col-span-4', $html);
        $this->assertStringContainsString('lg:col-span-8', $html);

        // Bottom: full-width stacked counter and advert.
        Setting::set('advert.position', 'bottom');
        $html = $this->get(route('display'))->assertOk()->getContent();
        $this->assertStringContainsString('data-advert-position="bottom"', $html);
        $this->assertStringNotContainsString('lg:col-span-4', $html);
        $this->assertStringNotContainsString('lg:col-span-8', $html);

        // Left: advert keeps the 8-wide column but reorders before the counter.
        Setting::set('advert.position', 'left');
        $html = $this->get(route('display'))->assertOk()->getContent();
        $this->assertStringContainsString('data-advert-position="left"', $html);
        $this->assertStringContainsString('lg:col-span-8', $html);
        $this->assertStringContainsString('lg:order-1', $html);
        $this->assertStringContainsString('lg:order-2', $html);
    }
}
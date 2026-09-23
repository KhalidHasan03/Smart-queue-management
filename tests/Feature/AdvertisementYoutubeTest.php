<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementYoutubeTest extends TestCase
{
    use RefreshDatabase;

    const VIDEO_ID = 'dQw4w9WgXcQ';

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::where('email', 'admin@queuecare.local')->firstOrFail();
    }

    public function test_sanitizer_accepts_watch_url(): void
    {
        $result = Advertisement::sanitizeEmbedFromInput('https://www.youtube.com/watch?v='.self::VIDEO_ID);

        $this->assertNotNull($result);
        $videoId = Advertisement::youtubeIdFromUrl($result['url']);
        $this->assertSame(self::VIDEO_ID, $videoId);
        $this->assertStringContainsString('youtube-nocookie.com/embed/'.self::VIDEO_ID, $result['embed']);
        $this->assertStringContainsString('allowfullscreen', $result['embed']);
        $this->assertStringContainsString('autoplay=1', $result['embed']);
    }

    public function test_sanitizer_accepts_iframe_embed_and_strips_pasted_attributes(): void
    {
        $snippet = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.self::VIDEO_ID.'?si=xyz" '
            .'title="YouTube video player" frameborder="0" allow="accelerometer; autoplay" '
            .'onload="alert(1)" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>';

        $result = Advertisement::sanitizeEmbedFromInput($snippet);

        $this->assertNotNull($result);
        $this->assertStringContainsString('youtube-nocookie.com/embed/'.self::VIDEO_ID, $result['embed']);
        $this->assertStringNotContainsString('onload=', $result['embed']);
        $this->assertStringNotContainsString('si=xyz', $result['embed']);
        $this->assertStringNotContainsString('width="560"', $result['embed']);
    }

    public function test_sanitizer_rejects_non_youtube_sources(): void
    {
        foreach ([
            'https://evil.example.com/x.js',
            'javascript:alert(1)',
            'https://player.vimeo.com/video/123',
            '<iframe src="https://evil.example.com/x" onload="alert(1)"></iframe>',
        ] as $input) {
            $this->assertNull(Advertisement::sanitizeEmbedFromInput($input), $input);
        }
    }

    public function test_sanitizer_accepts_nocookie_embed_url(): void
    {
        $result = Advertisement::sanitizeEmbedFromInput('https://www.youtube-nocookie.com/embed/'.self::VIDEO_ID);

        $this->assertNotNull($result);
        $this->assertSame(self::VIDEO_ID, Advertisement::youtubeIdFromUrl($result['url']));
    }

    public function test_store_youtube_ad_persists_sanitized_embed(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'title' => 'Clinic promo',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_embed' => 'https://youtu.be/'.self::VIDEO_ID,
            'duration_secs' => 15,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::where('title', 'Clinic promo')->firstOrFail();

        $this->assertTrue($ad->has_embed);
        $this->assertStringContainsString('youtube-nocookie.com/embed/'.self::VIDEO_ID, $ad->youtube_embed);
        $this->assertSame(self::VIDEO_ID, Advertisement::youtubeIdFromUrl($ad->youtube_url));
    }

    public function test_display_api_exposes_sanitized_embed(): void
    {
        $admin = $this->admin();

        Advertisement::create([
            'title' => 'YouTube ad',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_url' => 'https://www.youtube.com/watch?v='.self::VIDEO_ID,
            'youtube_embed' => Advertisement::sanitizeEmbedFromInput('https://youtu.be/'.self::VIDEO_ID)['embed'],
            'duration_secs' => 15,
            'is_active' => true,
            'sort_order' => 99,
        ]);

        $payload = $this->getJson(route('display.api'))->assertOk()->json();
        $item = collect($payload['advert']['items'])->first(fn ($item) => $item['youtube'] !== null);

        $this->assertNotNull($item);
        $this->assertTrue($item['has_embed']);
        $this->assertStringContainsString('youtube-nocookie.com/embed/'.self::VIDEO_ID, $item['youtube_embed']);
        $this->assertStringContainsString('allowfullscreen', $item['youtube_embed']);
    }

    public function test_quick_add_without_title_derives_title_and_reaches_display(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.advertisements.store'), [
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_embed' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.self::VIDEO_ID.'?si=x" allowfullscreen></iframe>',
            'duration_secs' => 20,
            'is_active' => 1,
        ])->assertRedirect(route('admin.advertisements.index'));

        $ad = Advertisement::orderByDesc('id')->firstOrFail();

        $this->assertSame('YouTube ad '.self::VIDEO_ID, $ad->title);
        $this->assertTrue($ad->has_embed);
        $this->assertSame(Advertisement::TYPE_YOUTUBE, $ad->media_type);

        $payload = $this->getJson(route('display.api'))->assertOk()->json();
        $item = collect($payload['advert']['items'])->first(fn ($item) => $item['youtube'] !== null);

        $this->assertNotNull($item);
        $this->assertSame($ad->id, $item['id']);
        $this->assertTrue($item['has_embed']);
        $this->assertStringContainsString('youtube-nocookie.com/embed/'.self::VIDEO_ID, $item['youtube_embed']);
    }

    public function test_index_shows_quick_add_form_for_admins(): void
    {
        $admin = $this->admin();

        $html = $this->actingAs($admin)->get(route('admin.advertisements.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Add YouTube advertisement', $html);
        $this->assertStringContainsString('qc-quick-yt', $html);
        $this->assertStringContainsString('name="youtube_embed"', $html);
        $this->assertStringContainsString('ad-sortable', $html);
    }

    public function test_index_hides_add_form_for_view_only_users(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'display@queuecare.local')->firstOrFail();

        $html = $this->actingAs($user)->get(route('admin.advertisements.index'))->assertOk()->getContent();

        $this->assertStringNotContainsString('Add YouTube advertisement', $html);
        $this->assertStringNotContainsString('name="youtube_embed"', $html);
    }

    public function test_rejects_non_youtube_url_on_store(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->from(route('admin.advertisements.create'))->post(route('admin.advertisements.store'), [
            'title' => 'Bad embed',
            'media_type' => Advertisement::TYPE_YOUTUBE,
            'youtube_embed' => 'https://vimeo.com/12345',
            'duration_secs' => 15,
        ])->assertRedirect(route('admin.advertisements.create'))
            ->assertSessionHasErrors('youtube_embed');

        $this->assertDatabaseMissing('advertisements', ['title' => 'Bad embed']);
    }
}

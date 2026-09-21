<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_patient_display_screen_renders_queue_and_advert_panes(): void
    {
        $this->get('/display')
            ->assertOk()
            ->assertSee('Queue-Pro', false)
            ->assertSee('advert', false)
            ->assertSee('upcoming', false)
            ->assertSee('serial', false);
    }

    public function test_display_api_returns_advert_payload(): void
    {
        $r = $this->getJson('/api/display')->assertOk()->json();

        $this->assertArrayHasKey('advert', $r);
        $this->assertArrayHasKey('items', $r['advert']);
        $this->assertArrayHasKey('enabled', $r['advert']);
        $this->assertArrayHasKey('mode', $r['advert']);
    }
}

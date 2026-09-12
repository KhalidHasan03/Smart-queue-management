<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function seedMasters(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_receptionist_can_render_token_create(): void
    {
        $this->seedMasters();
        $user = User::where('email', 'reception@queuecare.local')->first();
        $this->actingAs($user)->get(route('tokens.create'))->assertOk();
    }

    public function test_operator_cannot_open_token_create_but_can_queue(): void
    {
        $this->seedMasters();
        $op = User::where('email', 'operator@queuecare.local')->first();
        $this->actingAs($op)->get(route('tokens.create'))->assertForbidden();
        $this->actingAs($op)->get(route('queue.index'))->assertOk();
    }

    public function test_receptionist_can_view_queue_but_cannot_call_next(): void
    {
        $this->seedMasters();
        $rec = User::where('email', 'reception@queuecare.local')->first();
        $this->actingAs($rec)->get(route('queue.index'))->assertOk();
        $this->actingAs($rec)->post(route('queue.next'))->assertForbidden();
    }

    public function test_guest_display_is_public(): void
    {
        $this->seedMasters();
        $this->get(route('display'))->assertOk();
        $this->get(route('display.api'))->assertOk()->assertJsonStructure(['now', 'upcoming']);
    }

    public function test_token_create_route_not_shadowed_by_show(): void
    {
        $this->seedMasters();
        $admin = User::where('email', 'admin@queuecare.local')->first();
        $resp = $this->actingAs($admin)->get('/tokens/create');
        $resp->assertOk();
        $this->assertStringNotContainsString('No query results for model', $resp->getContent());
    }
}

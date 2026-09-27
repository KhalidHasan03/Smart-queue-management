<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_login_screen_offers_a_tap_to_fill_button_for_every_seeded_role(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get(route('login'));

        $response->assertOk();

        foreach ([
            'superadmin@queuecare.local',
            'admin@queuecare.local',
            'reception@queuecare.local',
            'operator@queuecare.local',
            'staff@queuecare.local',
            'display@queuecare.local',
        ] as $email) {
            $response->assertSee($email);
            // Js::from() renders the address as a single-quoted JS string.
            $response->assertSee("fillLogin('{$email}')", escape: false);
        }

        // Every autofill button must submit the shared demo password.
        $response->assertSee("pwdInput.value = 'password123'", escape: false);
    }

    public function test_each_seeded_role_can_sign_in_with_the_demo_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $emails = User::pluck('email');

        $this->assertCount(6, $emails, 'Expected all six demo roles to be seeded.');

        foreach ($emails as $email) {
            $response = $this->post('/login', ['email' => $email, 'password' => 'password123']);

            $response->assertRedirect(route('dashboard', absolute: false), "Seeded role {$email} could not sign in.");
            $this->assertAuthenticatedAs(User::where('email', $email)->firstOrFail());

            $this->post('/logout');
            $this->assertGuest();
        }
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}

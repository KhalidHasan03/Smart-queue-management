<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\Counter;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Review;
use App\Models\Service;
use App\Models\Token;
use App\Models\User;
use App\Support\ReviewCode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Renders every GET page an admin can reach, against a seeded database.
 *
 * This exists because a schema/code mismatch only shows up when the page is
 * actually built: a new column referenced by a scope fails with
 * "Unknown column ... in 'where clause'" at query time, long after the test
 * suite wrote the migration, and the test suite uses its own database — so a
 * developer who forgets `php artisan migrate` sees a 500 that nothing catches.
 *
 * A Blade error (unknown view, bad component, undefined variable) surfaces here
 * too, because the whole page is compiled and rendered.
 */
class RouteSmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private function bindModels(): array
    {
        $service = Service::where('prefix', 'G')->firstOrFail();
        $doctor = Doctor::where('service_id', $service->id)->firstOrFail();
        $counter = Counter::where('name', 'Counter-1')->firstOrFail();

        $token = Token::create([
            'token_date' => now()->toDateString(),
            'seq' => 1,
            'token_no' => $service->prefix.'-001',
            'review_code' => ReviewCode::generate(),
            'service_id' => $service->id,
            'doctor_id' => $doctor->id,
            'counter_id' => $counter->id,
            'patient_id' => Patient::create([
                'name' => 'Smoke Test',
                'phone' => '01700000001',
            ])->id,
            'status' => Token::COMPLETED,
            'finished_at' => now(),
            'review_status' => 'pending',
        ]);

        $review = Review::create([
            'token_id' => $token->id,
            'patient_id' => $token->patient_id,
            'rating' => Review::GOOD,
            'comment' => 'Smoke test comment',
            'status' => Review::STATUS_PENDING,
        ]);

        $advertisement = Advertisement::first() ?? Advertisement::create([
            'title' => 'Smoke Advert',
            'body' => 'Smoke advert body',
            'is_active' => true,
        ]);

        return [
            'service' => $service,
            'doctor' => $doctor,
            'counter' => $counter,
            'token' => $token,
            'review' => $review,
            'advertisement' => $advertisement,
            'admin' => $this->admin,
        ];
    }

    public function test_every_admin_page_renders(): void
    {
        $this->seed(DatabaseSeeder::class);

        // super_admin, not admin: config/rbac.php gives the admin role a
        // deliberate subset (no roles.manage, no display.manage), so a plain
        // admin 403s on three pages by design and would mask real breakage.
        $this->admin = User::where('email', 'superadmin@queuecare.local')->firstOrFail();
        $m = $this->bindModels();

        $pages = [
            'dashboard' => [],
            'queue.index' => [],
            'queue.reviews' => [],
            'tokens.index' => [],
            'tokens.create' => [],
            'tokens.issued' => [],
            'tokens.show' => [$m['token']],
            'tokens.print' => [$m['token']],
            'staff.index' => [],
            'reports.index' => [],
            'display.manage' => [],
            'reviews.setup' => [],
            'admin.counters.index' => [],
            'admin.counters.create' => [],
            'admin.counters.edit' => [$m['counter']],
            'admin.doctors.index' => [],
            'admin.doctors.create' => [],
            'admin.doctors.edit' => [$m['doctor']],
            'admin.services.index' => [],
            'admin.services.create' => [],
            'admin.services.edit' => [$m['service']],
            'admin.reviews.index' => [],
            'admin.reviews.show' => [$m['review']],
            'admin.advertisements.index' => [],
            'admin.advertisements.create' => [],
            'admin.advertisements.settings' => [],
            'admin.advertisements.edit' => [$m['advertisement']],
            'admin.users.index' => [],
            'admin.users.create' => [],
            'admin.users.edit' => [$m['admin']],
            'admin.roles.index' => [],
            'admin.settings.edit' => [],
            'profile.edit' => [],
        ];

        $failures = [];

        foreach ($pages as $name => $params) {
            try {
                $this->actingAs($this->admin)
                    ->get(route($name, $params))
                    ->assertOk();
            } catch (\Throwable $e) {
                $failures[] = $name.': '.$e->getMessage();
            }
        }

        $this->assertSame([], $failures, "Pages failed to render:\n".implode("\n", $failures));
    }

    public function test_every_public_page_renders(): void
    {
        $this->seed(DatabaseSeeder::class);

        $pages = [
            'landing' => '/',
            'landing.about' => [],
            'landing.contact' => [],
            'landing.features' => [],
            'landing.industry' => [],
            'landing.pricing' => [],
            'landing.services' => [],
            'display' => [],
            'review.kiosk' => [],
            'login' => [],
        ];

        $failures = [];

        foreach ($pages as $name => $params) {
            try {
                $this->get(route($name, $params))->assertOk();
            } catch (\Throwable $e) {
                $failures[] = $name.': '.$e->getMessage();
            }
        }

        $this->assertSame([], $failures, "Pages failed to render:\n".implode("\n", $failures));
    }

    public function test_the_display_api_still_answers_with_the_open_counter_schema(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->getJson(route('display.api'))
            ->assertOk()
            ->assertJsonStructure(['now', 'upcoming']);
    }

    public function test_the_review_list_json_poll_endpoint_answers(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->actingAs(User::where('email', 'admin@queuecare.local')->firstOrFail())
            ->getJson(route('admin.reviews.index', ['_pending_only' => 1]))
            ->assertOk()
            ->assertJsonStructure(['pending', 'total']);
    }
}

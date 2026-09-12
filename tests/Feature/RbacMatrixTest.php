<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $email)
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);

        return User::where('email', $email)->firstOrFail();
    }

    public function test_super_admin_has_full_access(): void
    {
        $u = $this->login('superadmin@queuecare.local');
        $this->assertTrue($u->hasPermission('roles.manage'));
        $this->actingAs($u)->get(route('admin.roles.index'))->assertOk();
        $this->actingAs($u)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($u)->get(route('admin.settings.edit'))->assertOk();
        $this->actingAs($u)->get(route('display.manage'))->assertOk();
        $this->actingAs($u)->get(route('staff.index'))->assertOk();
    }

    public function test_admin_cannot_manage_roles_or_super_admin(): void
    {
        $u = $this->login('admin@queuecare.local');
        $this->assertFalse($u->hasPermission('roles.manage'));
        $this->actingAs($u)->get(route('admin.roles.index'))->assertForbidden();
        $super = User::where('email', 'superadmin@queuecare.local')->first();
        $this->actingAs($u)->get(route('admin.users.edit', $super))->assertForbidden();
        $this->actingAs($u)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($u)->get(route('display.manage'))->assertForbidden();
    }

    public function test_admin_cannot_assign_super_admin_role(): void
    {
        $u = $this->login('admin@queuecare.local');
        $target = User::where('email', 'reception@queuecare.local')->first();
        $this->actingAs($u)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role' => 'super_admin',
        ])->assertSessionHasErrors('role');
    }

    public function test_receptionist_boundaries(): void
    {
        $u = $this->login('reception@queuecare.local');
        $this->actingAs($u)->get(route('tokens.create'))->assertOk();
        $this->actingAs($u)->get(route('queue.index'))->assertOk();
        $this->actingAs($u)->post(route('queue.next'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.settings.edit'))->assertForbidden();
        $this->actingAs($u)->get(route('display.manage'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.roles.index'))->assertForbidden();
    }

    public function test_operator_reaches_only_own_counter_and_actions(): void
    {
        $u = $this->login('operator@queuecare.local');
        $this->actingAs($u)->get(route('queue.index'))->assertOk();
        $this->actingAs($u)->get(route('tokens.create'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($u)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($u)->get(route('display.manage'))->assertForbidden();
    }

    public function test_staff_is_scoped_and_cannot_administer(): void
    {
        $u = $this->login('staff@queuecare.local');
        $this->actingAs($u)->get(route('staff.index'))->assertOk();
        $this->actingAs($u)->get(route('tokens.create'))->assertForbidden();
        $this->actingAs($u)->post(route('queue.next'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($u)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($u)->get(route('queue.index'))->assertOk();
    }

    public function test_display_operator_only_manages_display(): void
    {
        $u = $this->login('display@queuecare.local');
        $this->actingAs($u)->get(route('display.manage'))->assertOk();
        $this->actingAs($u)->get(route('tokens.index'))->assertForbidden();
        $this->actingAs($u)->get(route('queue.index'))->assertForbidden();
        $this->actingAs($u)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($u)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_super_admin_can_manage_roles_matrix_and_it_takes_effect(): void
    {
        $su = $this->login('superadmin@queuecare.local');
        $this->actingAs($su)->get(route('admin.roles.index'))->assertOk();

        $recPerms = \App\Support\Rbac::rolePermissions('receptionist');
        $this->assertContains('reports.view', $recPerms);

        $stripped = array_values(array_diff($recPerms, ['reports.view']));
        $this->actingAs($su)->put(route('admin.roles.update'), [
            'perms' => ['receptionist' => $stripped],
        ])->assertRedirect();

        $rec = User::where('email', 'reception@queuecare.local')->first();
        $this->assertFalse($rec->fresh()->hasPermission('reports.view'));
        $this->actingAs($rec)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($rec)->get(route('tokens.create'))->assertOk();

        $this->assertTrue($su->fresh()->hasPermission('roles.manage'));
        $this->actingAs($su)->get(route('admin.roles.index'))->assertOk();
    }

    public function test_admin_cannot_update_roles_matrix(): void
    {
        $u = $this->login('admin@queuecare.local');
        $this->actingAs($u)->put(route('admin.roles.update'), [
            'perms' => ['receptionist' => []],
        ])->assertForbidden();
    }

    public function test_existing_users_migrated_without_data_loss(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        foreach (['admin@queuecare.local', 'reception@queuecare.local', 'operator@queuecare.local'] as $email) {
            $this->assertNotNull(User::where('email', $email)->first(), $email);
        }
        $this->assertSame('admin', User::where('email', 'admin@queuecare.local')->first()->role);
    }
}

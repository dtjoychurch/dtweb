<?php

namespace Tests\Feature\Admin;

use App\Models\DiscipleshipRelationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_user_management(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_admin_can_create_and_update_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => '新使用者',
            'email' => 'new-user@example.com',
            'password' => 'password123',
            'role' => 'member',
            'status' => 'active',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'new-user@example.com']);

        $user = User::where('email', 'new-user@example.com')->first();

        $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => '新使用者',
            'email' => 'new-user@example.com',
            'password' => '',
            'role' => 'member',
            'status' => 'suspended',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'new-user@example.com', 'status' => 'suspended']);
    }

    public function test_admin_cannot_delete_user_who_is_part_of_a_relationship(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $mentor = User::factory()->create();
        DiscipleshipRelationship::factory()->create(['mentor_id' => $mentor->id]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $mentor))
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $mentor->id]);
    }
}

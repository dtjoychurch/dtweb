<?php

namespace Tests\Feature\Admin;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRecordType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecordTypeTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_manage_record_types(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.record-types.index'))->assertForbidden();
    }

    public function test_admin_can_create_a_record_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.record-types.store'), [
            'name' => '突破',
            'slug' => 'breakthrough',
            'sort_order' => 9,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.record-types.index'));
        $this->assertDatabaseHas('discipleship_record_types', ['slug' => 'breakthrough', 'name' => '突破']);
    }

    public function test_admin_cannot_delete_a_type_that_is_in_use(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $type = DiscipleshipRecordType::factory()->create();
        DiscipleshipRecord::factory()->create(['type_id' => $type->id]);

        $response = $this->actingAs($admin)->delete(route('admin.record-types.destroy', $type));

        $response->assertSessionHasErrors('record_type');
        $this->assertDatabaseHas('discipleship_record_types', ['id' => $type->id]);
    }

    public function test_admin_can_delete_an_unused_type(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $type = DiscipleshipRecordType::factory()->create();

        $this->actingAs($admin)->delete(route('admin.record-types.destroy', $type))
            ->assertRedirect(route('admin.record-types.index'));

        $this->assertDatabaseMissing('discipleship_record_types', ['id' => $type->id]);
    }

    public function test_inactive_types_do_not_appear_in_the_new_record_form_but_active_ones_do(): void
    {
        $user = User::factory()->create();
        $relationship = \App\Models\DiscipleshipRelationship::factory()->create(['mentor_id' => $user->id]);
        $active = DiscipleshipRecordType::factory()->create(['name' => '啟用類型', 'is_active' => true]);
        $inactive = DiscipleshipRecordType::factory()->create(['name' => '停用類型', 'is_active' => false]);

        $response = $this->actingAs($user)->get(route('discipleship.show', $relationship));

        $response->assertOk();
        $response->assertSee('啟用類型');
        $response->assertDontSee('停用類型');
    }

    public function test_creating_a_record_with_an_inactive_type_is_rejected(): void
    {
        $mentor = User::factory()->create();
        $relationship = \App\Models\DiscipleshipRelationship::factory()->create(['mentor_id' => $mentor->id]);
        $inactive = DiscipleshipRecordType::factory()->create(['is_active' => false]);

        $response = $this->actingAs($mentor)->post(route('discipleship.records.store', $relationship), [
            'type_id' => $inactive->id,
            'title' => '不應該成功',
            'content' => '內容',
            'visibility' => 'shared',
            'occurred_at' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('type_id');
        $this->assertDatabaseMissing('discipleship_records', ['title' => '不應該成功']);
    }
}

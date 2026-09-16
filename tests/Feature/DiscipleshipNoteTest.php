<?php

namespace Tests\Feature;

use App\Models\DiscipleshipNote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_own_note(): void
    {
        $user = User::factory()->create();
        $note = DiscipleshipNote::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('notes.edit', $note))->assertOk();
    }

    public function test_other_user_cannot_view_or_edit_note(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $note = DiscipleshipNote::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)->get(route('notes.edit', $note))->assertForbidden();
        $this->actingAs($other)->put(route('notes.update', $note), ['content' => 'x'])->assertForbidden();
        $this->actingAs($other)->delete(route('notes.destroy', $note))->assertForbidden();
    }

    public function test_admin_cannot_view_note_content(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create();
        $note = DiscipleshipNote::factory()->create([
            'user_id' => $owner->id,
            'content' => '這是機密的私人筆記內容',
        ]);

        // No content-viewing route exists for admins at all.
        $this->actingAs($admin)->get(route('notes.edit', $note))->assertForbidden();

        $index = $this->actingAs($admin)->get(route('admin.notes.index'));
        $index->assertOk();
        $index->assertDontSee('這是機密的私人筆記內容');
    }

    public function test_admin_can_moderate_delete_a_note_without_viewing_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $note = DiscipleshipNote::factory()->create();

        $this->actingAs($admin)->delete(route('admin.notes.destroy', $note))->assertRedirect();

        $this->assertSoftDeleted('discipleship_notes', ['id' => $note->id]);
    }

    public function test_user_can_create_note(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('notes.store'), [
            'title' => '今天的想法',
            'content' => '這是內容',
        ]);

        $response->assertRedirect(route('notes.index'));
        $this->assertDatabaseHas('discipleship_notes', [
            'user_id' => $user->id,
            'title' => '今天的想法',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_relationship_member_can_view_session(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
        ]);

        $this->actingAs($disciple)
            ->get(route('discipleship.sessions.show', [$relationship, $session]))
            ->assertOk();
    }

    public function test_unrelated_user_cannot_view_session(): void
    {
        $relationship = DiscipleshipRelationship::factory()->create();
        $session = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('discipleship.sessions.show', [$relationship, $session]))
            ->assertForbidden();
    }

    public function test_relationship_member_can_create_session(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);

        $response = $this->actingAs($mentor)->post(route('discipleship.sessions.store', $relationship), [
            'session_date' => now()->toDateString(),
            'title' => '第一次門訓',
            'content' => '今天的門訓內容。',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('discipleship_sessions', [
            'relationship_id' => $relationship->id,
            'title' => '第一次門訓',
        ]);
    }

    public function test_unrelated_user_cannot_create_session(): void
    {
        $relationship = DiscipleshipRelationship::factory()->create();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)->post(route('discipleship.sessions.store', $relationship), [
            'session_date' => now()->toDateString(),
            'content' => '不應該成功。',
        ])->assertForbidden();
    }

    public function test_cannot_create_two_sessions_on_the_same_day_for_the_same_relationship(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'session_date' => '2026-05-01',
        ]);

        $response = $this->actingAs($mentor)->post(route('discipleship.sessions.store', $relationship), [
            'session_date' => '2026-05-01',
            'content' => '同一天不應該可以再新增一筆。',
        ]);

        $response->assertSessionHasErrors('session_date');
        $this->assertSame(1, $relationship->sessions()->whereDate('session_date', '2026-05-01')->count());
    }

    public function test_same_day_is_still_allowed_across_different_relationships(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        DiscipleshipSession::factory()->create(['session_date' => '2026-05-01']); // a different relationship

        $response = $this->actingAs($mentor)->post(route('discipleship.sessions.store', $relationship), [
            'session_date' => '2026-05-01',
            'content' => '不同關係，同一天應該可以。',
        ]);

        $response->assertRedirect();
        $this->assertSame(
            '2026-05-01',
            $relationship->sessions()->latest('id')->first()->session_date->toDateString()
        );
    }

    public function test_creator_can_delete_own_session(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
        ]);

        $this->actingAs($mentor)
            ->delete(route('discipleship.sessions.destroy', [$relationship, $session]))
            ->assertRedirect(route('discipleship.show', $relationship));

        $this->assertSoftDeleted('discipleship_sessions', ['id' => $session->id]);
    }

    public function test_other_relationship_member_cannot_delete_a_session_they_did_not_create(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
        ]);

        $this->actingAs($disciple)
            ->delete(route('discipleship.sessions.destroy', [$relationship, $session]))
            ->assertForbidden();

        $this->assertDatabaseHas('discipleship_sessions', ['id' => $session->id, 'deleted_at' => null]);
    }

    public function test_admin_can_update_a_session_without_tripping_the_duplicate_day_check_on_itself(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $relationship = DiscipleshipRelationship::factory()->create();
        $session = DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'session_date' => '2026-05-01',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.sessions.update', $session), [
            'session_date' => '2026-05-01',
            'title' => '更新標題',
            'content' => '更新內容',
        ]);

        $response->assertRedirect(route('admin.sessions.index'));
        $this->assertDatabaseHas('discipleship_sessions', ['id' => $session->id, 'title' => '更新標題']);
    }

    public function test_admin_cannot_update_a_session_onto_a_day_another_session_already_uses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $relationship = DiscipleshipRelationship::factory()->create();
        DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'session_date' => '2026-05-01',
        ]);
        $session = DiscipleshipSession::factory()->create([
            'relationship_id' => $relationship->id,
            'session_date' => '2026-05-08',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.sessions.update', $session), [
            'session_date' => '2026-05-01',
            'title' => '不應該成功',
            'content' => '內容',
        ]);

        $response->assertSessionHasErrors('session_date');
        $this->assertSame('2026-05-08', $session->fresh()->session_date->toDateString());
    }
}

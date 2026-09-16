<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRelationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_mentor_can_view_relationship(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);

        $this->actingAs($mentor)
            ->get(route('discipleship.show', $relationship))
            ->assertOk();
    }

    public function test_disciple_can_view_relationship(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);

        $this->actingAs($disciple)
            ->get(route('discipleship.show', $relationship))
            ->assertOk();
    }

    public function test_unrelated_user_cannot_view_relationship(): void
    {
        $relationship = DiscipleshipRelationship::factory()->create();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('discipleship.show', $relationship))
            ->assertForbidden();
    }

    public function test_index_only_lists_relationships_the_user_belongs_to(): void
    {
        $user = User::factory()->create();
        $mine = DiscipleshipRelationship::factory()->create(['mentor_id' => $user->id]);
        $notMine = DiscipleshipRelationship::factory()->create();

        $response = $this->actingAs($user)->get(route('discipleship.index'));

        $response->assertOk();
        $this->assertTrue($response->viewData('relationships')->pluck('id')->contains($mine->id));
        $this->assertFalse($response->viewData('relationships')->pluck('id')->contains($notMine->id));
    }

    public function test_only_admin_can_create_relationship(): void
    {
        $member = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        [$mentor, $disciple] = User::factory()->count(2)->create();

        $this->actingAs($member)->get(route('admin.relationships.create'))->assertForbidden();

        $this->actingAs($admin)->post(route('admin.relationships.store'), [
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
            'started_at' => now()->toDateString(),
            'status' => 'active',
        ])->assertRedirect(route('admin.relationships.index'));

        $this->assertDatabaseHas('discipleship_relationships', [
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
    }
}

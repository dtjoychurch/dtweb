<?php

namespace Tests\Feature;

use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipRelationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipGoalTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_goal_is_visible_to_both_members(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $goal = DiscipleshipGoal::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
            'visibility' => 'shared',
        ]);

        $response = $this->actingAs($disciple)->get(route('discipleship.show', $relationship));

        $response->assertOk();
        $this->assertTrue($response->viewData('goals')->pluck('id')->contains($goal->id));
    }

    public function test_private_goal_is_hidden_from_the_other_member(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $goal = DiscipleshipGoal::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
            'visibility' => 'private',
        ]);

        $discipleView = $this->actingAs($disciple)->get(route('discipleship.show', $relationship));
        $this->assertFalse($discipleView->viewData('goals')->pluck('id')->contains($goal->id));

        $mentorView = $this->actingAs($mentor)->get(route('discipleship.show', $relationship));
        $this->assertTrue($mentorView->viewData('goals')->pluck('id')->contains($goal->id));
    }

    public function test_only_creator_can_update_goal(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $goal = DiscipleshipGoal::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $mentor->id,
        ]);

        $this->actingAs($disciple)->put(route('discipleship.goals.update', [$relationship, $goal]), [
            'title' => '不應該成功',
            'status' => 'completed',
            'visibility' => 'shared',
        ])->assertForbidden();
    }
}

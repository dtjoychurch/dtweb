<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_record_is_visible_to_both_members(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $record = DiscipleshipRecord::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $disciple->id,
            'visibility' => 'shared',
        ]);

        $response = $this->actingAs($mentor)->get(route('discipleship.show', $relationship));

        $response->assertOk();
        $this->assertTrue(
            $response->viewData('timeline')->pluck('model.id')->contains($record->id)
        );
    }

    public function test_private_record_is_only_visible_to_creator(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $record = DiscipleshipRecord::factory()->create([
            'relationship_id' => $relationship->id,
            'created_by' => $disciple->id,
            'visibility' => 'private',
        ]);

        $mentorView = $this->actingAs($mentor)->get(route('discipleship.show', $relationship));
        $mentorView->assertOk();
        $this->assertFalse($mentorView->viewData('timeline')->pluck('model.id')->contains($record->id));

        $discipleView = $this->actingAs($disciple)->get(route('discipleship.show', $relationship));
        $this->assertTrue($discipleView->viewData('timeline')->pluck('model.id')->contains($record->id));
    }

    public function test_record_cannot_be_attached_to_a_session_from_a_different_relationship(): void
    {
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $otherRelationship = DiscipleshipRelationship::factory()->create();
        $foreignSession = DiscipleshipSession::factory()->create([
            'relationship_id' => $otherRelationship->id,
        ]);

        $response = $this->actingAs($mentor)->post(route('discipleship.records.store', $relationship), [
            'session_id' => $foreignSession->id,
            'type' => 'growth',
            'title' => '不應該成功',
            'content' => '內容',
            'visibility' => 'shared',
            'occurred_at' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors('session_id');
        $this->assertDatabaseMissing('discipleship_records', ['title' => '不應該成功']);
    }
}

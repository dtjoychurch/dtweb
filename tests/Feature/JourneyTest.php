<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_journey_only_shows_records_from_relationships_where_user_is_the_disciple(): void
    {
        $user = User::factory()->create();
        $someoneElse = User::factory()->create();
        $anotherMentor = User::factory()->create();

        // $user is the disciple here — this record should show on their journey.
        $asDisciple = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $anotherMentor->id,
            'disciple_id' => $user->id,
        ]);
        $ownGrowthRecord = DiscipleshipRecord::factory()->create([
            'relationship_id' => $asDisciple->id,
            'created_by' => $user->id,
            'visibility' => 'shared',
        ]);

        // $user is the mentor here — this is someone else's journey, not $user's.
        $asMentor = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $user->id,
            'disciple_id' => $someoneElse->id,
        ]);
        $menteeRecord = DiscipleshipRecord::factory()->create([
            'relationship_id' => $asMentor->id,
            'created_by' => $someoneElse->id,
            'visibility' => 'shared',
        ]);

        $response = $this->actingAs($user)->get(route('journey.index'));

        $response->assertOk();
        $records = $response->viewData('records')->pluck('id');
        $this->assertTrue($records->contains($ownGrowthRecord->id));
        $this->assertFalse($records->contains($menteeRecord->id));
    }
}

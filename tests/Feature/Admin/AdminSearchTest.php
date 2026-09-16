<?php

namespace Tests\Feature\Admin;

use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipGoal;
use App\Models\DiscipleshipNote;
use App\Models\DiscipleshipRecord;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSearchTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_relationships_index_can_be_searched_by_mentor_or_disciple_name(): void
    {
        $match = DiscipleshipRelationship::factory()->create([
            'mentor_id' => User::factory()->create(['name' => '陳大文'])->id,
        ]);
        $noMatch = DiscipleshipRelationship::factory()->create([
            'mentor_id' => User::factory()->create(['name' => '王小明'])->id,
        ]);

        $response = $this->actingAs($this->admin())->get(route('admin.relationships.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('relationships')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }

    public function test_sessions_index_can_be_searched_by_name(): void
    {
        $mentor = User::factory()->create(['name' => '陳大文']);
        $relationship = DiscipleshipRelationship::factory()->create(['mentor_id' => $mentor->id]);
        $match = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);
        $noMatch = DiscipleshipSession::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.sessions.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('sessions')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }

    public function test_records_index_can_be_searched_by_name(): void
    {
        $creator = User::factory()->create(['name' => '陳大文']);
        $match = DiscipleshipRecord::factory()->create(['created_by' => $creator->id]);
        $noMatch = DiscipleshipRecord::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.records.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('records')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }

    public function test_goals_index_can_be_searched_by_name(): void
    {
        $creator = User::factory()->create(['name' => '陳大文']);
        $match = DiscipleshipGoal::factory()->create(['created_by' => $creator->id]);
        $noMatch = DiscipleshipGoal::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.goals.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('goals')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }

    public function test_comments_index_can_be_searched_by_name(): void
    {
        $user = User::factory()->create(['name' => '陳大文']);
        $match = DiscipleshipComment::factory()->create(['user_id' => $user->id]);
        $noMatch = DiscipleshipComment::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.comments.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('comments')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }

    public function test_notes_index_can_be_searched_by_name(): void
    {
        $user = User::factory()->create(['name' => '陳大文']);
        $match = DiscipleshipNote::factory()->create(['user_id' => $user->id]);
        $noMatch = DiscipleshipNote::factory()->create();

        $response = $this->actingAs($this->admin())->get(route('admin.notes.index', ['search' => '陳大文']));

        $response->assertOk();
        $ids = $response->viewData('notes')->pluck('id');
        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($noMatch->id));
    }
}

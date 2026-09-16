<?php

namespace Tests\Feature;

use App\Models\DiscipleshipComment;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscipleshipCommentTest extends TestCase
{
    use RefreshDatabase;

    private function makeSession(): array
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

        return [$mentor, $disciple, $relationship, $session];
    }

    public function test_relationship_member_can_comment(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();

        $response = $this->actingAs($disciple)->post(
            route('discipleship.sessions.comments.store', [$relationship, $session]),
            ['content' => '這次門訓很有幫助。']
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('discipleship_comments', [
            'session_id' => $session->id,
            'user_id' => $disciple->id,
        ]);
    }

    public function test_user_can_update_own_comment(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();
        $comment = DiscipleshipComment::factory()->create([
            'session_id' => $session->id,
            'user_id' => $disciple->id,
        ]);

        $this->actingAs($disciple)->put(
            route('discipleship.sessions.comments.update', [$relationship, $session, $comment]),
            ['content' => '更新後的留言內容']
        )->assertRedirect();

        $this->assertDatabaseHas('discipleship_comments', [
            'id' => $comment->id,
            'content' => '更新後的留言內容',
        ]);
    }

    public function test_user_cannot_update_others_comment(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();
        $comment = DiscipleshipComment::factory()->create([
            'session_id' => $session->id,
            'user_id' => $disciple->id,
        ]);

        $this->actingAs($mentor)->put(
            route('discipleship.sessions.comments.update', [$relationship, $session, $comment]),
            ['content' => '不應該成功']
        )->assertForbidden();
    }

    public function test_user_cannot_delete_others_comment(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();
        $comment = DiscipleshipComment::factory()->create([
            'session_id' => $session->id,
            'user_id' => $mentor->id,
        ]);

        $this->actingAs($disciple)->delete(
            route('discipleship.sessions.comments.destroy', [$relationship, $session, $comment])
        )->assertForbidden();

        $this->assertDatabaseHas('discipleship_comments', ['id' => $comment->id]);
    }
}

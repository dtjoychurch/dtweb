<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\DiscipleshipSessionPhoto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DiscipleshipSessionPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_session_can_include_photos(): void
    {
        Storage::fake('uploads');
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);

        $this->actingAs($mentor)->post(route('discipleship.sessions.store', $relationship), [
            'session_date' => now()->toDateString(),
            'content' => '今天的門訓內容。',
            'photos' => [
                UploadedFile::fake()->image('one.jpg'),
                UploadedFile::fake()->image('two.jpg'),
            ],
        ])->assertRedirect();

        $session = DiscipleshipSession::firstOrFail();
        $this->assertSame(2, $session->photos()->count());
    }

    public function test_relationship_member_can_add_photos_to_an_existing_session(): void
    {
        Storage::fake('uploads');
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);

        $this->actingAs($disciple)->post(route('discipleship.sessions.photos.store', [$relationship, $session]), [
            'photos' => [UploadedFile::fake()->image('extra.jpg')],
        ])->assertRedirect(route('discipleship.sessions.show', [$relationship, $session]));

        $this->assertSame(1, $session->photos()->count());
    }

    public function test_unrelated_user_cannot_add_photos(): void
    {
        Storage::fake('uploads');
        $relationship = DiscipleshipRelationship::factory()->create();
        $session = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)->post(route('discipleship.sessions.photos.store', [$relationship, $session]), [
            'photos' => [UploadedFile::fake()->image('extra.jpg')],
        ])->assertForbidden();
    }

    public function test_uploader_can_delete_own_photo(): void
    {
        Storage::fake('uploads');
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);
        $photo = DiscipleshipSessionPhoto::factory()->create([
            'session_id' => $session->id,
            'uploaded_by' => $mentor->id,
        ]);

        $this->actingAs($mentor)
            ->delete(route('discipleship.sessions.photos.destroy', [$relationship, $session, $photo]))
            ->assertRedirect(route('discipleship.sessions.show', [$relationship, $session]));

        $this->assertDatabaseMissing('discipleship_session_photos', ['id' => $photo->id]);
    }

    public function test_other_relationship_member_cannot_delete_a_photo_they_did_not_upload(): void
    {
        Storage::fake('uploads');
        $mentor = User::factory()->create();
        $disciple = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create([
            'mentor_id' => $mentor->id,
            'disciple_id' => $disciple->id,
        ]);
        $session = DiscipleshipSession::factory()->create(['relationship_id' => $relationship->id]);
        $photo = DiscipleshipSessionPhoto::factory()->create([
            'session_id' => $session->id,
            'uploaded_by' => $mentor->id,
        ]);

        $this->actingAs($disciple)
            ->delete(route('discipleship.sessions.photos.destroy', [$relationship, $session, $photo]))
            ->assertForbidden();

        $this->assertDatabaseHas('discipleship_session_photos', ['id' => $photo->id]);
    }
}

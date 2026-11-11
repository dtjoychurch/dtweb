<?php

namespace Tests\Feature;

use App\Models\DiscipleshipRelationship;
use App\Models\RelationshipInvite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelationshipInviteTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_generate_an_invite_code(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('discipleship.invites.store'), ['role' => 'mentor']);

        $response->assertRedirect(route('discipleship.invites.index'));
        $this->assertDatabaseHas('relationship_invites', [
            'created_by' => $user->id,
            'role' => 'mentor',
            'status' => 'pending',
        ]);
    }

    public function test_redeeming_a_code_creates_a_pending_relationship_with_correct_roles(): void
    {
        $mentorToBe = User::factory()->create();
        $discipleToBe = User::factory()->create();
        $invite = RelationshipInvite::factory()->create([
            'created_by' => $mentorToBe->id,
            'role' => 'mentor',
        ]);

        $response = $this->actingAs($discipleToBe)->post(route('discipleship.invites.redeem'), [
            'code' => $invite->code,
        ]);

        $response->assertRedirect(route('discipleship.invites.index'));
        $this->assertDatabaseHas('discipleship_relationships', [
            'mentor_id' => $mentorToBe->id,
            'disciple_id' => $discipleToBe->id,
            'approval_status' => 'pending',
        ]);
        $this->assertDatabaseHas('relationship_invites', [
            'id' => $invite->id,
            'status' => 'redeemed',
            'redeemed_by' => $discipleToBe->id,
        ]);
    }

    public function test_redeeming_assigns_the_opposite_role_when_creator_wanted_to_be_disciple(): void
    {
        $discipleToBe = User::factory()->create();
        $mentorToBe = User::factory()->create();
        $invite = RelationshipInvite::factory()->create([
            'created_by' => $discipleToBe->id,
            'role' => 'disciple',
        ]);

        $this->actingAs($mentorToBe)->post(route('discipleship.invites.redeem'), ['code' => $invite->code]);

        $this->assertDatabaseHas('discipleship_relationships', [
            'mentor_id' => $mentorToBe->id,
            'disciple_id' => $discipleToBe->id,
        ]);
    }

    public function test_cannot_redeem_your_own_code(): void
    {
        $user = User::factory()->create();
        $invite = RelationshipInvite::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post(route('discipleship.invites.redeem'), ['code' => $invite->code]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseHas('relationship_invites', ['id' => $invite->id, 'status' => 'pending']);
    }

    public function test_cannot_redeem_an_already_redeemed_code(): void
    {
        $invite = RelationshipInvite::factory()->create(['status' => 'redeemed']);
        $someone = User::factory()->create();

        $this->actingAs($someone)->post(route('discipleship.invites.redeem'), ['code' => $invite->code])
            ->assertSessionHasErrors('code');
    }

    public function test_cannot_redeem_an_expired_code(): void
    {
        $invite = RelationshipInvite::factory()->create(['expires_at' => now()->subDay()]);
        $someone = User::factory()->create();

        $response = $this->actingAs($someone)->post(route('discipleship.invites.redeem'), ['code' => $invite->code]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseHas('relationship_invites', ['id' => $invite->id, 'status' => 'expired']);
    }

    public function test_cannot_redeem_a_nonexistent_code(): void
    {
        $someone = User::factory()->create();

        $this->actingAs($someone)->post(route('discipleship.invites.redeem'), ['code' => 'NOTREAL1'])
            ->assertSessionHasErrors('code');
    }

    public function test_cannot_generate_a_fourth_pending_invite(): void
    {
        $user = User::factory()->create();
        RelationshipInvite::factory()->count(3)->create(['created_by' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user)->post(route('discipleship.invites.store'), ['role' => 'mentor']);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseCount('relationship_invites', 3);
    }

    public function test_pending_relationships_count_toward_the_cap_too(): void
    {
        $user = User::factory()->create();
        DiscipleshipRelationship::factory()->count(2)->create([
            'mentor_id' => $user->id,
            'approval_status' => 'pending',
        ]);
        RelationshipInvite::factory()->create(['created_by' => $user->id, 'status' => 'pending']);

        $response = $this->actingAs($user)->post(route('discipleship.invites.store'), ['role' => 'mentor']);

        $response->assertSessionHasErrors('role');
    }

    public function test_cannot_redeem_when_already_at_the_cap(): void
    {
        $redeemer = User::factory()->create();
        DiscipleshipRelationship::factory()->count(3)->create([
            'mentor_id' => $redeemer->id,
            'approval_status' => 'pending',
        ]);
        $invite = RelationshipInvite::factory()->create();

        $response = $this->actingAs($redeemer)->post(route('discipleship.invites.redeem'), ['code' => $invite->code]);

        $response->assertSessionHasErrors('code');
        $this->assertDatabaseHas('relationship_invites', ['id' => $invite->id, 'status' => 'pending']);
    }

    public function test_creator_can_cancel_their_own_unredeemed_invite(): void
    {
        $user = User::factory()->create();
        $invite = RelationshipInvite::factory()->create(['created_by' => $user->id]);

        $this->actingAs($user)->delete(route('discipleship.invites.destroy', $invite))
            ->assertRedirect(route('discipleship.invites.index'));

        $this->assertDatabaseHas('relationship_invites', ['id' => $invite->id, 'status' => 'cancelled']);
    }

    public function test_cannot_cancel_someone_elses_invite(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $invite = RelationshipInvite::factory()->create(['created_by' => $owner->id]);

        $this->actingAs($other)->delete(route('discipleship.invites.destroy', $invite))->assertForbidden();
    }

    public function test_admin_can_approve_a_pending_relationship(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $relationship = DiscipleshipRelationship::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.relationships.approve', $relationship))
            ->assertRedirect(route('admin.relationships.index'));

        $this->assertDatabaseHas('discipleship_relationships', ['id' => $relationship->id, 'approval_status' => 'approved']);
    }

    public function test_admin_can_reject_a_pending_relationship(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $relationship = DiscipleshipRelationship::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($admin)->post(route('admin.relationships.reject', $relationship))
            ->assertRedirect(route('admin.relationships.index'));

        $this->assertDatabaseHas('discipleship_relationships', ['id' => $relationship->id, 'approval_status' => 'rejected']);
    }

    public function test_non_admin_cannot_approve_or_reject(): void
    {
        $member = User::factory()->create();
        $relationship = DiscipleshipRelationship::factory()->create(['approval_status' => 'pending']);

        $this->actingAs($member)->post(route('admin.relationships.approve', $relationship))->assertForbidden();
        $this->actingAs($member)->post(route('admin.relationships.reject', $relationship))->assertForbidden();
    }
}

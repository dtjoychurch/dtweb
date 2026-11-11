<?php

namespace Tests\Feature;

use App\Models\AppNotification;
use App\Models\DiscipleshipRelationship;
use App\Models\DiscipleshipSession;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    public function test_commenting_notifies_the_counterpart(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();

        $this->actingAs($mentor)->post(
            route('discipleship.sessions.comments.store', [$relationship, $session]),
            ['content' => '這次很棒！']
        );

        $this->assertDatabaseHas('app_notifications', [
            'user_id' => $disciple->id,
            'type' => 'session_comment',
        ]);
    }

    public function test_no_notification_when_the_setting_is_disabled(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();
        NotificationSetting::where('key', 'session_comment')->update(['is_enabled' => false]);

        $this->actingAs($mentor)->post(
            route('discipleship.sessions.comments.store', [$relationship, $session]),
            ['content' => '這次很棒！']
        );

        $this->assertDatabaseCount('app_notifications', 0);
    }

    public function test_no_notification_when_the_session_is_hidden_from_the_counterpart(): void
    {
        [$mentor, $disciple, $relationship, $session] = $this->makeSession();
        $session->update(['hidden_from' => $disciple->id]);

        $this->actingAs($mentor)->post(
            route('discipleship.sessions.comments.store', [$relationship, $session]),
            ['content' => '這次很棒！']
        );

        $this->assertDatabaseCount('app_notifications', 0);
    }

    public function test_viewing_the_notifications_page_marks_them_read(): void
    {
        $user = User::factory()->create();
        AppNotification::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)->get(route('notifications.index'))->assertOk();

        $this->assertDatabaseMissing('app_notifications', ['user_id' => $user->id, 'read_at' => null]);
    }

    public function test_admin_can_update_the_message_template_and_toggle(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setting = NotificationSetting::where('key', 'session_comment')->first();

        $response = $this->actingAs($admin)->put(route('admin.notification-settings.update', $setting), [
            'message_template' => ':user 留言了，共第 :ordinal 次門訓。',
            'is_enabled' => '0',
        ]);

        $response->assertRedirect(route('admin.notification-settings.index'));
        $this->assertDatabaseHas('notification_settings', [
            'id' => $setting->id,
            'message_template' => ':user 留言了，共第 :ordinal 次門訓。',
            'is_enabled' => false,
        ]);
    }

    public function test_non_admin_cannot_manage_notification_settings(): void
    {
        $member = User::factory()->create();
        $setting = NotificationSetting::where('key', 'session_comment')->first();

        $this->actingAs($member)->get(route('admin.notification-settings.index'))->assertForbidden();
        $this->actingAs($member)->put(route('admin.notification-settings.update', $setting), [
            'message_template' => '不應該成功',
        ])->assertForbidden();
    }
}

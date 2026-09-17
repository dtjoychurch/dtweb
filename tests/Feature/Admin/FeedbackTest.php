<?php

namespace Tests\Feature\Admin;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_manage_feedback(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.feedbacks.index'))->assertForbidden();
    }

    public function test_admin_can_view_feedback_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $author = User::factory()->create(['name' => '王小明']);
        Feedback::factory()->create(['user_id' => $author->id, 'content' => '介面希望更好用']);

        $response = $this->actingAs($admin)->get(route('admin.feedbacks.index'));

        $response->assertOk();
        $response->assertSee('王小明');
        $response->assertSee('介面希望更好用');
    }

    public function test_admin_can_delete_feedback(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $feedback = Feedback::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.feedbacks.destroy', $feedback));

        $response->assertRedirect(route('admin.feedbacks.index'));
        $this->assertDatabaseMissing('feedbacks', ['id' => $feedback->id]);
    }

    public function test_admin_can_search_feedback_by_user_name(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['name' => '陳大文']);
        $other = User::factory()->create(['name' => '林小美']);
        Feedback::factory()->create(['user_id' => $target->id, 'content' => '目標意見']);
        Feedback::factory()->create(['user_id' => $other->id, 'content' => '其他意見']);

        $response = $this->actingAs($admin)->get(route('admin.feedbacks.index', ['search' => '陳大文']));

        $response->assertSee('目標意見');
        $response->assertDontSee('其他意見');
    }
}

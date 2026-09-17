<?php

namespace Tests\Feature;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_submit_feedback(): void
    {
        $this->post(route('feedback.store'), ['title' => '測試標題', 'content' => '測試意見'])
            ->assertRedirect('/login');
    }

    public function test_logged_in_user_can_submit_feedback(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('feedback.store'), [
            'title' => '深色模式建議',
            'content' => '希望可以增加深色模式',
        ]);

        $response->assertRedirect(route('feedback.index'));
        $this->assertDatabaseHas('feedbacks', [
            'user_id' => $user->id,
            'title' => '深色模式建議',
            'content' => '希望可以增加深色模式',
        ]);
    }

    public function test_feedback_requires_a_title(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('feedback.store'), [
            'content' => '沒有標題的意見',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_user_only_sees_their_own_feedback_history(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Feedback::factory()->create(['user_id' => $user->id, 'content' => '我自己的意見']);
        Feedback::factory()->create(['user_id' => $other->id, 'content' => '別人的意見']);

        $response = $this->actingAs($user)->get(route('feedback.index'));

        $response->assertSee('我自己的意見');
        $response->assertDontSee('別人的意見');
    }
}

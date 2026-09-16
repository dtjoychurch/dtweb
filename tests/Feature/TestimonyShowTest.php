<?php

namespace Tests\Feature;

use App\Models\Testimony;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonyShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_an_active_testimony(): void
    {
        $testimony = Testimony::factory()->create([
            'title' => '神奇妙的帶領',
            'content' => '這是完整的見證內容，應該完整顯示在詳細頁。',
            'is_active' => true,
        ]);

        $response = $this->get(route('testimonies.show', $testimony));

        $response->assertOk();
        $response->assertSee('神奇妙的帶領');
        $response->assertSee('這是完整的見證內容，應該完整顯示在詳細頁。');
    }

    public function test_guest_cannot_view_an_inactive_testimony(): void
    {
        $testimony = Testimony::factory()->create(['is_active' => false]);

        $this->get(route('testimonies.show', $testimony))->assertNotFound();
    }

    public function test_admin_can_preview_an_inactive_testimony(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $testimony = Testimony::factory()->create(['is_active' => false, 'title' => '草稿中的見證']);

        $response = $this->actingAs($admin)->get(route('testimonies.show', $testimony));

        $response->assertOk();
        $response->assertSee('草稿中的見證');
    }

    public function test_homepage_testimony_cards_link_to_their_show_page(): void
    {
        $testimony = Testimony::factory()->create(['is_active' => true]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('testimonies.show', $testimony), false);
    }
}

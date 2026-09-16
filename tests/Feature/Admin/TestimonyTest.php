<?php

namespace Tests\Feature\Admin;

use App\Models\Testimony;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestimonyTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_manage_testimonies(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.testimonies.index'))->assertForbidden();
    }

    public function test_admin_can_create_a_testimony_with_image(): void
    {
        Storage::fake('uploads');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.testimonies.store'), [
            'image' => UploadedFile::fake()->image('testimony.jpg'),
            'title' => '神奇妙的帶領',
            'content' => '這是一段見證內容。',
            'sort_order' => 0,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.testimonies.index'));

        $testimony = Testimony::firstOrFail();
        $this->assertSame('神奇妙的帶領', $testimony->title);
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $testimony->image_path));
    }

    public function test_only_active_testimonies_appear_on_the_homepage(): void
    {
        Testimony::factory()->create(['title' => '啟用中的見證', 'is_active' => true]);
        Testimony::factory()->create(['title' => '已停用的見證', 'is_active' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('啟用中的見證');
        $response->assertDontSee('已停用的見證');
    }
}

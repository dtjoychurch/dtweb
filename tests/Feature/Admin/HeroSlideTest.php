<?php

namespace Tests\Feature\Admin;

use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroSlideTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_manage_hero_slides(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.hero-slides.index'))->assertForbidden();
    }

    public function test_admin_can_upload_a_hero_slide(): void
    {
        Storage::fake('uploads');
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.hero-slides.store'), [
            'image' => UploadedFile::fake()->image('hero.jpg'),
            'title' => '測試主標',
            'subtitle' => '測試副標',
            'sort_order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.hero-slides.index'));

        $slide = HeroSlide::firstOrFail();
        $this->assertSame('測試主標', $slide->title);
        Storage::disk('uploads')->assertExists(str_replace('uploads/', '', $slide->image_path));
    }

    public function test_unchecking_active_deactivates_the_slide(): void
    {
        Storage::fake('uploads');
        $admin = User::factory()->create(['role' => 'admin']);
        $slide = HeroSlide::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->put(route('admin.hero-slides.update', $slide), [
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'sort_order' => 0,
            'is_active' => '0',
        ])->assertRedirect(route('admin.hero-slides.index'));

        $this->assertFalse($slide->fresh()->is_active);
    }

    public function test_only_active_slides_appear_on_the_homepage(): void
    {
        HeroSlide::factory()->create(['title' => '啟用中的Slide', 'is_active' => true]);
        HeroSlide::factory()->create(['title' => '已停用的Slide', 'is_active' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('啟用中的Slide');
        $response->assertDontSee('已停用的Slide');
    }
}

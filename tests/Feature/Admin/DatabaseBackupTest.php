<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_view_the_backup_list(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.database-backups.index'))->assertForbidden();
    }

    public function test_non_admin_cannot_trigger_a_restore(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)
            ->post(route('admin.database-backups.restore', 'backup-2026-01-01_000000.sql'))
            ->assertForbidden();
    }

    public function test_admin_sees_backups_from_the_bucket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('s3');
        Storage::disk('s3')->put('database-backups/backup-2026-01-01_000000.sql', 'dummy');

        $response = $this->actingAs($admin)->get(route('admin.database-backups.index'));

        $response->assertOk();
        $response->assertSee('backup-2026-01-01_000000.sql');
    }

    public function test_a_malformed_filename_is_rejected_before_touching_storage(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.database-backups.restore', '../../etc/passwd'))
            ->assertNotFound();
    }

    public function test_admin_can_restore_a_valid_backup(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('s3');
        Storage::disk('s3')->put('database-backups/backup-2026-01-01_000000.sql', 'SELECT 1;');

        $response = $this->actingAs($admin)
            ->post(route('admin.database-backups.restore', 'backup-2026-01-01_000000.sql'));

        $response->assertRedirect(route('admin.database-backups.index'));
        $response->assertSessionHas('status');
    }
}

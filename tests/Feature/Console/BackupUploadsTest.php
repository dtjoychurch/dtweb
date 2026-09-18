<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupUploadsTest extends TestCase
{
    public function test_it_copies_new_files_from_uploads_to_the_backup_disk(): void
    {
        Storage::fake('uploads');
        Storage::fake('s3');

        Storage::disk('uploads')->put('sessions/photo1.jpg', 'fake-image-content');
        Storage::disk('uploads')->put('testimonies/cover.jpg', 'other-fake-content');

        $this->artisan('app:backup-uploads')->assertExitCode(0);

        Storage::disk('s3')->assertExists('sessions/photo1.jpg');
        Storage::disk('s3')->assertExists('testimonies/cover.jpg');
        $this->assertSame('fake-image-content', Storage::disk('s3')->get('sessions/photo1.jpg'));
    }

    public function test_it_skips_files_already_backed_up_with_the_same_size(): void
    {
        Storage::fake('uploads');
        Storage::fake('s3');

        Storage::disk('uploads')->put('sessions/photo1.jpg', 'fake-image-content');

        $this->artisan('app:backup-uploads')->assertExitCode(0);
        $this->artisan('app:backup-uploads')
            ->expectsOutputToContain('略過 1 個已是最新的檔案')
            ->assertExitCode(0);
    }

    public function test_it_re_uploads_a_file_if_its_size_changed(): void
    {
        Storage::fake('uploads');
        Storage::fake('s3');

        Storage::disk('uploads')->put('sessions/photo1.jpg', 'short');
        $this->artisan('app:backup-uploads')->assertExitCode(0);

        Storage::disk('uploads')->put('sessions/photo1.jpg', 'a much longer replacement content');
        $this->artisan('app:backup-uploads')->assertExitCode(0);

        $this->assertSame('a much longer replacement content', Storage::disk('s3')->get('sessions/photo1.jpg'));
    }

    public function test_it_fails_loudly_instead_of_silently_when_the_backup_disk_rejects_a_file(): void
    {
        Storage::fake('uploads');
        Storage::disk('uploads')->put('sessions/photo1.jpg', 'fake-image-content');

        $failingDisk = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $failingDisk->shouldReceive('exists')->andReturn(false);
        $failingDisk->shouldReceive('put')->andReturn(false);
        Storage::set('s3', $failingDisk);

        $this->artisan('app:backup-uploads')
            ->expectsOutputToContain('備份失敗')
            ->assertExitCode(1);
    }
}

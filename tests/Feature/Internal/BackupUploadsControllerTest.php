<?php

namespace Tests\Feature\Internal;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupUploadsControllerTest extends TestCase
{
    public function test_request_without_a_token_is_rejected(): void
    {
        config(['services.backup.token' => 'correct-secret']);

        $this->postJson(route('internal.backup-uploads'))->assertForbidden();
    }

    public function test_request_with_the_wrong_token_is_rejected(): void
    {
        config(['services.backup.token' => 'correct-secret']);

        $this->postJson(route('internal.backup-uploads'), [], ['X-Backup-Token' => 'wrong-secret'])
            ->assertForbidden();
    }

    public function test_request_is_rejected_when_no_token_is_configured(): void
    {
        config(['services.backup.token' => null]);

        $this->postJson(route('internal.backup-uploads'), [], ['X-Backup-Token' => 'anything'])
            ->assertForbidden();
    }

    public function test_request_with_the_correct_token_runs_the_backup(): void
    {
        config(['services.backup.token' => 'correct-secret']);
        Storage::fake('uploads');
        Storage::fake('s3');
        Storage::disk('uploads')->put('sessions/photo1.jpg', 'fake-image-content');

        $response = $this->postJson(route('internal.backup-uploads'), [], ['X-Backup-Token' => 'correct-secret']);

        $response->assertOk();
        $response->assertJson(['exit_code' => 0]);
        Storage::disk('s3')->assertExists('sessions/photo1.jpg');
    }
}

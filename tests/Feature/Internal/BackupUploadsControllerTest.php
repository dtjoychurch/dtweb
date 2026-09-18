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

    public function test_a_broken_s3_config_returns_clean_json_instead_of_a_crash(): void
    {
        config([
            'services.backup.token' => 'correct-secret',
            // 沒有設定 bucket，會讓 Flysystem 在建立 adapter 時直接丟出 TypeError，
            // 而不是被指令內建的 throw=>false 擋下來 —— 這裡驗證這種情況也不會讓
            // 呼叫端收到原始的錯誤頁面。
            'filesystems.disks.s3.bucket' => null,
        ]);
        Storage::fake('uploads');
        Storage::disk('uploads')->put('sessions/photo1.jpg', 'fake-image-content');

        $response = $this->postJson(route('internal.backup-uploads'), [], ['X-Backup-Token' => 'correct-secret']);

        $response->assertStatus(500);
        $response->assertJsonStructure(['exit_code', 'error']);
    }
}

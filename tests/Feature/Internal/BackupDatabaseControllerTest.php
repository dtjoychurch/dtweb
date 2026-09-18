<?php

namespace Tests\Feature\Internal;

use App\Console\Commands\BackupDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupDatabaseControllerTest extends TestCase
{
    public function test_request_without_a_token_is_rejected(): void
    {
        config(['services.backup.token' => 'correct-secret']);

        $this->postJson(route('internal.backup-database'))->assertForbidden();
    }

    public function test_request_with_the_wrong_token_is_rejected(): void
    {
        config(['services.backup.token' => 'correct-secret']);

        $this->postJson(route('internal.backup-database'), [], ['X-Backup-Token' => 'wrong-secret'])
            ->assertForbidden();
    }

    public function test_request_with_the_correct_token_runs_the_backup(): void
    {
        config(['services.backup.token' => 'correct-secret']);
        Storage::fake('s3');

        $this->app->bind(BackupDatabase::class, fn () => new class extends BackupDatabase
        {
            protected function dump(string $tmpFile): void
            {
                file_put_contents($tmpFile, '-- fake dump');
            }
        });

        $response = $this->postJson(route('internal.backup-database'), [], ['X-Backup-Token' => 'correct-secret']);

        $response->assertOk();
        $response->assertJson(['exit_code' => 0]);
        $this->assertNotEmpty(Storage::disk('s3')->files('database-backups'));
    }
}

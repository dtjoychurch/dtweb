<?php

namespace Tests\Feature\Console;

use App\Console\Commands\BackupDatabase;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class BackupDatabaseTest extends TestCase
{
    /**
     * The actual SQL-generation is done by ifsnop/mysqldump-php against a real
     * MySQL connection - not something sqlite (used by this test suite) can
     * exercise, since that library's SQLite adapter is missing methods entirely.
     * Verified separately against the real MySQL dev container. Here we mock
     * the dump() step to test the surrounding upload/prune/error-handling logic.
     */
    private function fakeDumpWriting(string $content): void
    {
        // Mockery's partial mocks skip Command's constructor, which Symfony
        // Console needs to set up $signature parsing - a plain subclass avoids
        // that entirely and is simpler than fighting Mockery about it.
        $this->app->bind(BackupDatabase::class, fn () => new class($content) extends BackupDatabase
        {
            public function __construct(private readonly string $content)
            {
                parent::__construct();
            }

            protected function dump(string $tmpFile): void
            {
                file_put_contents($tmpFile, $this->content);
            }
        });
    }

    public function test_it_uploads_the_dump_file_to_the_backup_disk(): void
    {
        Storage::fake('s3');
        $this->fakeDumpWriting("-- fake dump\nINSERT INTO widgets VALUES (1, 'test-widget-xyz');\n");

        $this->artisan('app:backup-database')->assertExitCode(0);

        $files = Storage::disk('s3')->files('database-backups');
        $this->assertCount(1, $files);
        $this->assertStringContainsString('test-widget-xyz', Storage::disk('s3')->get($files[0]));
    }

    public function test_it_reports_failure_when_the_dump_step_throws(): void
    {
        $this->app->bind(BackupDatabase::class, fn () => new class extends BackupDatabase
        {
            protected function dump(string $tmpFile): void
            {
                throw new RuntimeException('connection refused');
            }
        });

        $this->artisan('app:backup-database')->assertExitCode(1);
    }

    public function test_it_only_keeps_the_30_most_recent_backups(): void
    {
        Storage::fake('s3');
        foreach (range(1, 32) as $i) {
            Storage::disk('s3')->put(sprintf('database-backups/backup-2026-01-%02d_000000.sql', $i), 'old dump');
        }
        $this->fakeDumpWriting('new dump');

        $this->artisan('app:backup-database')->assertExitCode(0);

        $files = Storage::disk('s3')->files('database-backups');
        $this->assertCount(30, $files);
        Storage::disk('s3')->assertMissing('database-backups/backup-2026-01-01_000000.sql');
        Storage::disk('s3')->assertMissing('database-backups/backup-2026-01-03_000000.sql');
    }
}

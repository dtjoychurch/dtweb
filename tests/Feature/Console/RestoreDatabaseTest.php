<?php

namespace Tests\Feature\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RestoreDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_downloads_and_executes_the_backup_sql(): void
    {
        Storage::fake('s3');

        $sql = <<<'SQL'
        -- comment line should be skipped
        DROP TABLE IF EXISTS restore_test_table;
        CREATE TABLE restore_test_table (id INTEGER PRIMARY KEY, name TEXT);
        INSERT INTO restore_test_table (id, name) VALUES (1, 'restored-value');
        SQL;

        Storage::disk('s3')->put('database-backups/backup-2026-01-01_000000.sql', $sql);

        $this->artisan('app:restore-database', ['file' => 'database-backups/backup-2026-01-01_000000.sql'])
            ->assertExitCode(0);

        $this->assertSame('restored-value', DB::table('restore_test_table')->first()->name);
    }

    public function test_it_fails_cleanly_when_the_file_does_not_exist(): void
    {
        Storage::fake('s3');

        $this->artisan('app:restore-database', ['file' => 'database-backups/does-not-exist.sql'])
            ->assertExitCode(1);
    }

    public function test_it_fails_cleanly_when_a_statement_is_invalid(): void
    {
        Storage::fake('s3');
        Storage::disk('s3')->put('database-backups/backup-2026-01-02_000000.sql', 'THIS IS NOT VALID SQL;');

        $this->artisan('app:restore-database', ['file' => 'database-backups/backup-2026-01-02_000000.sql'])
            ->assertExitCode(1);
    }

    public function test_it_warns_when_the_safety_backup_before_restoring_fails(): void
    {
        Storage::fake('s3');
        Storage::disk('s3')->put('database-backups/backup-2026-01-03_000000.sql', 'SELECT 1;');

        // The test DB connection is sqlite, which BackupDatabase's real dump()
        // step can't target (mysqldump-php needs a real MySQL DSN), so the
        // safety backup genuinely fails here - exactly the scenario this
        // warning exists for.
        $this->artisan('app:restore-database', ['file' => 'database-backups/backup-2026-01-03_000000.sql'])
            ->expectsOutputToContain('安全備份失敗')
            ->assertExitCode(0);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class RestoreDatabase extends Command
{
    protected $signature = 'app:restore-database {file : 備份檔案在雲端儲存上的路徑，例如 database-backups/backup-2026-01-01_000000.sql}';

    protected $description = '從備份的 SQL 檔案還原資料庫——危險操作，會覆蓋現有資料';

    public function handle(): int
    {
        $file = $this->argument('file');
        $disk = Storage::disk('s3');

        if (! $disk->exists($file)) {
            $this->error("找不到備份檔案：{$file}");

            return self::FAILURE;
        }

        // 還原前先備份現在的狀態，萬一選錯備份還能救回來。
        $this->info('先備份目前的資料庫狀態……');

        if (Artisan::call('app:backup-database') !== 0) {
            $this->warn('警告：還原前的安全備份失敗了，這次還原沒有安全網。');
        }

        $sql = $disk->get($file);

        try {
            $this->runStatements($sql);
        } catch (Throwable $e) {
            $this->error("還原失敗：{$e->getMessage()}");

            return self::FAILURE;
        }

        $this->info("資料庫已還原：{$file}");

        return self::SUCCESS;
    }

    protected function runStatements(string $sql): void
    {
        foreach ($this->splitStatements($sql) as $statement) {
            DB::unprepared($statement);
        }
    }

    private function splitStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';

        foreach (preg_split('/\r?\n/', $sql) as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }

            $buffer .= $line."\n";

            if (str_ends_with($trimmed, ';')) {
                $statements[] = trim($buffer);
                $buffer = '';
            }
        }

        return array_filter($statements);
    }
}

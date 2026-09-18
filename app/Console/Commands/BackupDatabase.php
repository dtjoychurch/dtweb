<?php

namespace App\Console\Commands;

use Ifsnop\Mysqldump\Mysqldump;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'app:backup-database';

    protected $description = '把資料庫內容匯出成 SQL，上傳到雲端物件儲存，並只保留最近 30 份';

    private const KEEP = 30;

    public function handle(): int
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'db-backup-');

        try {
            $this->dump($tmpFile);
        } catch (Throwable $e) {
            $this->error("資料庫匯出失敗：{$e->getMessage()}");
            @unlink($tmpFile);

            return self::FAILURE;
        }

        $filename = 'database-backups/backup-'.now()->format('Y-m-d_His').'.sql';
        $stream = fopen($tmpFile, 'r');
        $ok = Storage::disk('s3')->put($filename, $stream);

        if (is_resource($stream)) {
            fclose($stream);
        }

        @unlink($tmpFile);

        if (! $ok) {
            $this->error('資料庫備份上傳失敗。');

            return self::FAILURE;
        }

        $this->info("資料庫備份完成：{$filename}");
        $this->pruneOldBackups();

        return self::SUCCESS;
    }

    /**
     * 拆成獨立方法方便測試替換——ifsnop/mysqldump-php 的 SQLite adapter 並不完整
     * （缺少方法），只有 MySQL 是真正支援、測過的路徑，正式環境也只會用 MySQL。
     */
    protected function dump(string $tmpFile): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($connection !== 'mysql') {
            throw new RuntimeException("不支援的資料庫連線類型：{$connection}");
        }

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";

        $dump = new Mysqldump($dsn, $config['username'] ?? '', $config['password'] ?? '');
        $dump->start($tmpFile);
    }

    private function pruneOldBackups(): void
    {
        $disk = Storage::disk('s3');
        $files = $disk->files('database-backups');
        sort($files);

        $toDelete = array_slice($files, 0, max(0, count($files) - self::KEEP));

        if ($toDelete !== []) {
            $disk->delete($toDelete);
            $this->info('清掉 '.count($toDelete).' 份超過保留數量的舊備份。');
        }
    }
}

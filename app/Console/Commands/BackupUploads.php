<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BackupUploads extends Command
{
    protected $signature = 'app:backup-uploads';

    protected $description = '將 public/uploads 底下的檔案（門訓照片、Hero 圖片、見證圖片等）同步備份到雲端物件儲存';

    public function handle(): int
    {
        $source = Storage::disk('uploads');
        $backup = Storage::disk('s3');

        $files = $source->allFiles();
        $copied = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($files as $file) {
            if ($backup->exists($file) && $backup->size($file) === $source->size($file)) {
                $skipped++;

                continue;
            }

            $stream = $source->readStream($file);
            $ok = $backup->put($file, $stream);

            if (is_resource($stream)) {
                fclose($stream);
            }

            if ($ok) {
                $copied++;
            } else {
                $failed++;
                $this->error("備份失敗：{$file}");
            }
        }

        $this->info("備份完成：新增/更新 {$copied} 個檔案，略過 {$skipped} 個已是最新的檔案，失敗 {$failed} 個。");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 每天把 uploads 備份到雲端物件儲存，避免 Railway 重新部署把照片清掉。
Schedule::command('app:backup-uploads')->daily();

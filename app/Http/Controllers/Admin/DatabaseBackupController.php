<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        $disk = Storage::disk('s3');

        $backups = collect($disk->files('database-backups'))
            ->sortDesc()
            ->values()
            ->map(fn (string $path) => [
                'filename' => basename($path),
                'size' => $disk->size($path),
                'last_modified' => Carbon::createFromTimestamp($disk->lastModified($path)),
            ]);

        return view('admin.database-backups.index', compact('backups'));
    }

    public function restore(string $filename): RedirectResponse
    {
        if (! preg_match('/^backup-\d{4}-\d{2}-\d{2}_\d{6}\.sql$/', $filename)) {
            abort(404);
        }

        $exitCode = Artisan::call('app:restore-database', ['file' => 'database-backups/'.$filename]);

        if ($exitCode !== 0) {
            return back()->withErrors(['restore' => '還原失敗：'.Artisan::output()]);
        }

        return redirect()->route('admin.database-backups.index')->with('status', '資料庫已還原完成。');
    }
}

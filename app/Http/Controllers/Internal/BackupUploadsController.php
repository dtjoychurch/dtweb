<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BackupUploadsController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $expected = config('services.backup.token');
        $given = (string) $request->header('X-Backup-Token');

        if (! $expected || ! hash_equals($expected, $given)) {
            abort(403);
        }

        $exitCode = Artisan::call('app:backup-uploads');

        return response()->json([
            'exit_code' => $exitCode,
            'output' => Artisan::output(),
        ], $exitCode === 0 ? 200 : 500);
    }
}

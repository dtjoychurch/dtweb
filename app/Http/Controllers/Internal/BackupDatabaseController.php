<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class BackupDatabaseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $expected = config('services.backup.token');
        $given = (string) $request->header('X-Backup-Token');

        if (! $expected || ! hash_equals($expected, $given)) {
            abort(403);
        }

        try {
            $exitCode = Artisan::call('app:backup-database');

            return response()->json([
                'exit_code' => $exitCode,
                'output' => Artisan::output(),
            ], $exitCode === 0 ? 200 : 500);
        } catch (Throwable $e) {
            return response()->json([
                'exit_code' => 1,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

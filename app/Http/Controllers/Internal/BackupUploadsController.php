<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class BackupUploadsController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $expected = config('services.backup.token');
        $given = (string) $request->header('X-Backup-Token');

        if (! $expected || ! hash_equals($expected, $given)) {
            // 暫時性除錯輸出：只給雜湊前綴和長度做比對，絕對不會洩漏密鑰本身，
            // 確認問題排除後就會拿掉這段。
            return response()->json([
                'error' => 'invalid token',
                'debug' => [
                    'expected_configured' => (bool) $expected,
                    'expected_length' => strlen((string) $expected),
                    'expected_hash_prefix' => substr(hash('sha256', (string) $expected), 0, 8),
                    'given_length' => strlen($given),
                    'given_hash_prefix' => substr(hash('sha256', $given), 0, 8),
                ],
            ], 403);
        }

        // 備份指令本身對「單一檔案上傳失敗」已經有處理，但像雲端儲存設定本身
        // 不完整（例如 bucket 沒設）這種在建立連線階段就出錯的狀況，Flysystem
        // 會直接拋出例外，不會被 throw=>false 擋下來。這裡整個包起來，確保
        // 不管哪裡壞掉，呼叫端（GitHub Actions）都能拿到乾淨的 JSON 錯誤訊息，
        // 而不是一大包 HTML 錯誤頁。
        try {
            $exitCode = Artisan::call('app:backup-uploads');

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

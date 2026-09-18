<?php

use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\GoalController as AdminGoalController;
use App\Http\Controllers\Api\Admin\NoteController as AdminNoteController;
use App\Http\Controllers\Api\Admin\RecordController as AdminRecordController;
use App\Http\Controllers\Api\Admin\RelationshipController as AdminRelationshipController;
use App\Http\Controllers\Api\Admin\SessionController as AdminSessionController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\DiscipleshipController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\JourneyController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\RecordController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Internal\BackupDatabaseController;
use App\Http\Controllers\Internal\BackupUploadsController;
use Illuminate\Support\Facades\Route;

// 每個 IP 每分鐘最多 6 次，防止暴力破解密碼 / 機器人大量灌帳號。
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// 給站外排程服務（例如 GitHub Actions）用密鑰觸發每日備份，見 config/services.php 的說明。
// 放在 api.php 而不是 web.php，是因為 web 路由預設會套用 CSRF 驗證，外部排程呼叫
// 沒有瀏覽器 session，永遠無法通過 CSRF 檢查（會被擋成 419）；api 群組沒有這個問題。
Route::post('/internal/backup-uploads', [BackupUploadsController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('internal.backup-uploads');

Route::post('/internal/backup-database', [BackupDatabaseController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('internal.backup-database');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // 門訓
    Route::get('/discipleship', [DiscipleshipController::class, 'index']);
    Route::get('/discipleship/{relationship}', [DiscipleshipController::class, 'show']);
    Route::get('/discipleship/{relationship}/sessions', [SessionController::class, 'index']);
    Route::post('/discipleship/{relationship}/sessions', [SessionController::class, 'store']);
    Route::post('/discipleship/{relationship}/records', [RecordController::class, 'store']);
    Route::post('/discipleship/{relationship}/goals', [GoalController::class, 'store']);

    // 單次門訓
    Route::get('/sessions/{session}', [SessionController::class, 'show']);
    Route::delete('/sessions/{session}', [SessionController::class, 'destroy']);
    Route::post('/sessions/{session}/comments', [CommentController::class, 'store']);

    // 留言
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // 生命歷程紀錄
    Route::delete('/records/{record}', [RecordController::class, 'destroy']);

    // 門訓目標
    Route::get('/goals', [GoalController::class, 'index']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);

    // 我的私人筆記
    Route::get('/notes', [NoteController::class, 'index']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    // 生命歷程（總覽）
    Route::get('/journey', [JourneyController::class, 'index']);

    // 後台
    // 注意：一定要加 name('api.admin.') 前綴——這幾個 apiResource 產生的路由名稱
    // 預設是 users.index / notes.index 這種裸名字，會跟 routes/web.php 前台的
    // Route::resource('notes', ...) 等同名路由衝突，導致 `route:cache` 在正式環境
    // build 時直接噴 LogicException（本地 `php artisan serve` 不會觸發，因為
    // route:cache 才會真的去檢查名稱唯一性，這就是為什麼本地測試都正常）。
    Route::prefix('admin')->middleware('admin')->name('api.admin.')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('relationships', AdminRelationshipController::class);
        Route::apiResource('sessions', AdminSessionController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('comments', AdminCommentController::class)->only(['index', 'destroy']);
        Route::apiResource('notes', AdminNoteController::class)->only(['index', 'destroy']);
        Route::apiResource('records', AdminRecordController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('goals', AdminGoalController::class)->only(['index', 'update', 'destroy']);
    });
});

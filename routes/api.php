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
use Illuminate\Support\Facades\Route;

// 每個 IP 每分鐘最多 6 次，防止暴力破解密碼 / 機器人大量灌帳號。
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

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
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::apiResource('users', AdminUserController::class);
        Route::apiResource('relationships', AdminRelationshipController::class);
        Route::apiResource('sessions', AdminSessionController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('comments', AdminCommentController::class)->only(['index', 'destroy']);
        Route::apiResource('notes', AdminNoteController::class)->only(['index', 'destroy']);
        Route::apiResource('records', AdminRecordController::class)->only(['index', 'update', 'destroy']);
        Route::apiResource('goals', AdminGoalController::class)->only(['index', 'update', 'destroy']);
    });
});

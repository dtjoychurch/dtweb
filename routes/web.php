<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DiscipleshipCommentController;
use App\Http\Controllers\DiscipleshipController;
use App\Http\Controllers\DiscipleshipGoalController;
use App\Http\Controllers\DiscipleshipNoteController;
use App\Http\Controllers\DiscipleshipRecordController;
use App\Http\Controllers\DiscipleshipSessionController;
use App\Http\Controllers\DiscipleshipSessionPhotoController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JourneyController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestimonyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/about', [PageController::class, 'about'])->name('pages.about');
Route::get('/resources', [PageController::class, 'resources'])->name('pages.resources');
Route::get('/testimonies/{testimony}', [TestimonyController::class, 'show'])->name('testimonies.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');

    // 每個 IP 每分鐘最多 6 次，防止暴力破解密碼 / 機器人大量灌帳號。
    Route::middleware('throttle:6,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 門訓
    Route::get('/discipleship', [DiscipleshipController::class, 'index'])->name('discipleship.index');
    Route::get('/discipleship/{relationship}', [DiscipleshipController::class, 'show'])->name('discipleship.show');

    // 單次門訓
    Route::post('/discipleship/{relationship}/sessions', [DiscipleshipSessionController::class, 'store'])->name('discipleship.sessions.store');
    Route::get('/discipleship/{relationship}/sessions/{session}', [DiscipleshipSessionController::class, 'show'])->name('discipleship.sessions.show');
    Route::delete('/discipleship/{relationship}/sessions/{session}', [DiscipleshipSessionController::class, 'destroy'])->name('discipleship.sessions.destroy');

    // 門訓照片
    Route::post('/discipleship/{relationship}/sessions/{session}/photos', [DiscipleshipSessionPhotoController::class, 'store'])->name('discipleship.sessions.photos.store');
    Route::delete('/discipleship/{relationship}/sessions/{session}/photos/{photo}', [DiscipleshipSessionPhotoController::class, 'destroy'])->name('discipleship.sessions.photos.destroy');

    // 留言
    Route::post('/discipleship/{relationship}/sessions/{session}/comments', [DiscipleshipCommentController::class, 'store'])->name('discipleship.sessions.comments.store');
    Route::put('/discipleship/{relationship}/sessions/{session}/comments/{comment}', [DiscipleshipCommentController::class, 'update'])->name('discipleship.sessions.comments.update');
    Route::delete('/discipleship/{relationship}/sessions/{session}/comments/{comment}', [DiscipleshipCommentController::class, 'destroy'])->name('discipleship.sessions.comments.destroy');

    // 生命歷程紀錄（掛在門訓關係下）
    Route::post('/discipleship/{relationship}/records', [DiscipleshipRecordController::class, 'store'])->name('discipleship.records.store');
    Route::delete('/discipleship/{relationship}/records/{record}', [DiscipleshipRecordController::class, 'destroy'])->name('discipleship.records.destroy');

    // 門訓目標
    Route::post('/discipleship/{relationship}/goals', [DiscipleshipGoalController::class, 'store'])->name('discipleship.goals.store');
    Route::put('/discipleship/{relationship}/goals/{goal}', [DiscipleshipGoalController::class, 'update'])->name('discipleship.goals.update');
    Route::delete('/discipleship/{relationship}/goals/{goal}', [DiscipleshipGoalController::class, 'destroy'])->name('discipleship.goals.destroy');

    // 我的私人筆記
    Route::resource('notes', DiscipleshipNoteController::class)->except(['show']);

    // 生命歷程（總覽）
    Route::get('/journey', [JourneyController::class, 'index'])->name('journey.index');
});

require __DIR__.'/admin.php';

<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GoalController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\RecordController;
use App\Http\Controllers\Admin\RelationshipController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\TestimonyController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('relationships', RelationshipController::class)->except(['show']);
    Route::resource('sessions', SessionController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('comments', CommentController::class)->only(['index', 'destroy']);
    Route::resource('notes', NoteController::class)->only(['index', 'destroy']);
    Route::resource('records', RecordController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('goals', GoalController::class)->only(['index', 'edit', 'update', 'destroy']);

    // 內容管理：首頁 Hero、見證分享
    Route::resource('hero-slides', HeroSlideController::class)->except(['show']);
    Route::resource('testimonies', TestimonyController::class)->except(['show']);
});

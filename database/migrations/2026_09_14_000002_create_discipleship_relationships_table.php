<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discipleship_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('disciple_id')->constrained('users')->restrictOnDelete();
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->enum('status', ['active', 'paused', 'completed'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // 明確給短一點的索引名稱——Laravel 自動產生的名字在 MySQL 上會超過
            // 64 字元上限（identifier 太長），SQLite 不會檢查所以本機測試沒抓到。
            $table->unique(['mentor_id', 'disciple_id', 'started_at'], 'discipleship_relationships_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipleship_relationships');
    }
};

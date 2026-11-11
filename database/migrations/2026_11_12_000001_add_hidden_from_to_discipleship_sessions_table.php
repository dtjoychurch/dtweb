<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discipleship_sessions', function (Blueprint $table) {
            // 兩邊都能編輯這筆門訓紀錄，所以「隱藏」沒辦法用 created_by 判斷
            // （不是建立者編輯時會把自己也鎖在外面）。改成直接記錄「這筆對誰隱藏」，
            // null 代表雙方都看得到。
            $table->foreignId('hidden_from')->nullable()->after('content')
                ->constrained('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('discipleship_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('hidden_from');
        });
    }
};

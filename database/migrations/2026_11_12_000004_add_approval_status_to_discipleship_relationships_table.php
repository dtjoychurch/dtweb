<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 刻意用獨立的字串欄位，不是把 'pending'/'rejected' 加進現有的 status enum——
     * 沒有 doctrine/dbal，MySQL 的 enum 要改用 raw ALTER TABLE MODIFY，但 SQLite
     * 的 enum 是用 CHECK constraint 模擬的，兩邊沒有共通、可攜的改法。新增欄位
     * 不管哪個 driver 都是單純的 ADD COLUMN，不用管這個問題。
     */
    public function up(): void
    {
        Schema::table('discipleship_relationships', function (Blueprint $table) {
            $table->string('approval_status')->default('approved')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('discipleship_relationships', function (Blueprint $table) {
            $table->dropColumn('approval_status');
        });
    }
};

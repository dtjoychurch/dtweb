<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('message_template');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        DB::table('notification_settings')->insert([
            'key' => 'session_comment',
            'label' => '門訓紀錄有新留言',
            'message_template' => ':user 在「第 :ordinal 次門訓」留言了。',
            'is_enabled' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};

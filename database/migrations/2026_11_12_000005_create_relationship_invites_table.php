<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('relationship_invites', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('role'); // 產生這個碼的人想當 mentor 還是 disciple
            $table->string('status')->default('pending'); // pending / redeemed / cancelled / expired
            $table->timestamp('expires_at');
            $table->foreignId('redeemed_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('redeemed_at')->nullable();
            $table->foreignId('relationship_id')->nullable()->constrained('discipleship_relationships')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relationship_invites');
    }
};

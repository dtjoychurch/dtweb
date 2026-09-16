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

            $table->unique(['mentor_id', 'disciple_id', 'started_at']);
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

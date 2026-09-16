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
        Schema::create('discipleship_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relationship_id')->constrained('discipleship_relationships')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->enum('visibility', ['shared', 'private'])->default('shared');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['relationship_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipleship_goals');
    }
};

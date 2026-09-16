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
        Schema::create('discipleship_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relationship_id')->constrained('discipleship_relationships')->restrictOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('discipleship_sessions')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->enum('type', [
                'growth', 'struggle', 'reflection', 'prayer',
                'milestone', 'observation', 'decision', 'testimony',
            ]);
            $table->string('title');
            $table->longText('content');
            $table->enum('visibility', ['shared', 'private'])->default('shared');
            $table->date('occurred_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['relationship_id', 'occurred_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipleship_records');
    }
};

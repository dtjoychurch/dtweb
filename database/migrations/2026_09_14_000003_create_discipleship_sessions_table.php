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
        Schema::create('discipleship_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('relationship_id')->constrained('discipleship_relationships')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->date('session_date');
            $table->string('title')->nullable();
            $table->longText('content');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['relationship_id', 'session_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discipleship_sessions');
    }
};

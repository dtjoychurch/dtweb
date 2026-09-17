<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The 8 types that used to be a hardcoded enum on discipleship_records.
     * Seeded here (not in DatabaseSeeder) so every environment — including
     * production, which only ever runs migrations — ends up with them.
     */
    public const DEFAULT_TYPES = [
        ['slug' => 'growth', 'name' => '成長', 'sort_order' => 1],
        ['slug' => 'struggle', 'name' => '掙扎', 'sort_order' => 2],
        ['slug' => 'reflection', 'name' => '反思', 'sort_order' => 3],
        ['slug' => 'prayer', 'name' => '禱告', 'sort_order' => 4],
        ['slug' => 'milestone', 'name' => '里程碑', 'sort_order' => 5],
        ['slug' => 'observation', 'name' => '觀察', 'sort_order' => 6],
        ['slug' => 'decision', 'name' => '決定', 'sort_order' => 7],
        ['slug' => 'testimony', 'name' => '見證', 'sort_order' => 8],
    ];

    public function up(): void
    {
        Schema::create('discipleship_record_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('discipleship_record_types')->insert(array_map(
            fn (array $type) => [...$type, 'created_at' => $now, 'updated_at' => $now],
            self::DEFAULT_TYPES
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('discipleship_record_types');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Swaps the hardcoded `type` enum column for a `type_id` foreign key
     * into discipleship_record_types, backfilling existing rows by slug
     * so this is safe to run against a database that already has real
     * records (not just a fresh install).
     *
     * type_id stays nullable at the DB level deliberately — making it
     * NOT NULL after the fact needs Schema::change(), which requires the
     * doctrine/dbal package that this project doesn't install. Application
     * validation (StoreDiscipleshipRecordRequest) already requires it on
     * every create/update, so this is a non-issue in practice.
     */
    public function up(): void
    {
        Schema::table('discipleship_records', function (Blueprint $table) {
            $table->foreignId('type_id')->nullable()->after('type')->constrained('discipleship_record_types')->restrictOnDelete();
        });

        $typeIdsBySlug = DB::table('discipleship_record_types')->pluck('id', 'slug');

        foreach ($typeIdsBySlug as $slug => $id) {
            DB::table('discipleship_records')->where('type', $slug)->update(['type_id' => $id]);
        }

        Schema::table('discipleship_records', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('discipleship_records', function (Blueprint $table) {
            $table->string('type')->nullable()->after('session_id');
        });

        $slugsById = DB::table('discipleship_record_types')->pluck('slug', 'id');

        foreach ($slugsById as $id => $slug) {
            DB::table('discipleship_records')->where('type_id', $id)->update(['type' => $slug]);
        }

        Schema::table('discipleship_records', function (Blueprint $table) {
            $table->dropForeign(['type_id']);
            $table->dropColumn('type_id');
        });
    }
};

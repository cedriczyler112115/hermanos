<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('music_sheets', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->after('file_size');
            $table->unsignedBigInteger('download_count')->default(0)->after('view_count');
        });

        Schema::create('music_sheet_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('music_sheet_id')->constrained('music_sheets')->cascadeOnDelete();
            $table->string('event_type', 20);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('identifier_hash', 64);
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['music_sheet_id', 'event_type', 'identifier_hash', 'created_at'], 'ms_events_dedupe_idx');
            $table->index(['event_type', 'created_at'], 'ms_events_stats_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_sheet_events');

        Schema::table('music_sheets', function (Blueprint $table) {
            $table->dropColumn(['view_count', 'download_count']);
        });
    }
};


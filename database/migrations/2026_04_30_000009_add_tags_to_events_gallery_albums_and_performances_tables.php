<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('tags', 255)->nullable()->after('about')->index();
        });

        Schema::table('gallery_albums', function (Blueprint $table) {
            $table->string('tags', 255)->nullable()->after('description')->index();
        });

        Schema::table('performances', function (Blueprint $table) {
            $table->string('tags', 255)->nullable()->after('youtube_url')->index();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('tags');
        });

        Schema::table('gallery_albums', function (Blueprint $table) {
            $table->dropColumn('tags');
        });

        Schema::table('performances', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
};


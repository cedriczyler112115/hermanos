<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_albums', function (Blueprint $table) {
            $table->id();
            $table->string('album_name', 255);
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_photo_path', 2048)->nullable();
            $table->string('cover_photo_thumb_path', 2048)->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();

            $table->index('album_name');
            $table->index('title');
        });

        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_album_id')->constrained('gallery_albums')->cascadeOnDelete();
            $table->string('photo_path', 2048);
            $table->string('photo_thumb_path', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->timestamps();

            $table->index('gallery_album_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');
        Schema::dropIfExists('gallery_albums');
    }
};


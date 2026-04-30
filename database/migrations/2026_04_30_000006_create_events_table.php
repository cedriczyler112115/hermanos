<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('schedule', 255)->nullable();
            $table->string('event_type', 100)->nullable();
            $table->string('location', 255)->nullable();
            $table->text('about')->nullable();
            $table->string('photo_path', 2048)->nullable();
            $table->string('photo_thumb_path', 2048)->nullable();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamps();

            $table->index('title');
            $table->index('event_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};


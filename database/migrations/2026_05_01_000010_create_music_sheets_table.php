<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('music_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('composer', 255);
            $table->string('file_path');
            $table->string('file_original_name', 255);
            $table->string('file_mime', 120);
            $table->unsignedBigInteger('file_size');
            $table->timestamps();

            $table->index('title');
            $table->index('composer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('music_sheets');
    }
};


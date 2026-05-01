<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slideshow_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploaded_by')->nullable()->index();
            $table->string('base_name', 64)->unique();

            $table->string('original_name')->nullable();
            $table->string('original_mime', 100)->nullable();
            $table->unsignedBigInteger('original_size')->nullable();
            $table->string('original_path');

            $table->string('desktop_path');
            $table->unsignedBigInteger('desktop_size')->nullable();
            $table->unsignedInteger('desktop_width')->nullable();
            $table->unsignedInteger('desktop_height')->nullable();

            $table->string('mobile_path');
            $table->unsignedBigInteger('mobile_size')->nullable();
            $table->string('thumb_path');
            $table->unsignedBigInteger('thumb_size')->nullable();

            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slideshow_images');
    }
};


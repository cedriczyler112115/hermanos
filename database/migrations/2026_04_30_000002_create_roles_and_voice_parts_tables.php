<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('voice_parts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('member_role', function (Blueprint $table) {
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->primary(['member_id', 'role_id']);
            $table->index('role_id');
        });

        Schema::create('member_voice_part', function (Blueprint $table) {
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('voice_part_id')->constrained('voice_parts')->cascadeOnDelete();
            $table->primary(['member_id', 'voice_part_id']);
            $table->index('voice_part_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_voice_part');
        Schema::dropIfExists('member_role');
        Schema::dropIfExists('voice_parts');
        Schema::dropIfExists('roles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('members')) {
            return;
        }

        if (! Schema::hasColumn('members', 'sort_order')) {
            return;
        }

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('members')) {
            return;
        }

        if (Schema::hasColumn('members', 'sort_order')) {
            return;
        }

        Schema::table('members', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->index();
        });
    }
};

<?php

use App\Models\Member;
use App\Models\Role;
use App\Models\VoicePart;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('roles') && ! Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }

        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                if (! Schema::hasColumn('members', 'role_id')) {
                    $table->foreignId('role_id')->nullable()->after('name')->constrained('roles')->nullOnDelete();
                }

                if (! Schema::hasColumn('members', 'voice_part_id')) {
                    $table->foreignId('voice_part_id')->nullable()->after('role_id')->constrained('voice_parts')->nullOnDelete();
                }
            });
        }

        if (! Schema::hasTable('members')) {
            return;
        }

        if (! Schema::hasTable('roles') || ! Schema::hasTable('voice_parts')) {
            return;
        }

        if (Schema::hasTable('member_role')) {
            DB::table('member_role')
                ->select('member_id', DB::raw('MIN(role_id) as role_id'))
                ->groupBy('member_id')
                ->get()
                ->each(function ($row) {
                    Member::query()
                        ->whereKey($row->member_id)
                        ->whereNull('role_id')
                        ->update(['role_id' => $row->role_id]);
                });
        }

        if (Schema::hasTable('member_voice_part')) {
            DB::table('member_voice_part')
                ->select('member_id', DB::raw('MIN(voice_part_id) as voice_part_id'))
                ->groupBy('member_id')
                ->get()
                ->each(function ($row) {
                    Member::query()
                        ->whereKey($row->member_id)
                        ->whereNull('voice_part_id')
                        ->update(['voice_part_id' => $row->voice_part_id]);
                });
        }

        if (Schema::hasColumn('members', 'role')) {
            Member::query()
                ->whereNull('role_id')
                ->whereNotNull('role')
                ->get(['id', 'role'])
                ->each(function (Member $member) {
                    $name = trim((string) $member->role);
                    if ($name === '') {
                        return;
                    }

                    $role = Role::query()->firstOrCreate(['name' => $name]);
                    $member->forceFill(['role_id' => $role->id])->saveQuietly();
                });
        }

        if (Schema::hasColumn('members', 'voice_part')) {
            Member::query()
                ->whereNull('voice_part_id')
                ->whereNotNull('voice_part')
                ->get(['id', 'voice_part'])
                ->each(function (Member $member) {
                    $name = trim((string) $member->voice_part);
                    if ($name === '') {
                        return;
                    }

                    $voicePart = VoicePart::query()->firstOrCreate(['name' => $name]);
                    $member->forceFill(['voice_part_id' => $voicePart->id])->saveQuietly();
                });
        }

        Schema::table('members', function (Blueprint $table) {
            if (Schema::hasColumn('members', 'role')) {
                $table->dropColumn('role');
            }
            if (Schema::hasColumn('members', 'voice_part')) {
                $table->dropColumn('voice_part');
            }
        });

        Schema::dropIfExists('member_voice_part');
        Schema::dropIfExists('member_role');
    }

    public function down(): void
    {
        if (! Schema::hasTable('members')) {
            return;
        }

        Schema::table('members', function (Blueprint $table) {
            if (! Schema::hasColumn('members', 'role')) {
                $table->string('role')->nullable()->after('name');
            }
            if (! Schema::hasColumn('members', 'voice_part')) {
                $table->string('voice_part')->nullable()->after('role');
            }

            if (Schema::hasColumn('members', 'voice_part_id')) {
                $table->dropConstrainedForeignId('voice_part_id');
            }
            if (Schema::hasColumn('members', 'role_id')) {
                $table->dropConstrainedForeignId('role_id');
            }
        });
    }
};

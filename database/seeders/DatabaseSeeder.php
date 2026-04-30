<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Schema::hasTable('voice_parts')) {
            foreach (['Soprano', 'Alto', 'Tenor', 'Bass'] as $name) {
                VoicePart::query()->firstOrCreate(['name' => $name]);
            }
        }

        if (Schema::hasTable('roles')) {
            $roles = [
                ['name' => 'Choir Member', 'description' => 'Active member of the choir.'],
                ['name' => 'Choir Leader', 'description' => 'Leads rehearsals and coordinates choir participation.'],
                ['name' => 'Coordinator', 'description' => 'Coordinates schedules, events, and communications.'],
            ];

            foreach ($roles as $role) {
                Role::query()->updateOrCreate(
                    ['name' => $role['name']],
                    ['description' => $role['description']],
                );
            }
        }

        $adminEmail = env('ADMIN_EMAIL');
        $adminPassword = env('ADMIN_PASSWORD');

        if (is_string($adminEmail) && $adminEmail !== '' && is_string($adminPassword) && $adminPassword !== '') {
            $isAdmin = Schema::hasColumn('users', 'is_admin');

            User::query()->firstOrCreate(
                ['email' => $adminEmail],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make($adminPassword),
                    ...($isAdmin ? ['is_admin' => true] : []),
                ],
            );
        }
    }
}

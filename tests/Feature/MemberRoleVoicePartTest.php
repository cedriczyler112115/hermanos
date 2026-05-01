<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemberRoleVoicePartTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_member_create_page_loads_role_and_voice_part_options(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        Role::create(['name' => 'Choir Member', 'description' => 'Member']);
        VoicePart::create(['name' => 'Soprano']);

        $response = $this->actingAs($user)->get('/admin/members/create');

        $response->assertOk();
        $response->assertSee('Choir Member');
        $response->assertSee('Soprano');
    }

    public function test_admin_can_create_member_with_valid_role_id_and_voice_part_id(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $role = Role::create(['name' => 'Choir Member', 'description' => 'Member']);
        $voicePart = VoicePart::create(['name' => 'Soprano']);

        $response = $this->actingAs($user)->post(route('admin.members.store'), [
            'name' => 'Juan Dela Cruz',
            'address' => 'Sample Address',
            'hobbies' => 'Singing',
            'description' => 'A dedicated choir member.',
            'role_id' => $role->id,
            'voice_part_id' => $voicePart->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('members', [
            'name' => 'Juan Dela Cruz',
            'role_id' => $role->id,
            'voice_part_id' => $voicePart->id,
        ]);
    }

    public function test_admin_member_store_rejects_invalid_role_id_and_voice_part_id(): void
    {
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)->post(route('admin.members.store'), [
            'name' => 'Invalid',
            'role_id' => 999999,
            'voice_part_id' => 999999,
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors(['role_id', 'voice_part_id']);
    }

    public function test_deleting_role_or_voice_part_sets_member_fk_to_null(): void
    {
        $role = Role::create(['name' => 'Choir Member', 'description' => 'Member']);
        $voicePart = VoicePart::create(['name' => 'Soprano']);

        $member = Member::create([
            'name' => 'Test Member',
            'role_id' => $role->id,
            'voice_part_id' => $voicePart->id,
            'is_active' => true,
        ]);

        $role->delete();
        $member->refresh();
        $this->assertNull($member->role_id);
        $this->assertSame($voicePart->id, $member->voice_part_id);

        $voicePart->delete();
        $member->refresh();
        $this->assertNull($member->voice_part_id);
    }

    public function test_public_members_page_groups_by_voice_part_and_sorts_by_last_name(): void
    {
        $soprano = VoicePart::create(['name' => 'Soprano']);
        $alto = VoicePart::create(['name' => 'Alto']);

        Member::create(['name' => 'Ana Zed', 'voice_part_id' => $soprano->id, 'is_active' => true]);
        Member::create(['name' => 'Bob Alpha', 'voice_part_id' => $soprano->id, 'is_active' => true]);
        Member::create(['name' => 'Carl Beta', 'voice_part_id' => $alto->id, 'is_active' => true]);
        Member::create(['name' => 'Dana Omega', 'voice_part_id' => null, 'is_active' => true]);

        $response = $this->get(route('site.members'));
        $response->assertOk();

        $response->assertSeeInOrder([
            'Soprano',
            'Bob Alpha',
            'Ana Zed',
            'Alto',
            'Carl Beta',
            'Unassigned',
            'Dana Omega',
        ]);
    }

    public function test_public_members_page_shows_service_duration_and_details_in_modal(): void
    {
        $this->travelTo(Carbon::parse('2026-05-01'));

        $role = Role::create(['name' => 'Choir Member', 'description' => 'Member']);
        $voicePart = VoicePart::create(['name' => 'Tenor']);

        Member::create([
            'name' => 'John Example',
            'email_address' => 'john@example.com',
            'start_date' => '2020-05-01',
            'address' => 'Sample Address',
            'hobbies' => 'Singing',
            'bio' => 'Bio text',
            'description' => 'Description text',
            'role_id' => $role->id,
            'voice_part_id' => $voicePart->id,
            'is_active' => true,
        ]);

        Member::create([
            'name' => 'Jane Sample',
            'start_date' => '2025-01-26',
            'role_id' => $role->id,
            'voice_part_id' => $voicePart->id,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.members'));
        $response->assertOk();

        $response->assertSee('6 years in service');
        $response->assertSee('1 year 3 months and 5 days in service');
        $response->assertSee('Email address');
        $response->assertSee('john@example.com');
        $response->assertSee('Start date');
        $response->assertSee('May 1, 2020');
        $response->assertSee('Bio');
        $response->assertSee('Bio text');
        $response->assertSee('Description');
        $response->assertSee('Description text');
        $response->assertSee('Role');
        $response->assertSee('Choir Member');
        $response->assertSee('Voice part');
        $response->assertSee('Tenor');
    }

    public function test_officers_page_shows_roles_1_to_6_and_includes_choir_member_section(): void
    {
        $now = now();
        DB::table('roles')->insert(
            collect(range(1, 6))->map(function ($i) use ($now) {
                return [
                    'id' => $i,
                    'name' => 'Officer Role '.$i,
                    'description' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            })->all()
        );

        DB::table('roles')->insert([
            [
                'id' => 7,
                'name' => 'Organist',
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'name' => 'Choir Member',
                'description' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        Member::create([
            'name' => 'Officer One',
            'role_id' => 1,
            'is_active' => true,
        ]);

        Member::create([
            'name' => 'Officer Six',
            'role_id' => 6,
            'is_active' => true,
        ]);

        Member::create([
            'name' => 'The Organist',
            'role_id' => 7,
            'is_active' => true,
        ]);

        Member::create([
            'name' => 'Regular Member',
            'role_id' => 8,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.officers'));
        $response->assertOk();

        $response->assertSee('Organizational Structure');
        $response->assertSee('Officer Role 1');
        $response->assertSee('Officer One');
        $response->assertSee('Officer Role 6');
        $response->assertSee('Officer Six');
        $response->assertDontSee('Officer Role 7');
        $response->assertDontSee('Officer Seven');
        $response->assertDontSee('Organist');
        $response->assertDontSee('The Organist');
        $response->assertSee('CHOIR MEMBER');
        $response->assertSee('Regular Member');
    }

    public function test_board_of_directors_page_filters_by_is_bod(): void
    {
        Member::create([
            'name' => 'Director One',
            'is_bod' => true,
            'is_active' => true,
        ]);

        Member::create([
            'name' => 'Not Director',
            'is_bod' => false,
            'is_active' => true,
        ]);

        $response = $this->get(route('site.board_of_directors'));
        $response->assertOk();

        $response->assertSee('The Board of Directors');
        $response->assertSee('Director One');
        $response->assertDontSee('Not Director');
    }
}

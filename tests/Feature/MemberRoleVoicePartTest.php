<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Role;
use App\Models\VoicePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index()
    {
        $members = Member::query()
            ->with(['role', 'voicePart'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.members.index', [
            'members' => $members,
        ]);
    }

    public function create()
    {
        return view('admin.members.create', [
            'member' => new Member(),
            'roles' => Role::query()->orderBy('id', 'asc')->get(['id', 'name']),
            'voiceParts' => VoicePart::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedMember($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('members', 'public');
        }

        $member = Member::create($validated);

        return redirect()
            ->route('admin.members.edit', $member)
            ->with('status', 'Member created.');
    }

    public function edit(Member $member)
    {
        $member->load(['role', 'voicePart']);

        return view('admin.members.edit', [
            'member' => $member,
            'roles' => Role::query()->orderBy('name')->get(['id', 'name']),
            'voiceParts' => VoicePart::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Member $member)
    {
        $validated = $this->validatedMember($request);

        if ($request->hasFile('photo')) {
            if ($member->photo_path) {
                Storage::disk('public')->delete($member->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('members', 'public');
        }

        $member->update($validated);

        return redirect()
            ->route('admin.members.edit', $member)
            ->with('status', 'Member updated.');
    }

    public function destroy(Member $member)
    {
        if ($member->photo_path) {
            Storage::disk('public')->delete($member->photo_path);
        }

        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('status', 'Member deleted.');
    }

    private function validatedMember(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:500'],
            'hobbies' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:5000'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'voice_part_id' => ['nullable', 'integer', 'exists:voice_parts,id'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        unset($validated['photo']);

        return $validated;
    }
}

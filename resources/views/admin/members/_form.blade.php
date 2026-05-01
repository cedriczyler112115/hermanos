<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-slate-800">Full name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $member->name) }}" required class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('name')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="email_address" class="block text-sm font-medium text-slate-800">Email address</label>
        <input id="email_address" name="email_address" type="email" value="{{ old('email_address', $member->email_address) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('email_address')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="start_date" class="block text-sm font-medium text-slate-800">Start date</label>
        <input id="start_date" name="start_date" type="date" value="{{ old('start_date', optional($member->start_date)->format('Y-m-d')) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('start_date')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="address" class="block text-sm font-medium text-slate-800">Address</label>
        <input id="address" name="address" type="text" value="{{ old('address', $member->address) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('address')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="hobbies" class="block text-sm font-medium text-slate-800">Hobbies</label>
        <input id="hobbies" name="hobbies" type="text" value="{{ old('hobbies', $member->hobbies) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('hobbies')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="role_id" class="block text-sm font-medium text-slate-800">Role</label>
        <select id="role_id" name="role_id" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">
            <option value="">— Select —</option>
            @php($rolesList = ($roles ?? collect()))
            @php($rolesBeforeChoir = $rolesList->filter(fn ($r) => (int) $r->id <= 6))
            @php($rolesChoir = $rolesList->filter(fn ($r) => (int) $r->id > 6))

            @foreach ($rolesBeforeChoir as $role)
                <option value="{{ $role->id }}" {{ (string) old('role_id', $member->role_id) === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach

            @if ($rolesChoir->count() > 0)
                <optgroup label="CHOIR MEMBERS">
                    @foreach ($rolesChoir as $role)
                        <option value="{{ $role->id }}" {{ (string) old('role_id', $member->role_id) === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </optgroup>
            @endif
        </select>
        @error('role_id')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="voice_part_id" class="block text-sm font-medium text-slate-800">Voice part</label>
        <select id="voice_part_id" name="voice_part_id" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">
            <option value="">— Select —</option>
            @foreach (($voiceParts ?? collect()) as $voicePart)
                <option value="{{ $voicePart->id }}" {{ (string) old('voice_part_id', $member->voice_part_id) === (string) $voicePart->id ? 'selected' : '' }}>
                    {{ $voicePart->name }}
                </option>
            @endforeach
        </select>
        @error('voice_part_id')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="block text-sm font-medium text-slate-800">Description</label>
        <textarea id="description" name="description" rows="6" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0">{{ old('description', $member->description ?? $member->bio) }}</textarea>
        @error('description')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="facebook_url" class="block text-sm font-medium text-slate-800">Facebook URL</label>
        <input id="facebook_url" name="facebook_url" type="url" value="{{ old('facebook_url', $member->facebook_url) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('facebook_url')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div>
        <label for="youtube_url" class="block text-sm font-medium text-slate-800">YouTube URL</label>
        <input id="youtube_url" name="youtube_url" type="url" value="{{ old('youtube_url', $member->youtube_url) }}" class="mt-1 w-full rounded-xl border border-[var(--color-border)] bg-white px-3 py-3 text-slate-900 shadow-sm focus:border-[var(--color-primary)] focus:ring-0" />
        @error('youtube_url')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $member->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-0" />
        <label for="is_active" class="text-sm font-medium text-slate-800">Show publicly</label>
        @error('is_active')
            <div class="text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
        <input id="is_bod" name="is_bod" type="checkbox" value="1" {{ old('is_bod', $member->is_bod ?? false) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-[var(--color-primary)] focus:ring-0" />
        <label for="is_bod" class="text-sm font-medium text-slate-800">Board of Directors</label>
        @error('is_bod')
            <div class="text-sm text-red-700">{{ $message }}</div>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="photo" class="block text-sm font-medium text-slate-800">Photo (optional)</label>
        <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-lg file:border file:border-[var(--color-border)] file:bg-[var(--color-surface)] file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-900 hover:file:bg-[var(--color-muted)]" />
        @error('photo')
            <div class="mt-1 text-sm text-red-700">{{ $message }}</div>
        @enderror

        @if ($member->photo_path)
            <div class="mt-3 flex items-center gap-3">
                <img src="{{ asset('storage/' . $member->photo_path) }}" alt="{{ $member->name }}" class="h-16 w-16 rounded-full object-cover ring-1 ring-[var(--color-border)]" />
                <div class="text-sm text-slate-700">Current photo</div>
            </div>
        @endif
    </div>
</div>

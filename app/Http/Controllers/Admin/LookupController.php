<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\VoicePart;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function rolesIndex()
    {
        return response()->json([
            'data' => Role::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function rolesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $name = trim($validated['name']);

        $role = Role::query()->firstOrCreate(
            ['name' => $name],
            ['description' => $validated['description'] ?? null],
        );

        if (array_key_exists('description', $validated) && $validated['description'] !== null) {
            $role->forceFill(['description' => $validated['description']])->save();
        }

        return response()->json([
            'data' => $role->only(['id', 'name']),
        ]);
    }

    public function voicePartsIndex()
    {
        return response()->json([
            'data' => VoicePart::query()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function voicePartsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $voicePart = VoicePart::query()->firstOrCreate([
            'name' => trim($validated['name']),
        ]);

        return response()->json([
            'data' => $voicePart->only(['id', 'name']),
        ]);
    }
}

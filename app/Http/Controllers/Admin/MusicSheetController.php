<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MusicSheetController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $sheets = MusicSheet::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';

                $query->where(function ($inner) use ($like) {
                    $inner
                        ->where('title', 'like', $like)
                        ->orWhere('composer', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.music-sheets.index', [
            'sheets' => $sheets,
            'q' => $q,
        ]);
    }

    public function analytics(Request $request)
    {
        $driver = DB::connection()->getDriverName();

        $dayExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m-%d', created_at)"
            : 'DATE(created_at)';

        $weekExpr = $driver === 'sqlite'
            ? "strftime('%Y-W%W', created_at)"
            : "DATE_FORMAT(created_at, '%x-W%v')";

        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $daily = DB::table('music_sheet_events')
            ->selectRaw($dayExpr.' as bucket, event_type, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('bucket', 'event_type')
            ->orderBy('bucket', 'asc')
            ->get();

        $weekly = DB::table('music_sheet_events')
            ->selectRaw($weekExpr.' as bucket, event_type, COUNT(*) as total')
            ->where('created_at', '>=', now()->subWeeks(12))
            ->groupBy('bucket', 'event_type')
            ->orderBy('bucket', 'asc')
            ->get();

        $monthly = DB::table('music_sheet_events')
            ->selectRaw($monthExpr.' as bucket, event_type, COUNT(*) as total')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('bucket', 'event_type')
            ->orderBy('bucket', 'asc')
            ->get();

        $rollup = function ($rows) {
            $map = [];
            foreach ($rows as $row) {
                $bucket = (string) ($row->bucket ?? '');
                $type = (string) ($row->event_type ?? '');
                $count = (int) ($row->total ?? 0);
                if ($bucket === '') continue;
                $map[$bucket] ??= ['view' => 0, 'download' => 0];
                if ($type === 'view') $map[$bucket]['view'] = $count;
                if ($type === 'download') $map[$bucket]['download'] = $count;
            }
            ksort($map);
            return $map;
        };

        return view('admin.music-sheets.analytics', [
            'daily' => $rollup($daily),
            'weekly' => $rollup($weekly),
            'monthly' => $rollup($monthly),
        ]);
    }

    public function create()
    {
        return view('admin.music-sheets.create', [
            'sheet' => new MusicSheet(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatedSheet($request, true);

        try {
            $sheet = DB::transaction(function () use ($request, $validated) {
                $file = $request->file('file');
                if (! $file) {
                    throw new \RuntimeException('Missing file.');
                }

                $ext = strtolower((string) $file->getClientOriginalExtension());
                $filename = (string) Str::uuid().($ext !== '' ? '.'.$ext : '');

                $path = $file->storeAs('music-sheets', $filename, 'public');
                if (! is_string($path) || $path === '') {
                    throw new \RuntimeException('Failed to store file.');
                }

                $validated['file_path'] = $path;
                $validated['file_original_name'] = (string) $file->getClientOriginalName();
                $validated['file_mime'] = (string) ($file->getClientMimeType() ?: $file->getMimeType() ?: '');
                $validated['file_size'] = (int) $file->getSize();

                return MusicSheet::query()->create($validated);
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['file' => 'Failed to upload the music sheet file. Please try again.']);
        }

        Log::info('admin.music_sheets.create', [
            'user_id' => $request->user()?->id,
            'music_sheet_id' => $sheet->id,
        ]);

        return redirect()
            ->route('admin.music_sheets.edit', $sheet)
            ->with('status', 'Music sheet created.');
    }

    public function edit(MusicSheet $music_sheet)
    {
        return view('admin.music-sheets.edit', [
            'sheet' => $music_sheet,
        ]);
    }

    public function update(Request $request, MusicSheet $music_sheet)
    {
        $validated = $this->validatedSheet($request, false);

        $oldPath = $music_sheet->file_path;

        try {
            DB::transaction(function () use ($request, $music_sheet, $validated, $oldPath) {
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    if (! $file) {
                        throw new \RuntimeException('Missing file.');
                    }

                    $ext = strtolower((string) $file->getClientOriginalExtension());
                    $filename = (string) Str::uuid().($ext !== '' ? '.'.$ext : '');

                    $path = $file->storeAs('music-sheets', $filename, 'public');
                    if (! is_string($path) || $path === '') {
                        throw new \RuntimeException('Failed to store file.');
                    }

                    $validated['file_path'] = $path;
                    $validated['file_original_name'] = (string) $file->getClientOriginalName();
                    $validated['file_mime'] = (string) ($file->getClientMimeType() ?: $file->getMimeType() ?: '');
                    $validated['file_size'] = (int) $file->getSize();
                }

                $music_sheet->update($validated);

                if ($request->hasFile('file') && is_string($oldPath) && $oldPath !== '') {
                    Storage::disk('public')->delete($oldPath);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors(['file' => 'Failed to update the music sheet file. Please try again.']);
        }

        Log::info('admin.music_sheets.update', [
            'user_id' => $request->user()?->id,
            'music_sheet_id' => $music_sheet->id,
        ]);

        return redirect()
            ->route('admin.music_sheets.edit', $music_sheet)
            ->with('status', 'Music sheet updated.');
    }

    public function destroy(Request $request, MusicSheet $music_sheet)
    {
        $path = $music_sheet->file_path;

        $music_sheet->delete();

        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }

        Log::info('admin.music_sheets.delete', [
            'user_id' => $request->user()?->id,
            'music_sheet_id' => $music_sheet->id,
        ]);

        return redirect()
            ->route('admin.music_sheets.index')
            ->with('status', 'Music sheet deleted.');
    }

    private function validatedSheet(Request $request, bool $fileRequired): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'composer' => ['required', 'string', 'max:255'],
            'file' => [$fileRequired ? 'required' : 'nullable', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,gif'],
        ];

        $validated = $request->validate($rules);

        unset($validated['file']);

        return $validated;
    }
}

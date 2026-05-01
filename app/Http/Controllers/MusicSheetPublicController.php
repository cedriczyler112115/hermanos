<?php

namespace App\Http\Controllers;

use App\Models\MusicSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MusicSheetPublicController extends Controller
{
    public function trackView(Request $request, MusicSheet $music_sheet)
    {
        return $this->trackEvent($request, $music_sheet, 'view');
    }

    public function downloadIntent(Request $request, MusicSheet $music_sheet)
    {
        $result = $this->trackEvent($request, $music_sheet, 'download', true);

        $baseUrl = (string) $request->getBaseUrl();
        $baseUrl = $baseUrl !== '' && $baseUrl !== '/' ? rtrim($baseUrl, '/') : '';
        $downloadUrl = $baseUrl.route('site.music_sheets.download', $music_sheet, false);

        $payload = $result->getData(true);
        $payload['download_url'] = $downloadUrl;

        return response()->json($payload);
    }

    public function file(Request $request, MusicSheet $music_sheet)
    {
        $path = $this->safePublicMusicSheetPath($music_sheet);
        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $name = (string) ($music_sheet->file_original_name ?: basename($path));
        $mime = (string) ($music_sheet->file_mime ?: '');

        $headers = [
            'Content-Disposition' => 'inline; filename="'.addcslashes($name, '"\\').'"',
        ];
        if ($mime !== '') {
            $headers['Content-Type'] = $mime;
        }

        return Storage::disk('public')->response($path, $name, $headers);
    }

    public function download(Request $request, MusicSheet $music_sheet)
    {
        $this->trackEvent($request, $music_sheet, 'download', false);

        return $this->streamFile($music_sheet);
    }

    public function downloadFile(Request $request, MusicSheet $music_sheet)
    {
        return $this->streamFile($music_sheet);
    }

    private function streamFile(MusicSheet $music_sheet)
    {
        $path = $this->safePublicMusicSheetPath($music_sheet);
        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $name = (string) ($music_sheet->file_original_name ?: basename($path));

        return Storage::disk('public')->download($path, $name);
    }

    private function safePublicMusicSheetPath(MusicSheet $music_sheet): string
    {
        $path = ltrim((string) $music_sheet->file_path, '/\\');
        if ($path === '') {
            return '';
        }

        $path = str_replace('\\', '/', $path);
        $needle = 'music-sheets/';
        $pos = strpos($path, $needle);
        if ($pos === false) {
            $onlyName = basename($path);
            if ($onlyName === '' || $onlyName === '.' || $onlyName === '..') {
                return '';
            }
            $path = $needle.$onlyName;
        } elseif ($pos !== 0) {
            $path = substr($path, $pos);
        }

        if (! str_starts_with($path, $needle)) {
            return '';
        }

        if (str_contains($path, '../') || str_contains($path, '..\\') || str_contains($path, "\0")) {
            return '';
        }

        return $path;
    }

    private function trackEvent(Request $request, MusicSheet $music_sheet, string $eventType, bool $jsonOnly = true)
    {
        $eventType = strtolower(trim($eventType));
        if (! in_array($eventType, ['view', 'download'], true)) {
            abort(400);
        }

        $now = Carbon::now();
        $since = $now->copy()->subHours(24);

        $user = $request->user();
        $userId = $user?->id;

        $ip = (string) ($request->ip() ?: '');
        $ua = (string) substr((string) $request->userAgent(), 0, 255);
        $identity = $userId ? ('user:'.$userId) : ('guest:'.$ip.'|'.$ua);
        $identifierHash = hash('sha256', $identity);

        $didIncrement = false;

        DB::transaction(function () use ($music_sheet, $eventType, $identifierHash, $since, $now, $userId, $ip, $ua, &$didIncrement) {
            $exists = DB::table('music_sheet_events')
                ->where('music_sheet_id', $music_sheet->id)
                ->where('event_type', $eventType)
                ->where('identifier_hash', $identifierHash)
                ->where('created_at', '>=', $since)
                ->exists();

            if ($exists) {
                return;
            }

            DB::table('music_sheet_events')->insert([
                'music_sheet_id' => $music_sheet->id,
                'event_type' => $eventType,
                'user_id' => $userId,
                'identifier_hash' => $identifierHash,
                'ip' => $ip !== '' ? $ip : null,
                'user_agent' => $ua !== '' ? $ua : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $column = $eventType === 'view' ? 'view_count' : 'download_count';
            DB::table('music_sheets')->where('id', $music_sheet->id)->increment($column);

            $didIncrement = true;
        });

        $fresh = MusicSheet::query()->find($music_sheet->id);
        $viewCount = (int) ($fresh?->view_count ?? $music_sheet->view_count ?? 0);
        $downloadCount = (int) ($fresh?->download_count ?? $music_sheet->download_count ?? 0);

        return response()->json([
            'music_sheet_id' => $music_sheet->id,
            'event_type' => $eventType,
            'view_count' => $viewCount,
            'download_count' => $downloadCount,
            'deduped' => ! $didIncrement,
        ]);
    }
}

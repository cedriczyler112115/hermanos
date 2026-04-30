<?php

namespace App\Http\Controllers;

use App\Models\MusicSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MusicSheetPublicController extends Controller
{
    public function trackView(Request $request, MusicSheet $music_sheet)
    {
        return $this->trackEvent($request, $music_sheet, 'view');
    }

    public function downloadIntent(Request $request, MusicSheet $music_sheet)
    {
        $result = $this->trackEvent($request, $music_sheet, 'download', true);

        $signedUrl = URL::temporarySignedRoute(
            'site.music_sheets.download_file',
            now()->addMinutes(10),
            ['music_sheet' => $music_sheet->id],
            false,
        );
        $baseUrl = (string) $request->getBaseUrl();
        if ($baseUrl !== '' && $baseUrl !== '/' && str_starts_with($signedUrl, '/')) {
            $signedUrl = rtrim($baseUrl, '/').$signedUrl;
        }

        $payload = $result->getData(true);
        $payload['download_url'] = $signedUrl;

        return response()->json($payload);
    }

    public function download(Request $request, MusicSheet $music_sheet)
    {
        $this->trackEvent($request, $music_sheet, 'download', false);

        return $this->streamFile($music_sheet);
    }

    public function downloadFile(Request $request, MusicSheet $music_sheet)
    {
        if (! $request->hasValidSignature(false)) {
            abort(403);
        }

        return $this->streamFile($music_sheet);
    }

    private function streamFile(MusicSheet $music_sheet)
    {
        $path = (string) $music_sheet->file_path;
        if ($path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $name = (string) ($music_sheet->file_original_name ?: basename($path));

        return Storage::disk('public')->download($path, $name);
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

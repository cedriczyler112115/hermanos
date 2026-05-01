<?php

namespace Tests\Feature;

use App\Models\MusicSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MusicSheetTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_tracking_is_deduped_within_24_hours(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('music-sheets/test.pdf', 'pdf');

        $sheet = MusicSheet::query()->create([
            'title' => 'Test',
            'composer' => 'Composer',
            'file_path' => 'music-sheets/test.pdf',
            'file_original_name' => 'test.pdf',
            'file_mime' => 'application/pdf',
            'file_size' => 3,
            'view_count' => 0,
            'download_count' => 0,
        ]);

        Carbon::setTestNow('2026-05-01 10:00:00');

        $r1 = $this->post(route('site.music_sheets.track_view', $sheet), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
        $r1->assertOk();
        $r1->assertJsonPath('view_count', 1);
        $this->assertSame(1, $sheet->fresh()->view_count);

        $r2 = $this->post(route('site.music_sheets.track_view', $sheet), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
        $r2->assertOk();
        $r2->assertJsonPath('view_count', 1);
        $this->assertSame(1, $sheet->fresh()->view_count);

        Carbon::setTestNow('2026-05-02 11:00:00');
        $r3 = $this->post(route('site.music_sheets.track_view', $sheet), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
        $r3->assertOk();
        $r3->assertJsonPath('view_count', 2);
        $this->assertSame(2, $sheet->fresh()->view_count);
    }

    public function test_download_intent_increments_and_returns_signed_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('music-sheets/test.pdf', 'pdf');

        $sheet = MusicSheet::query()->create([
            'title' => 'Test',
            'composer' => 'Composer',
            'file_path' => 'music-sheets/test.pdf',
            'file_original_name' => 'test.pdf',
            'file_mime' => 'application/pdf',
            'file_size' => 3,
            'view_count' => 0,
            'download_count' => 0,
        ]);

        Carbon::setTestNow('2026-05-01 10:00:00');

        $intent = $this->post(route('site.music_sheets.download_intent', $sheet), [], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'application/json',
        ]);
        $intent->assertOk();
        $intent->assertJsonPath('download_count', 1);
        $intent->assertJsonStructure(['download_url']);

        $sheet->refresh();
        $this->assertSame(1, $sheet->download_count);

        $signedUrl = $intent->json('download_url');
        $this->assertIsString($signedUrl);

        $download = $this->get($signedUrl);
        $download->assertOk();
    }

    public function test_download_and_file_routes_only_allow_music_sheets_folder(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('other/test.pdf', 'pdf');

        $sheet = MusicSheet::query()->create([
            'title' => 'Test',
            'composer' => 'Composer',
            'file_path' => 'other/test.pdf',
            'file_original_name' => 'test.pdf',
            'file_mime' => 'application/pdf',
            'file_size' => 3,
            'view_count' => 0,
            'download_count' => 0,
        ]);

        $this->get(route('site.music_sheets.download', $sheet))->assertNotFound();
        $this->get(route('site.music_sheets.file', $sheet))->assertNotFound();
    }

    public function test_download_and_file_routes_accept_legacy_prefixed_paths(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('music-sheets/test.pdf', 'pdf');

        $sheet = MusicSheet::query()->create([
            'title' => 'Test',
            'composer' => 'Composer',
            'file_path' => 'public/music-sheets/test.pdf',
            'file_original_name' => 'test.pdf',
            'file_mime' => 'application/pdf',
            'file_size' => 3,
            'view_count' => 0,
            'download_count' => 0,
        ]);

        $this->get(route('site.music_sheets.file', $sheet))->assertOk();
        $this->get(route('site.music_sheets.download', $sheet))->assertOk();
    }
}

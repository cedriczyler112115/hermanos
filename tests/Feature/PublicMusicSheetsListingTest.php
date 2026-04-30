<?php

namespace Tests\Feature;

use App\Models\MusicSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMusicSheetsListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_music_sheets_page_search_and_per_page_work(): void
    {
        foreach (range(1, 27) as $i) {
            MusicSheet::query()->create([
                'title' => $i === 4 ? 'Special Hymn Sheet' : "Sheet {$i}",
                'composer' => $i === 4 ? 'Composer Special' : "Composer {$i}",
                'file_path' => "music-sheets/{$i}.pdf",
                'file_original_name' => "{$i}.pdf",
                'file_mime' => 'application/pdf',
                'file_size' => 12345,
                'view_count' => 0,
                'download_count' => 0,
            ]);
        }

        $response = $this->get('/music-sheets?per_page=10&page=2');
        $response->assertOk();

        $response = $this->get('/music-sheets?q=special&per_page=10&page=1');
        $response->assertOk();
        $response->assertSee('Special Hymn Sheet');

        $json = $this->get('/music-sheets?q=special&per_page=10&page=1', [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $json->assertOk();
        $json->assertJsonStructure(['html']);
    }

    public function test_music_sheets_all_option_returns_all_records(): void
    {
        foreach (range(1, 15) as $i) {
            MusicSheet::query()->create([
                'title' => "All Sheet {$i}",
                'composer' => "Composer {$i}",
                'file_path' => "music-sheets/all-{$i}.pdf",
                'file_original_name' => "all-{$i}.pdf",
                'file_mime' => 'application/pdf',
                'file_size' => 12345,
                'view_count' => 0,
                'download_count' => 0,
            ]);
        }

        $response = $this->get('/music-sheets?per_page=all');
        $response->assertOk();
        $response->assertSee('All Sheet 1');
        $response->assertSee('All Sheet 15');
    }
}

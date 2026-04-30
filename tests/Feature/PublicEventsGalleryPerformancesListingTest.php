<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\Performance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicEventsGalleryPerformancesListingTest extends TestCase
{
    use RefreshDatabase;

    public function testEventsPaginationPerPageAndSearchWork(): void
    {
        Carbon::setTestNow('2026-04-30 12:00:00');

        foreach (range(1, 30) as $i) {
            Event::query()->create([
                'title' => $i === 5 ? 'Special Fiesta Event' : "Event {$i}",
                'about' => $i === 5 ? 'A special celebration' : 'General',
                'tags' => $i === 5 ? 'fiesta, special' : null,
                'is_published' => true,
                'created_at' => Carbon::now()->subMinutes($i),
                'updated_at' => Carbon::now()->subMinutes($i),
            ]);
        }

        Event::query()->create([
            'title' => 'Hidden Event',
            'about' => 'Should not appear',
            'tags' => 'special',
            'is_published' => false,
        ]);

        $response = $this->get('/events?per_page=12&page=1');
        $response->assertOk();

        $response = $this->get('/events?q=special&per_page=12&page=1');
        $response->assertOk();
        $response->assertSee('Special Fiesta Event');
        $response->assertDontSee('Hidden Event');

        $json = $this->get('/events?q=special&per_page=12&page=1', [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $json->assertOk();
        $json->assertJsonStructure(['html']);
        $this->assertIsString($json->json('html'));
    }

    public function testGalleryPaginationPerPageAndSearchWork(): void
    {
        foreach (range(1, 25) as $i) {
            GalleryAlbum::query()->create([
                'album_name' => $i === 3 ? 'Choir Outreach Album' : "Album {$i}",
                'title' => $i === 3 ? 'Outreach' : null,
                'description' => $i === 3 ? 'Community outreach photos' : null,
                'tags' => $i === 3 ? 'outreach, charity' : null,
                'is_published' => true,
            ]);
        }

        $response = $this->get('/gallery?per_page=12&page=2');
        $response->assertOk();

        $response = $this->get('/gallery?q=outreach&per_page=12&page=1');
        $response->assertOk();
        $response->assertSee('Choir Outreach Album');

        $json = $this->get('/gallery?q=outreach&per_page=12&page=1', [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $json->assertOk();
        $json->assertJsonStructure(['html']);
    }

    public function testPerformancesPaginationPerPageAndSearchWork(): void
    {
        foreach (range(1, 40) as $i) {
            Performance::query()->create([
                'title' => $i === 7 ? 'Mass Recording Special' : "Performance {$i}",
                'description' => $i === 7 ? 'Special performance description' : 'General',
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'tags' => $i === 7 ? 'mass, special' : null,
            ]);
        }

        $response = $this->get('/performances?per_page=12&page=1');
        $response->assertOk();

        $response = $this->get('/performances?q=special&per_page=12&page=1');
        $response->assertOk();
        $response->assertSee('Mass Recording Special');

        $json = $this->get('/performances?q=special&per_page=12&page=1', [
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest',
        ]);
        $json->assertOk();
        $json->assertJsonStructure(['html']);
    }
}


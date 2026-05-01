<?php

namespace Tests\Feature;

use App\Models\SlideshowImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSlideshowMarkupTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contains_accessible_slideshow_controls(): void
    {
        SlideshowImage::query()->create([
            'base_name' => 'test-slide-1',
            'original_path' => 'slideshow/test-slide-1_original.jpg',
            'desktop_path' => 'slideshow/test-slide-1.webp',
            'mobile_path' => 'slideshow/mobile/test-slide-1.webp',
            'thumb_path' => 'slideshow/thumb/test-slide-1.webp',
        ]);
        SlideshowImage::query()->create([
            'base_name' => 'test-slide-2',
            'original_path' => 'slideshow/test-slide-2_original.jpg',
            'desktop_path' => 'slideshow/test-slide-2.webp',
            'mobile_path' => 'slideshow/mobile/test-slide-2.webp',
            'thumb_path' => 'slideshow/thumb/test-slide-2.webp',
        ]);

        $response = $this->get(route('site.home'));

        $response->assertOk();
        $response->assertSee('data-home-slideshow', false);
        $response->assertSee('data-home-slideshow-stage', false);
        $response->assertSee('data-prev', false);
        $response->assertSee('data-next', false);
        $response->assertSee('aria-label="Previous image"', false);
        $response->assertSee('aria-label="Next image"', false);
    }
}

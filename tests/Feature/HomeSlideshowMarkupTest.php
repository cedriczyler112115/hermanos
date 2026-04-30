<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeSlideshowMarkupTest extends TestCase
{
    public function test_homepage_contains_accessible_slideshow_controls(): void
    {
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


<?php

namespace Tests\Unit;

use App\Support\PublicListing;
use PHPUnit\Framework\TestCase;

class PublicListingTest extends TestCase
{
    public function testPerPageDefaultsTo12WhenInvalid(): void
    {
        $this->assertSame(12, PublicListing::perPage(null));
        $this->assertSame(12, PublicListing::perPage('0'));
        $this->assertSame(12, PublicListing::perPage('999'));
        $this->assertSame(12, PublicListing::perPage('abc'));
    }

    public function testPerPageAcceptsAllowedValues(): void
    {
        $this->assertSame(12, PublicListing::perPage(12));
        $this->assertSame(24, PublicListing::perPage('24'));
        $this->assertSame(48, PublicListing::perPage(48));
    }

    public function testQueryStringTrimsCollapsesWhitespaceAndLimitsLength(): void
    {
        $this->assertSame('', PublicListing::queryString('   '));
        $this->assertSame('hello world', PublicListing::queryString(" \nhello\t world  "));

        $long = str_repeat('a', 200);
        $this->assertSame(120, mb_strlen(PublicListing::queryString($long)));
    }
}


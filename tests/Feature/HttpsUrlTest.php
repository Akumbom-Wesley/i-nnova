<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Behind a web server that terminates TLS, PHP sees plain HTTP unless the
 * forwarded headers are trusted. Everything this site tells a search engine
 * about itself is built from the request, so getting this wrong would
 * advertise an http:// address on every page of an https:// site.
 */
class HttpsUrlTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_canonical_follows_the_scheme_the_visitor_used(): void
    {
        $html = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.10',
        ])->get('/en/about')->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="https://', $html);
        $this->assertStringNotContainsString('<link rel="canonical" href="http://', $html);
    }

    public function test_the_alternates_and_open_graph_follow_it_too(): void
    {
        $html = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/en/about')->getContent();

        $this->assertStringContainsString('<meta property="og:url" content="https://', $html);
        $this->assertStringContainsString('hreflang="fr" href="https://', $html);
    }

    public function test_the_sitemap_follows_it(): void
    {
        $xml = $this->withServerVariables([
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/sitemap.xml')->getContent();

        $this->assertStringNotContainsString('<loc>http://', $xml);
        $this->assertStringContainsString('<loc>https://', $xml);
    }

    public function test_plain_http_is_left_alone(): void
    {
        // No forwarded header means no proxy, so nothing should be rewritten.
        $this->get('/en/about')
            ->assertSee('<link rel="canonical" href="http://', false);
    }
}

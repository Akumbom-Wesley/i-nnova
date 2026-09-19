<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Client;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_declares_a_canonical_and_both_alternates(): void
    {
        $response = $this->get('/en/products');

        $response->assertSee('<link rel="canonical" href="' . url('/en/products') . '">', false);
        $response->assertSee('hreflang="en" href="' . url('/en/products') . '"', false);
        $response->assertSee('hreflang="fr" href="' . url('/fr/products') . '"', false);
        $response->assertSee('hreflang="x-default"', false);
    }

    public function test_the_open_graph_image_resolves_rather_than_404ing(): void
    {
        $settings = SiteSetting::instance();
        $settings->save();
        $settings->addMediaFromString('fake-image-bytes')
            ->usingFileName('og.png')
            ->toMediaCollection('og_image');

        SiteSetting::forgetInstance();

        $response = $this->get('/en');
        $html = $response->getContent();

        preg_match('/<meta property="og:image" content="([^"]+)"/', $html, $matches);

        $this->assertNotEmpty($matches, 'No og:image was emitted.');

        // The URL must point at a file that is actually on disk.
        $path = parse_url($matches[1], PHP_URL_PATH);
        $this->assertFileExists(public_path($path));
    }

    public function test_a_page_without_an_og_image_falls_back_to_a_summary_card(): void
    {
        $this->get('/en')
            ->assertSee('name="twitter:card" content="summary"', false)
            ->assertDontSee('og:image', false);
    }

    public function test_a_product_page_carries_its_own_title_and_schema(): void
    {
        $product = Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'tagline' => ['en' => 'Comprehensive school management system'],
            'status' => ProductStatus::Live,
        ]);

        $response = $this->get("/en/products/{$product->slug}");

        $response->assertSee('<title>EduTrust Schools | I-NNOVA</title>', false);
        $response->assertSee('"@type":"SoftwareApplication"', false);
        $response->assertSee('og:type" content="product"', false);
    }

    public function test_the_organization_schema_is_on_every_page(): void
    {
        $this->get('/en')->assertSee('"@type":"Organization"', false);
        $this->get('/en/contact')->assertSee('"@type":"Organization"', false);
    }

    public function test_the_sitemap_lists_every_locale_and_published_record(): void
    {
        Product::create(['name' => ['en' => 'A Product'], 'slug' => 'a-product', 'status' => ProductStatus::Live]);
        Product::create(['name' => ['en' => 'Unshipped'], 'slug' => 'unshipped', 'status' => ProductStatus::ComingSoon]);
        Client::create(['name' => 'An Institution', 'slug' => 'an-institution', 'summary' => ['en' => 'A deployment.'], 'is_verified' => true]);

        $response = $this->get('/sitemap.xml');

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/xml');

        $xml = $response->getContent();

        $this->assertStringContainsString(url('/en/products/a-product'), $xml);
        $this->assertStringContainsString(url('/fr/products/a-product'), $xml);
        $this->assertStringContainsString(url('/en/work/an-institution'), $xml);

        // An unlaunched product has no page worth indexing.
        $this->assertStringNotContainsString('unshipped', $xml);

        $this->assertNotFalse(simplexml_load_string($xml), 'The sitemap is not valid XML.');
    }

    public function test_robots_allows_the_site_and_points_at_the_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap:', $robots);
    }
}

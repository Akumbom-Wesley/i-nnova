<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Client;
use App\Models\CompanyValue;
use App\Models\Product;
use App\Support\RichText;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Rich text from the admin is printed unescaped, because it is HTML.
 *
 * That makes every editor field a way to put a script on a public page. It
 * takes one phished admin password, and the person who gets hurt is a visitor
 * who never touched the admin. So the markup is filtered on the way out
 * rather than trusted because of who typed it.
 */
class StoredXssTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RichText::flush();
    }

    public function test_a_script_in_a_product_description_never_reaches_the_page(): void
    {
        $this->product('<p>Real copy.</p><script>alert(document.cookie)</script>');

        $html = $this->get('/en/products/a-product')->getContent();

        $this->assertStringNotContainsString('alert(document.cookie)', $html);

        // The legitimate half of the same field must survive, or the fix is
        // just breaking the editor.
        $this->assertStringContainsString('Real copy.', $html);
    }

    public function test_an_event_handler_attribute_is_stripped(): void
    {
        $this->product('<p onmouseover="alert(1)">Hover me</p>');

        $html = $this->get('/en/products/a-product')->getContent();

        $this->assertStringNotContainsString('onmouseover', $html);
        $this->assertStringContainsString('Hover me', $html);
    }

    public function test_a_javascript_url_is_stripped_but_the_link_text_stays(): void
    {
        $this->product('<a href="javascript:alert(1)">Read more</a>');

        $html = $this->get('/en/products/a-product')->getContent();

        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringContainsString('Read more', $html);
    }

    public function test_a_real_link_survives_and_gains_the_rel_that_protects_it(): void
    {
        $this->product('<a href="https://example.com">Partner</a>');

        $html = $this->get('/en/products/a-product')->getContent();

        $this->assertStringContainsString('https://example.com', $html);
        $this->assertStringContainsString('noopener', $html);
    }

    public function test_an_image_pointing_anywhere_is_not_rendered(): void
    {
        // An img in body copy is a request to whatever host it names, which
        // leaks every reader's address to that host.
        $this->product('<p>Copy</p><img src="https://tracker.test/pixel.gif">');

        $this->get('/en/products/a-product')
            ->assertOk()
            ->assertDontSee('tracker.test');
    }

    public function test_a_closing_script_tag_cannot_break_out_of_the_json_ld(): void
    {
        // The structured data block is JSON inside a <script>. A name
        // containing a closing tag ends that script early and everything
        // after it is parsed as markup.
        Product::create([
            'name' => ['en' => 'Breakout</script><script>alert(1)</script>'],
            'slug' => 'breakout',
            'status' => ProductStatus::Live,
        ]);

        $html = $this->get('/en/products/breakout')->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
    }

    public function test_the_values_section_is_filtered_too(): void
    {
        // A second render path for the same kind of content. Filtering one and
        // not the other is how this sort of hole survives a fix.
        CompanyValue::create([
            'title' => ['en' => 'Practicality'],
            'body' => ['en' => '<p>We ship.</p><script>alert(1)</script>'],
        ]);

        $html = $this->get('/en/about')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('We ship.', $html);
    }

    public function test_a_client_story_is_filtered_too(): void
    {
        Client::create([
            'name' => 'An Institution',
            'slug' => 'an-institution',
            'summary' => ['en' => 'A deployment.'],
            'challenge' => ['en' => '<p>The work.</p><script>alert(1)</script>'],
            'is_verified' => true,
        ]);

        $html = $this->get('/en/work/an-institution')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    private function product(string $description): Product
    {
        return Product::create([
            'name' => ['en' => 'A Product'],
            'slug' => 'a-product',
            'status' => ProductStatus::Live,
            'description' => ['en' => $description],
        ]);
    }
}

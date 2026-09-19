<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * A client is a client, whether or not anything is written about it.
 *
 * Case studies used to be a separate model, which meant the same institution
 * could exist twice with nothing keeping the two in step. Now there is one
 * row, and how much has been filled in decides how it appears: a logo on the
 * wall, or a page of its own under Our work.
 */
class ClientWorkTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_client_with_a_summary_gets_a_page(): void
    {
        $client = $this->client(['name' => 'A Told Institution', 'slug' => 'told']);

        $this->get('/en/work/told')
            ->assertOk()
            ->assertSee('A Told Institution');

        $this->get('/en/work')->assertSee('A Told Institution');

        $this->assertTrue($client->isTold());
    }

    public function test_a_logo_on_the_wall_does_not_get_a_page(): void
    {
        // Verified, so it may be shown, but nothing is written about it. It
        // belongs on the home page logo wall, not on a page with a heading
        // and nothing underneath.
        $this->client(['name' => 'Just A Logo', 'slug' => 'just-a-logo', 'summary' => null]);

        $this->get('/en/work/just-a-logo')->assertNotFound();
        $this->get('/en/work')->assertDontSee('Just A Logo');
    }

    public function test_an_unverified_client_is_never_published(): void
    {
        // Written up but not confirmed. The story must not leak out early.
        $this->client([
            'name' => 'Unconfirmed Institution',
            'slug' => 'unconfirmed',
            'is_verified' => false,
        ]);

        $this->get('/en/work/unconfirmed')->assertNotFound();
        $this->get('/en/work')->assertDontSee('Unconfirmed Institution');
    }

    public function test_a_client_can_run_more_than_one_product(): void
    {
        $client = $this->client(['name' => 'Big Institution', 'slug' => 'big']);

        $client->products()->attach([
            $this->product('EduTrust Schools', 'edutrust-schools')->id,
            $this->product('I-NNOVA POS', 'innova-pos')->id,
        ]);

        // The whole point of the pivot: one institution, several systems,
        // rather than a duplicate row per product.
        $this->get('/en/work/big')
            ->assertOk()
            ->assertSee('EduTrust Schools')
            ->assertSee('I-NNOVA POS');
    }

    public function test_a_product_page_names_the_institutions_running_it(): void
    {
        $product = $this->product('EduTrust Schools', 'edutrust-schools');
        $this->client(['name' => 'A School', 'slug' => 'a-school'])->products()->attach($product);

        $this->get('/en/products/edutrust-schools')
            ->assertOk()
            ->assertSee('A School');
    }

    public function test_the_sector_filter_only_offers_sectors_with_something_behind_them(): void
    {
        $education = Sector::create(['name' => ['en' => 'Education'], 'slug' => 'education']);
        $empty = Sector::create(['name' => ['en' => 'Hospitality'], 'slug' => 'hospitality']);

        $this->client(['name' => 'A School', 'slug' => 'a-school', 'sector_id' => $education->id]);

        $html = $this->get('/en/work')->getContent();

        $this->assertStringContainsString('Education', $html);

        // A filter leading to an empty page is a dead end.
        $this->assertStringNotContainsString($empty->slug, $html);
    }

    public function test_a_slug_is_derived_when_none_is_given(): void
    {
        $client = Client::create(['name' => 'Saint Marys College']);

        $this->assertSame('saint-marys-college', $client->slug);
    }

    public function test_two_institutions_with_the_same_name_do_not_collide(): void
    {
        Client::create(['name' => 'Saint Marys College']);
        $second = Client::create(['name' => 'Saint Marys College']);

        $this->assertSame('saint-marys-college-2', $second->slug);
    }

    private function client(array $attributes = []): Client
    {
        return Client::create(array_merge([
            'summary' => ['en' => 'A deployment worth writing about.'],
            'is_verified' => true,
        ], $attributes));
    }

    private function product(string $name, string $slug): Product
    {
        return Product::create([
            'name' => ['en' => $name],
            'slug' => $slug,
            'status' => ProductStatus::Live,
        ]);
    }
}

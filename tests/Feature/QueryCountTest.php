<?php

namespace Tests\Feature;

use App\Enums\GalleryPlacement;
use App\Enums\ProductStatus;
use App\Models\GalleryImage;
use App\Models\Product;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * A page that loads a relation one record at a time costs more queries the
 * more records it shows. Rather than pin a magic number, these add records
 * and assert the count does not move: that is the property that matters, and
 * it does not have to be rewritten every time a section is added.
 */
class QueryCountTest extends TestCase
{
    use RefreshDatabase;

    private function countQueriesFor(string $path): int
    {
        // Warm anything resolved once per process, so the first run is not
        // charged for work the second would not repeat.
        $this->get($path);

        $queries = 0;

        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        $this->get($path)->assertSuccessful();

        DB::flushQueryLog();

        return $queries;
    }

    private function seedProducts(int $count, int $from = 1): void
    {
        foreach (range($from, $from + $count - 1) as $number) {
            Product::create([
                'name' => ['en' => "Product {$number}"],
                'slug' => "product-{$number}",
                'tagline' => ['en' => 'Does a thing'],
                'status' => ProductStatus::Live,
            ]);
        }
    }

    public function test_the_products_page_does_not_query_per_product(): void
    {
        $this->seedProducts(3);
        $withThree = $this->countQueriesFor('/en/products');

        $this->seedProducts(9, 4);
        $withTwelve = $this->countQueriesFor('/en/products');

        $this->assertSame(
            $withThree,
            $withTwelve,
            "Products page went from {$withThree} to {$withTwelve} queries when nine more products were added.",
        );
    }

    public function test_the_home_page_does_not_query_per_record(): void
    {
        $this->seedProducts(2);

        TeamMember::create(['name' => 'A', 'slug' => 'a']);
        Testimonial::create(['quote' => ['en' => 'Good'], 'person_name' => 'B', 'is_featured' => true]);
        GalleryImage::create(['placement' => GalleryPlacement::Home, 'external_url' => 'https://example.com/1.jpg', 'is_active' => true]);

        $before = $this->countQueriesFor('/en');

        $this->seedProducts(6, 3);

        foreach (range(1, 6) as $number) {
            TeamMember::create(['name' => "T{$number}", 'slug' => "t{$number}"]);
            Testimonial::create(['quote' => ['en' => 'Good'], 'person_name' => "P{$number}", 'is_featured' => true]);
            GalleryImage::create([
                'placement' => GalleryPlacement::Home,
                'external_url' => "https://example.com/{$number}.jpg",
                'is_active' => true,
            ]);
        }

        $after = $this->countQueriesFor('/en');

        $this->assertSame(
            $before,
            $after,
            "Home page went from {$before} to {$after} queries when eighteen more records were added.",
        );
    }

    public function test_the_kickstarter_gallery_does_not_query_per_item(): void
    {
        foreach (range(1, 2) as $number) {
            GalleryImage::create([
                'placement' => GalleryPlacement::Kickstarter,
                'external_url' => "https://example.com/k{$number}.jpg",
                'is_active' => true,
            ]);
        }

        $before = $this->countQueriesFor('/en/kickstarter');

        foreach (range(3, 10) as $number) {
            GalleryImage::create([
                'placement' => GalleryPlacement::Kickstarter,
                'external_url' => "https://example.com/k{$number}.jpg",
                'is_active' => true,
            ]);
        }

        $after = $this->countQueriesFor('/en/kickstarter');

        $this->assertSame($before, $after, "Kickstarter went from {$before} to {$after} queries.");
    }
}

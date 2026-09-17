<?php

namespace Tests\Feature;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_gallery_page_shows_everything_placed_in_the_gallery(): void
    {
        $this->makeImages(5);

        $this->get('/en/kickstarter/gallery')
            ->assertOk()
            ->assertSee('Photograph 1')
            ->assertSee('Photograph 5');
    }

    public function test_the_kickstarter_page_shows_only_the_featured_few(): void
    {
        $this->makeImages(8);

        GalleryImage::query()->whereIn('sort_order', [2, 4])->update(['is_featured' => true]);

        $html = $this->get('/en/kickstarter')->getContent();

        $this->assertStringContainsString('Photograph 2', $html);
        $this->assertStringContainsString('Photograph 4', $html);

        // Everything not marked stays on the gallery page.
        $this->assertStringNotContainsString('Photograph 7', $html);
    }

    public function test_the_band_falls_back_to_the_first_few_when_nothing_is_marked(): void
    {
        $this->makeImages(8);

        $html = $this->get('/en/kickstarter')->getContent();

        // A page that went blank because nobody ticked a box would be the
        // worse failure, so the editor's own order stands in.
        $this->assertStringContainsString('Photograph 1', $html);
        $this->assertStringContainsString('Photograph 3', $html);
        $this->assertStringNotContainsString('Photograph 4', $html);
    }

    public function test_the_band_links_on_to_the_rest(): void
    {
        $this->makeImages(8);

        $this->get('/en/kickstarter')
            ->assertSee(route('kickstarter.gallery'), false)
            ->assertSee('See all 8 photographs and videos');
    }

    public function test_the_band_does_not_link_on_when_there_is_nothing_more(): void
    {
        $this->makeImages(GalleryImage::FEATURED_LIMIT);

        // Sending someone to a page holding exactly what they are looking at
        // is a wasted click. The menu still offers the gallery page, so this
        // is about the band's own link rather than the address appearing
        // anywhere in the document.
        $this->get('/en/kickstarter')
            ->assertDontSee('See all 3 photographs and videos');
    }

    public function test_the_gallery_pages_rather_than_growing_without_limit(): void
    {
        $this->makeImages(30);

        // Newest first, so the last one added leads and the first one added
        // has fallen to the second page.
        $this->get('/en/kickstarter/gallery')
            ->assertSee('Photograph 30')
            ->assertDontSee('Photograph 1"', false)
            ->assertSee('?page=2', false);

        $this->get('/en/kickstarter/gallery?page=2')
            ->assertSee('Photograph 1')
            ->assertDontSee('Photograph 30');
    }

    public function test_the_newest_photograph_leads(): void
    {
        $this->makeImages(3);

        // Set rather than touched: all three are created within the same
        // second, so touch() would not reliably move it ahead of the others.
        GalleryImage::query()
            ->where('sort_order', 1)
            ->update(['updated_at' => now()->addDay()]);

        $html = $this->get('/en/kickstarter/gallery')->getContent();

        $this->assertLessThan(
            strpos($html, 'Photograph 3'),
            strpos($html, 'Photograph 1'),
            'The most recently updated photograph should come first.',
        );
    }

    public function test_the_gallery_can_be_narrowed_to_one_kind_of_thing(): void
    {
        $this->makeImages(2);

        GalleryImage::create([
            'placement' => GalleryPlacement::Kickstarter,
            'external_url' => 'https://example.test/still.jpg',
            'video_url' => 'https://www.youtube.com/watch?v=abc123',
            'alt' => ['en' => 'A recorded session'],
            'is_active' => true,
        ]);

        $this->get('/en/kickstarter/gallery?type=video')
            ->assertSee('A recorded session')
            ->assertDontSee('Photograph 1');

        $this->get('/en/kickstarter/gallery?type=photo')
            ->assertSee('Photograph 1')
            ->assertDontSee('A recorded session');

        // Unfiltered is still everything.
        $this->get('/en/kickstarter/gallery')
            ->assertSee('Photograph 1')
            ->assertSee('A recorded session');
    }

    public function test_a_filter_with_nothing_behind_it_is_not_offered(): void
    {
        $this->makeImages(3);

        // No video, so there is no point in a Video button that leads to an
        // empty page.
        $this->get('/en/kickstarter/gallery')
            ->assertSee('Photographs')
            ->assertDontSee('type=video', false);
    }

    public function test_a_nonsense_filter_shows_the_gallery_rather_than_failing(): void
    {
        $this->makeImages(2);

        $this->get('/en/kickstarter/gallery?type=something-else')
            ->assertOk()
            ->assertSee('Photograph 1');
    }

    public function test_the_filter_survives_paging(): void
    {
        $this->makeImages(30);

        $this->get('/en/kickstarter/gallery?type=photo')
            ->assertSee('type=photo', false)
            ->assertSee('page=2', false);
    }

    public function test_pagination_keeps_the_locale_it_was_reached_in(): void
    {
        $this->makeImages(30);

        $this->get('/fr/kickstarter/gallery')
            ->assertOk()
            ->assertSee('/fr/kickstarter/gallery?page=2', false);
    }

    public function test_the_page_stands_up_with_nothing_in_it(): void
    {
        // Not linked from anywhere when empty, but the address can still be
        // typed or shared.
        $this->get('/en/kickstarter/gallery')
            ->assertOk()
            ->assertSee('There is nothing in the gallery yet.');
    }

    public function test_photographs_are_reachable_without_javascript(): void
    {
        $this->makeImages(1);

        // The lightbox only intercepts a click on a link that already works.
        $this->get('/en/kickstarter/gallery')
            ->assertSee('https://example.test/photo-1.jpg', false);
    }

    private function makeImages(int $count): void
    {
        foreach (range(1, $count) as $index) {
            GalleryImage::create([
                'placement' => GalleryPlacement::Kickstarter,
                'external_url' => "https://example.test/photo-{$index}.jpg",
                'alt' => ['en' => "Photograph {$index}"],
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}

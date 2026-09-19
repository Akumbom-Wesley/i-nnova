<?php

namespace Tests\Feature;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use App\Models\User;
use Database\Seeders\Support\PlaceholderImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PhotographyTest extends TestCase
{
    use RefreshDatabase;

    private function photo(GalleryPlacement $placement, array $attributes = []): GalleryImage
    {
        return GalleryImage::create(array_merge([
            'placement' => $placement,
            'title' => ['en' => 'A photograph'],
            'caption' => ['en' => 'Engineers at work'],
            'alt' => ['en' => 'Two engineers at a desk'],
            'external_url' => 'https://picsum.photos/seed/test/1200/900',
            'is_active' => true,
        ], $attributes));
    }

    public static function placements(): array
    {
        return [
            'home' => ['home', '/en'],
            'about' => ['about', '/en/about'],
            'kickstarter' => ['kickstarter', '/en/kickstarter'],
        ];
    }

    #[DataProvider('placements')]
    public function test_a_photo_appears_on_the_page_it_is_placed_on(string $placement, string $path): void
    {
        $this->photo(GalleryPlacement::from($placement));

        $this->get($path)
            ->assertSee('https://picsum.photos/seed/test/1200/900', false)
            ->assertSee('Two engineers at a desk');
    }

    #[DataProvider('placements')]
    public function test_a_gallery_section_is_absent_when_it_has_no_photos(string $placement, string $path): void
    {
        // Sections are conditional, so an empty placement leaves no empty
        // header behind on the page.
        $this->get($path)->assertDontSee('Inside I-NNOVA');
    }

    public function test_a_photo_only_shows_where_it_is_placed(): void
    {
        $this->photo(GalleryPlacement::Kickstarter, [
            'external_url' => 'https://picsum.photos/seed/only-kickstarter/1200/900',
        ]);

        $this->get('/en')->assertDontSee('only-kickstarter', false);
        $this->get('/en/about')->assertDontSee('only-kickstarter', false);
        $this->get('/en/kickstarter')->assertSee('only-kickstarter', false);
    }

    public function test_a_hidden_photo_is_not_rendered(): void
    {
        $this->photo(GalleryPlacement::About, [
            'external_url' => 'https://picsum.photos/seed/not-shown-at-all/1200/900',
            'is_active' => false,
        ]);

        // "hidden" on its own matches aria-hidden all over the markup, so the
        // assertion has to be against the seed in the address.
        $this->get('/en/about')->assertDontSee('not-shown-at-all', false);
    }

    public function test_an_upload_replaces_the_stand_in_with_no_code_change(): void
    {
        $photo = $this->photo(GalleryPlacement::About);

        $this->assertTrue($photo->isPlaceholder());
        $this->get('/en/about')->assertSee('picsum.photos', false);

        // This is what an editor does in the admin: drop a file on the record.
        // Media Library runs conversions on upload, so this has to be a real
        // image rather than arbitrary bytes.
        $photo->addMediaFromString(PlaceholderImage::png('Team at work', 1200, 900))
            ->usingFileName('team-at-work.png')
            ->toMediaCollection('image');

        $photo->refresh();

        $this->assertFalse($photo->isPlaceholder());

        $this->get('/en/about')
            ->assertSee('team-at-work', false)
            ->assertDontSee('picsum.photos', false);
    }

    public function test_alt_text_falls_back_to_the_title_when_left_empty(): void
    {
        $photo = $this->photo(GalleryPlacement::About, ['alt' => null]);

        $this->assertSame('A photograph', $photo->altText());
    }

    public function test_every_rendered_photo_has_alt_text_and_dimensions(): void
    {
        $this->photo(GalleryPlacement::About);

        $html = preg_replace('/\s+/', ' ', $this->get('/en/about')->getContent());

        preg_match_all('/<img [^>]*picsum[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches[0]);

        foreach ($matches[0] as $tag) {
            $this->assertStringContainsString('alt="Two engineers at a desk"', $tag);
            $this->assertStringContainsString('width=', $tag);
            $this->assertStringContainsString('height=', $tag);
            $this->assertStringContainsString('loading="lazy"', $tag);
        }
    }

    public function test_a_stand_in_emits_no_srcset_because_it_cannot_be_resized(): void
    {
        $photo = $this->photo(GalleryPlacement::About);

        $this->assertNull($photo->srcset());
        $this->get('/en/about')->assertDontSee('srcset', false);
    }

    public function test_the_admin_lists_photography_and_counts_the_stand_ins(): void
    {
        $this->photo(GalleryPlacement::About);
        $this->photo(GalleryPlacement::Home, [
            'external_url' => 'https://picsum.photos/seed/second/1200/900',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/gallery-images')
            ->assertSuccessful();

        $this->assertSame('2', \App\Filament\Resources\GalleryImages\GalleryImageResource::getNavigationBadge());
    }

    public function test_the_hero_falls_back_to_the_light_treatment_with_no_slides(): void
    {
        // With no photography loaded the hero must not render as a black
        // rectangle waiting for images that are not there.
        $response = $this->get('/en');

        $response->assertSuccessful();
        $response->assertDontSee('heroSlider(', false);
        $response->assertSee('Practical software for institutions');
    }

    public function test_the_hero_runs_a_slideshow_once_photographs_are_placed(): void
    {
        foreach (range(1, 4) as $number) {
            $this->photo(GalleryPlacement::Hero, [
                'external_url' => "https://picsum.photos/seed/hero{$number}/1920/1080",
                'sort_order' => $number,
            ]);
        }

        $response = $this->get('/en');

        $response->assertSee('heroSlider(4)', false);
        $response->assertSee('hero-scrim', false);

        foreach (range(1, 4) as $number) {
            $response->assertSee("seed/hero{$number}/", false);
        }
    }

    public function test_only_the_first_slide_is_eager_so_the_rest_do_not_fight_the_headline(): void
    {
        foreach (range(1, 3) as $number) {
            $this->photo(GalleryPlacement::Hero, [
                'external_url' => "https://picsum.photos/seed/hero{$number}/1920/1080",
                'sort_order' => $number,
            ]);
        }

        $html = preg_replace('/\s+/', ' ', $this->get('/en')->getContent());

        preg_match_all('/<img [^>]*seed\/hero[^>]*>/', $html, $matches);

        $this->assertCount(3, $matches[0]);
        $this->assertSame(1, substr_count(implode(' ', $matches[0]), 'fetchpriority="high"'));
        $this->assertSame(2, substr_count(implode(' ', $matches[0]), 'loading="lazy"'));
    }

    public function test_a_single_slide_gets_no_dots_to_click(): void
    {
        $this->photo(GalleryPlacement::Hero);

        $this->get('/en')->assertDontSee('hero-dot', false);
    }

    public function test_the_slides_are_hidden_from_assistive_tech_but_the_controls_are_not(): void
    {
        foreach (range(1, 3) as $number) {
            $this->photo(GalleryPlacement::Hero, [
                'external_url' => "https://picsum.photos/seed/hero{$number}/1920/1080",
                'sort_order' => $number,
            ]);
        }

        $html = preg_replace('/\s+/', ' ', $this->get('/en')->getContent());

        // The headline carries the meaning, so the photographs are decorative.
        $this->assertMatchesRegularExpression('/<div class="absolute inset-0" aria-hidden="true">/', $html);

        // The dots are real controls and each one says what it goes to.
        $this->assertStringContainsString('aria-label="Choose a photograph"', $html);
        $this->assertStringContainsString('<span class="sr-only">Two engineers at a desk</span>', $html);
    }

    public function test_the_hero_serves_the_untouched_upload(): void
    {
        $photo = $this->photo(GalleryPlacement::Hero, ['external_url' => null]);

        $photo->addMediaFromString(PlaceholderImage::png('Hero', 1280, 960))
            ->usingFileName('hero-original.png')
            ->toMediaCollection('image');

        $html = preg_replace('/\s+/', ' ', $this->get('/en')->getContent());

        preg_match('/<img [^>]*hero-original[^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches, 'The hero did not render the upload.');

        // The src must be the original file, not a re-encoded conversion, so
        // nothing stands between the photograph and the reader.
        $this->assertMatchesRegularExpression(
            '#src="[^"]*/hero-original\.png"#',
            $matches[0],
            'The hero should serve the original, not a conversion.',
        );
        $this->assertStringNotContainsString('conversions/', $matches[0]);
    }

    public function test_gallery_tiles_use_a_conversion_rather_than_the_full_original(): void
    {
        // Tiles render small, so serving the full file to each would be waste
        // rather than quality.
        foreach (range(1, 3) as $number) {
            $photo = $this->photo(GalleryPlacement::About, ['external_url' => null, 'sort_order' => $number]);

            $photo->addMediaFromString(PlaceholderImage::png("Tile {$number}", 1280, 960))
                ->usingFileName("tile-{$number}.png")
                ->toMediaCollection('image');
        }

        $html = preg_replace('/\s+/', ' ', $this->get('/en/about')->getContent());

        // The featured tile takes the larger conversion, the rest take thumb.
        $this->assertStringContainsString('tile-1-wide', $html);
        $this->assertStringContainsString('tile-2-thumb', $html);
    }

    public function test_the_kickstarter_header_carries_a_photo_cluster(): void
    {
        foreach (range(1, 3) as $number) {
            $this->photo(GalleryPlacement::KickstarterFeature, [
                'external_url' => "https://picsum.photos/seed/ks{$number}/1200/900",
                'sort_order' => $number,
            ]);
        }

        $html = preg_replace('/\s+/', ' ', $this->get('/en/kickstarter')->getContent());

        $this->assertStringContainsString('float-drift', $html);

        // Two portraits across the top, one landscape centred beneath.
        $this->assertSame(2, substr_count($html, 'aspect-[4/5]'));
        $this->assertSame(1, substr_count($html, 'aspect-[16/10]'));
    }

    public function test_the_cluster_and_the_gallery_show_different_photographs(): void
    {
        // Separate placements, so neither takes from the other and the
        // gallery no longer disappears when the cluster is full.
        foreach (range(1, 3) as $number) {
            $this->photo(GalleryPlacement::KickstarterFeature, [
                'external_url' => "https://picsum.photos/seed/feature{$number}/1200/900",
                'sort_order' => $number,
            ]);

            $this->photo(GalleryPlacement::Kickstarter, [
                'external_url' => "https://picsum.photos/seed/gallery{$number}/1200/900",
                'sort_order' => $number,
            ]);
        }

        $html = $this->get('/en/kickstarter')->getContent();

        foreach (range(1, 3) as $number) {
            $this->assertSame(1, substr_count($html, "seed/feature{$number}/"));
            $this->assertSame(1, substr_count($html, "seed/gallery{$number}/"));
        }

        $this->assertStringContainsString('The programme in pictures and video', $html);
    }

    public function test_the_gallery_shows_with_even_one_item(): void
    {
        // It used to take what the cluster left over, so fewer than four
        // meant no gallery at all. It is its own placement now.
        $this->photo(GalleryPlacement::Kickstarter, [
            'external_url' => 'https://picsum.photos/seed/only-one/1200/900',
        ]);

        $this->get('/en/kickstarter')
            ->assertSee('The programme in pictures and video')
            ->assertSee('only-one', false);
    }

    public function test_the_gallery_plays_a_linked_video(): void
    {
        $this->photo(GalleryPlacement::Kickstarter, [
            'external_url' => null,
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->get('/en/kickstarter')
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false)
            ->assertSee('<iframe', false);
    }

    public function test_a_video_that_is_neither_youtube_nor_vimeo_is_not_embedded(): void
    {
        $photo = $this->photo(GalleryPlacement::Kickstarter, [
            'video_url' => 'https://example.com/clip.mp4',
        ]);

        // Embedding an unknown host would mean trusting whatever it serves.
        $this->assertNull($photo->videoEmbedUrl());
        $this->assertTrue($photo->isVideo());
    }

    public function test_the_cluster_is_absent_when_there_are_no_photographs(): void
    {
        $this->get('/en/kickstarter')
            ->assertSuccessful()
            ->assertDontSee('float-drift', false);
    }

    public function test_the_about_header_is_a_designed_panel_not_photographs(): void
    {
        // Every photograph of the company lives on Kickstarter. Repeating it
        // on About would show the same three pictures twice across the site.
        $this->photo(GalleryPlacement::About);

        $html = $this->get('/en/about')->getContent();

        $this->assertStringContainsString('Technology', $html);
        $this->assertStringContainsString('Build what runs', $html);
        $this->assertStringNotContainsString('aspect-[4/5]', $html);
    }

    public function test_the_stem_panel_needs_no_content_to_render(): void
    {
        // It is a designed panel, so it must stand up on an empty database.
        $this->get('/en/about')
            ->assertSuccessful()
            ->assertSee('Science')
            ->assertSee('Mathematics')
            ->assertSee('Prove it works');
    }

    public function test_the_stem_panel_is_translated(): void
    {
        $this->get('/fr/about')
            ->assertSee('Technologie')
            ->assertSee('Construire ce qui tourne');
    }
}

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
            ->assertSee('Engineers at work');
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
}

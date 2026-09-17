<?php

namespace Tests\Feature;

use App\Enums\GalleryPlacement;
use App\Enums\ProductStatus;
use App\Models\GalleryImage;
use App\Models\KickstarterTrack;
use App\Models\ProcessStep;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Support\Navigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationAndMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_menu_is_grouped_and_has_an_explicit_home(): void
    {
        $response = $this->get('/en');

        foreach (['Home', 'Products', 'Kickstarter', 'Company'] as $item) {
            $response->assertSee($item);
        }
    }

    public function test_the_products_menu_is_filled_from_what_is_published(): void
    {
        Product::create([
            'name' => ['en' => 'A Published Product'],
            'slug' => 'a-published-product',
            'status' => ProductStatus::Live,
        ]);

        Product::create([
            'name' => ['en' => 'An Unreleased Product'],
            'slug' => 'an-unreleased-product',
            'status' => ProductStatus::ComingSoon,
        ]);

        $html = $this->get('/en')->getContent();

        // A new product reaches the menu without anyone editing a template.
        $this->assertStringContainsString('a-published-product', $html);

        // Something unreleased is not offered as though it were available.
        $this->assertStringNotContainsString('an-unreleased-product', $html);
    }

    /**
     * Walks the real menu rather than a list written out here, so an anchor
     * added to the navigation cannot quietly point at nothing.
     */
    public function test_every_anchor_the_menu_points_at_exists(): void
    {
        $this->seedEverySectionTheMenuLinksTo();

        $anchors = $this->anchorsInTheMenu();

        $this->assertNotEmpty($anchors, 'The menu offered no anchors at all, so this proved nothing.');

        foreach ($anchors as $url) {
            [$page, $fragment] = explode('#', $url, 2);

            $this->assertStringContainsString(
                'id="' . $fragment . '"',
                $this->get($page)->getContent(),
                "The menu links to #{$fragment} on {$page}, but no such section is rendered.",
            );
        }
    }

    public function test_an_anchor_is_not_offered_when_its_section_is_empty(): void
    {
        // Nothing seeded: every anchored section is conditional, so none of
        // them render and none of them should be in the menu.
        $this->assertSame([], $this->anchorsInTheMenu());
    }

    /**
     * Every URL in the menu that carries a fragment, as a path plus fragment.
     *
     * @return array<int, string>
     */
    private function anchorsInTheMenu(): array
    {
        // Through a request, so the menu is built with the locale prefix the
        // visitor actually gets rather than the CLI default.
        $this->get('/en');

        return collect(Navigation::menu())
            ->flatMap(fn (array $item): array => [$item, ...$item['children'] ?? []])
            ->pluck('url')
            ->filter(fn (string $url): bool => str_contains($url, '#'))
            ->map(fn (string $url): string => (string) parse_url($url, PHP_URL_PATH) . '#' . parse_url($url, PHP_URL_FRAGMENT))
            ->unique()
            ->values()
            ->all();
    }

    private function seedEverySectionTheMenuLinksTo(): void
    {
        ProcessStep::create(['title' => ['en' => 'Understand the day']]);
        TeamMember::create(['name' => 'Ada Example', 'slug' => 'ada-example']);

        KickstarterTrack::create([
            'name' => ['en' => 'Web Development'],
            'slug' => 'web-development',
            'is_active' => true,
        ]);

        GalleryImage::create([
            'placement' => GalleryPlacement::Kickstarter,
            'external_url' => 'https://example.test/cohort.jpg',
            'alt' => ['en' => 'A cohort at work'],
            'is_active' => true,
        ]);
    }

    public function test_the_map_shows_once_coordinates_are_set(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'map_latitude' => 5.9631,
            'map_longitude' => 10.1591,
            'map_is_visible' => true,
        ])->save();

        SiteSetting::forgetInstance();

        $this->get('/en/about')
            ->assertSee('Where we work from')
            ->assertSee('openstreetmap.org/export/embed', false);
    }

    public function test_the_map_is_absent_without_coordinates(): void
    {
        $this->get('/en/about')->assertDontSee('Where we work from');
    }

    public function test_the_map_can_be_hidden_without_losing_the_coordinates(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'map_latitude' => 5.9631,
            'map_longitude' => 10.1591,
            'map_is_visible' => false,
        ])->save();

        SiteSetting::forgetInstance();

        $this->get('/en/about')->assertDontSee('Where we work from');
        $this->assertEqualsWithDelta(5.9631, (float) SiteSetting::instance()->map_latitude, 0.0001);
    }

    public function test_an_empty_zoom_falls_back_rather_than_failing_to_save(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'map_latitude' => 5.9631,
            'map_longitude' => 10.1591,
            'map_zoom' => null,
        ])->save();

        $this->assertSame(SiteSetting::DEFAULT_MAP_ZOOM, $settings->fresh()->mapZoom());
    }
}

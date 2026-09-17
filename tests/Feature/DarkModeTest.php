<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Dark mode is a redefinition of the semantic tokens, so nothing in a template
 * carries a dark variant. These guard the two things that would break that:
 * a hardcoded colour creeping into a view, and a token that must not flip
 * being renamed to one that does.
 */
class DarkModeTest extends TestCase
{
    use RefreshDatabase;

    private const COLORS = [
        // Light
        'paper' => '#FFFFFF',
        'paper-dim' => '#F5F7FA',
        'ink' => '#0A1A33',
        'content' => '#0A1A33',
        'muted' => '#5B6B85',
        'primary' => '#1157B6',
        'accent' => '#E85D0C',
        'accent-text' => '#B8460A',
        // Dark
        'dark-paper' => '#0B1524',
        'dark-paper-dim' => '#111E31',
        'dark-ink' => '#18293F',
        'dark-content' => '#E8EDF5',
        'dark-muted' => '#9DACC2',
        'dark-primary' => '#6FA8F5',
        'dark-accent' => '#FF7A2E',
        'dark-accent-text' => '#FFA167',
    ];

    private function luminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $channels = [];

        foreach ([0, 2, 4] as $offset) {
            $value = hexdec(substr($hex, $offset, 2)) / 255;
            $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function contrast(string $a, string $b): float
    {
        $la = $this->luminance(self::COLORS[$a]);
        $lb = $this->luminance(self::COLORS[$b]);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    public static function darkPairs(): array
    {
        return [
            'body text on the page' => ['dark-content', 'dark-paper', 4.5],
            'body text on the dim band' => ['dark-content', 'dark-paper-dim', 4.5],
            'body text on the contrast band' => ['dark-content', 'dark-ink', 4.5],
            'muted text on the page' => ['dark-muted', 'dark-paper', 4.5],
            'muted on the dim band' => ['dark-muted', 'dark-paper-dim', 4.5],
            'links on the page' => ['dark-primary', 'dark-paper', 4.5],
            'orange as text on the page' => ['dark-accent-text', 'dark-paper', 4.5],
            'eyebrow orange on the contrast band' => ['dark-accent', 'dark-ink', 4.5],
            'focus ring on the page' => ['dark-accent', 'dark-paper', 3.0],
        ];
    }

    #[DataProvider('darkPairs')]
    public function test_the_dark_palette_clears_wcag_aa(string $foreground, string $background, float $minimum): void
    {
        $this->assertGreaterThanOrEqual($minimum, round($this->contrast($foreground, $background), 2));
    }

    public function test_the_brand_colours_keep_their_identity_in_dark_mode(): void
    {
        // Blue must still read as blue and orange as orange. Only their
        // lightness moves, enough to clear contrast on a dark ground.
        foreach ([['primary', 'dark-primary'], ['accent', 'dark-accent']] as [$light, $dark]) {
            [$lr, $lg, $lb] = sscanf(self::COLORS[$light], '#%02x%02x%02x');
            [$dr, $dg, $db] = sscanf(self::COLORS[$dark], '#%02x%02x%02x');

            $lightIsBlue = $lb > $lr;
            $darkIsBlue = $db > $dr;

            $this->assertSame($lightIsBlue, $darkIsBlue, "{$light} changed hue family in dark mode.");
            $this->assertGreaterThan(
                $this->luminance(self::COLORS[$light]),
                $this->luminance(self::COLORS[$dark]),
                "{$dark} should be lighter than {$light} to sit on a dark ground.",
            );
        }
    }

    public function test_the_contrast_band_stays_dark_in_both_modes(): void
    {
        // ink is the ground under every white-on-navy section. Inverting it
        // would turn those sections inside out.
        $this->assertLessThan(0.2, $this->luminance(self::COLORS['ink']));
        $this->assertLessThan(0.2, $this->luminance(self::COLORS['dark-ink']));

        // And it must still separate from the page it sits on.
        $this->assertNotSame(self::COLORS['dark-ink'], self::COLORS['dark-paper']);
    }

    public static function pages(): array
    {
        return [
            'home' => ['/en'],
            'products' => ['/en/products'],
            'work' => ['/en/work'],
            'about' => ['/en/about'],
            'kickstarter' => ['/en/kickstarter'],
            'contact' => ['/en/contact'],
        ];
    }

    #[DataProvider('pages')]
    public function test_no_page_hardcodes_a_colour_that_cannot_follow_the_mode(string $path): void
    {
        $html = $this->get($path)->getContent();

        // Strip what is allowed to be fixed: inline SVG brand artwork and the
        // scrim, which stays dark over a photograph in either mode.
        $body = preg_replace('/<svg\b.*?<\/svg>/s', '', $html);
        $body = preg_replace('/<script\b.*?<\/script>/s', '', (string) $body);
        $body = preg_replace('/<style\b.*?<\/style>/s', '', (string) $body);

        // A translucent white on a band that stays dark in both modes is
        // correct, so only the opaque forms are a problem.
        preg_match_all(
            '/(?<![\w-])(bg-white|text-white-|bg-black|text-black|bg-gray-\d+|text-gray-\d+)(?![\w\/-])/',
            (string) $body,
            $matches,
        );

        $this->assertSame([], $matches[1], "{$path} uses a fixed colour that will not follow the mode: " . implode(', ', array_unique($matches[1])));
    }

    #[DataProvider('pages')]
    public function test_every_page_can_be_served_in_either_mode(string $path): void
    {
        // The mode is a client-side attribute, so the server response is the
        // same either way. This is really asserting the toggle is present on
        // every page rather than only the home page.
        $this->get($path)
            ->assertSuccessful()
            ->assertSee('themeToggle', false);
    }
}

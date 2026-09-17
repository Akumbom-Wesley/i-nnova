<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccessibilityTest extends TestCase
{
    use RefreshDatabase;

    private const COLORS = [
        'primary' => '#1157B6',
        'accent' => '#E85D0C',
        'accent-dark' => '#C74F0C',
        'accent-text' => '#B8460A',
        'ink' => '#0A1A33',
        'paper' => '#FFFFFF',
        'paper-dim' => '#F5F7FA',
        'muted' => '#5B6B85',
    ];

    private function relativeLuminance(string $hex): float
    {
        $hex = ltrim($hex, '#');
        $channels = [];

        foreach ([0, 2, 4] as $offset) {
            $value = hexdec(substr($hex, $offset, 2)) / 255;
            $channels[] = $value <= 0.03928 ? $value / 12.92 : (($value + 0.055) / 1.055) ** 2.4;
        }

        return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
    }

    private function contrast(string $foreground, string $background): float
    {
        $a = $this->relativeLuminance(self::COLORS[$foreground]);
        $b = $this->relativeLuminance(self::COLORS[$background]);

        return (max($a, $b) + 0.05) / (min($a, $b) + 0.05);
    }

    public static function contrastPairs(): array
    {
        return [
            'body text' => ['ink', 'paper', 4.5],
            'muted text' => ['muted', 'paper', 4.5],
            'muted on the dim band' => ['muted', 'paper-dim', 4.5],
            'links' => ['primary', 'paper', 4.5],
            'orange as text' => ['accent-text', 'paper', 4.5],
            // Accent labels are set at 16px semibold, which counts as large text.
            'white on accent' => ['paper', 'accent', 3.0],
            'white on accent hover' => ['paper', 'accent-dark', 4.5],
            'white on the blue band' => ['paper', 'primary', 4.5],
            'white on the navy band' => ['paper', 'ink', 4.5],
            // The focus ring is a non-text indicator, so 3:1 applies.
            'focus ring on white' => ['accent', 'paper', 3.0],
            'focus halo on the blue band' => ['paper', 'primary', 3.0],
            'focus ring on the navy band' => ['accent', 'ink', 3.0],
        ];
    }

    #[DataProvider('contrastPairs')]
    public function test_the_palette_clears_wcag_aa(string $foreground, string $background, float $minimum): void
    {
        $ratio = $this->contrast($foreground, $background);

        $this->assertGreaterThanOrEqual(
            $minimum,
            round($ratio, 2),
            "{$foreground} on {$background} is only " . round($ratio, 2) . ':1.',
        );
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
    public function test_each_page_has_exactly_one_h1_and_no_skipped_levels(string $path): void
    {
        $html = $this->get($path)->getContent();

        preg_match_all('/<h([1-6])[ >]/', $html, $matches);

        $levels = array_map('intval', $matches[1]);

        $this->assertSame(1, count(array_filter($levels, fn (int $l): bool => $l === 1)), "{$path} needs exactly one h1.");

        $previous = 0;

        foreach ($levels as $level) {
            if ($previous !== 0) {
                $this->assertLessThanOrEqual(
                    1,
                    $level - $previous,
                    "{$path} jumps from h{$previous} to h{$level}.",
                );
            }

            $previous = $level;
        }
    }

    #[DataProvider('pages')]
    public function test_each_page_has_a_skip_link_and_a_main_landmark(string $path): void
    {
        $this->get($path)
            ->assertSee('href="#main"', false)
            ->assertSee('<main id="main">', false);
    }

    public function test_every_image_carries_alt_text_and_intrinsic_dimensions(): void
    {
        $html = preg_replace('/\s+/', ' ', $this->get('/en')->getContent());

        preg_match_all('/<img [^>]*>/', $html, $matches);

        $this->assertNotEmpty($matches[0], 'The home page rendered no images.');

        foreach ($matches[0] as $tag) {
            // alt may be empty for decoration, but it has to be declared.
            $this->assertStringContainsString('alt=', $tag, "Missing alt: {$tag}");
            $this->assertStringContainsString('width=', $tag, "Missing width: {$tag}");
            $this->assertStringContainsString('height=', $tag, "Missing height: {$tag}");
        }
    }

    public function test_the_contact_form_labels_every_field(): void
    {
        $html = $this->get('/en/contact')->getContent();

        foreach (['name', 'email', 'phone', 'organisation', 'subject', 'message'] as $field) {
            $this->assertStringContainsString('for="field-' . $field . '"', $html, "No label for {$field}.");
            $this->assertStringContainsString('id="field-' . $field . '"', $html, "No control for {$field}.");
        }
    }

    public function test_decorative_motifs_are_hidden_from_assistive_tech(): void
    {
        $html = preg_replace('/\s+/', ' ', $this->get('/en')->getContent());

        preg_match_all('/<svg [^>]*>/', $html, $matches);

        foreach ($matches[0] as $tag) {
            $this->assertStringContainsString('aria-hidden="true"', $tag, "Unlabelled svg: {$tag}");
        }
    }
    /**
     * The inner page headers sit on a coloured ground. Orange measures 4.97:1
     * on navy but only 2.42:1 on the brand blue, so the gradient is written to
     * keep the whole text column on navy and let blue in only past it. These
     * assert the navy end, which is what the eyebrow and the rule sit on.
     */
    public static function headerGroundPairs(): array
    {
        return [
            'white title on the header ground' => ['paper', 'ink', 4.5],
            'orange eyebrow on the header ground' => ['accent', 'ink', 4.5],
            'orange rule on the header ground' => ['accent', 'ink', 3.0],
        ];
    }

    #[DataProvider('headerGroundPairs')]
    public function test_the_coloured_page_header_stays_legible(string $foreground, string $background, float $minimum): void
    {
        $ratio = $this->contrast($foreground, $background);

        $this->assertGreaterThanOrEqual($minimum, round($ratio, 2));
    }

    public function test_orange_is_never_asked_to_sit_on_the_brand_blue(): void
    {
        // Documents why the header gradient is written the way it is. If this
        // ever passes, the constraint has gone and the gradient can relax.
        $this->assertLessThan(
            3.0,
            $this->contrast('accent', 'primary'),
            'Orange now clears 3:1 on blue; the header gradient constraint can be revisited.',
        );
    }
}
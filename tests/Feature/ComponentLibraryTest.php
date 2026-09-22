<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ComponentLibraryTest extends TestCase
{
    use RefreshDatabase;

    public static function motifs(): array
    {
        return [
            'circuit' => ['tech.circuit', 'circuit-trace'],
            'grid' => ['tech.grid', 'grid-scan'],
            'network' => ['tech.network', 'circuit-packet'],
            'code' => ['tech.code', 'code-line'],
            'waveform' => ['tech.waveform', 'wave-draw'],
        ];
    }

    #[DataProvider('motifs')]
    public function test_each_motif_renders_and_is_hidden_from_assistive_tech(string $component, string $animationClass): void
    {
        $html = Blade::render('<x-' . $component . ' class="w-10" />');

        $this->assertStringContainsString($animationClass, $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    public function test_the_score_ring_fills_to_its_value(): void
    {
        $html = Blade::render('<x-ui.score-ring :value="27" :max="100" label="Career Capital Score" />');

        $this->assertStringContainsString('Career Capital Score', $html);
        $this->assertStringContainsString('27', $html);
        $this->assertStringContainsString('ring-value', $html);

        // 27 of 100 leaves 73 percent of the circumference still dashed.
        $circumference = 2 * M_PI * 70;
        $this->assertStringContainsString('--ring-offset: ' . round($circumference * 0.73, 2), $html);
    }

    public function test_a_score_of_zero_does_not_divide_by_zero(): void
    {
        $html = Blade::render('<x-ui.score-ring :value="0" :max="0" />');

        $this->assertStringContainsString('ring-value', $html);
    }

    public function test_the_logo_wall_shows_only_verified_clients(): void
    {
        Client::create(['name' => 'Confirmed Institution', 'is_verified' => true]);
        Client::create(['name' => 'Unconfirmed Institution', 'is_verified' => false]);

        // Deliberately passing every client, including the unverified one, to
        // prove the component refuses it rather than trusting the caller.
        $html = Blade::render(
            '<x-ui.logo-wall :clients="$clients" />',
            ['clients' => Client::all()],
        );

        $this->assertStringContainsString('Confirmed Institution', $html);
        $this->assertStringNotContainsString('Unconfirmed Institution', $html);
    }

    public function test_an_unconfirmed_client_never_reaches_the_home_page(): void
    {
        Client::create([
            'name' => 'Unconfirmed Institution',
            'is_verified' => false,
            'is_featured' => true,
        ]);

        $this->get('/en')->assertDontSee('Unconfirmed Institution');
    }

    public function test_a_confirmed_client_appears_in_the_deployments_band(): void
    {
        // Clients used to sit in the logo wall beside the partners. That band
        // is partners only now, so the home page shows clients where there is
        // something to say about them: the deployments band, which names the
        // institution and what it runs.
        Client::create([
            'name' => 'Confirmed Institution',
            'is_verified' => true,
            'is_featured' => true,
        ]);

        $this->get('/en')->assertSee('Confirmed Institution');
    }

    public function test_the_feature_list_renders_each_item(): void
    {
        $html = Blade::render(
            '<x-ui.feature-list :items="[\'Hands-on projects\', \'Mentorship and guidance\']" />',
        );

        $this->assertStringContainsString('Hands-on projects', $html);
        $this->assertStringContainsString('Mentorship and guidance', $html);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_timeline_renders_in_order(): void
    {
        Milestone::create(['year' => '2022', 'title' => ['en' => 'The spark'], 'sort_order' => 0]);
        Milestone::create(['year' => '2025', 'title' => ['en' => 'Scale'], 'sort_order' => 1]);

        $html = $this->get('/en/about')->getContent();

        $this->assertStringContainsString('2022', $html);
        $this->assertStringContainsString('The spark', $html);
        $this->assertLessThan(
            strpos($html, 'Scale'),
            strpos($html, 'The spark'),
            'The timeline should read oldest first.',
        );
    }

    public function test_the_timeline_section_is_absent_when_empty(): void
    {
        $this->get('/en/about')->assertDontSee('From one idea in Bamenda');
    }

    public function test_mission_and_vision_render_when_set(): void
    {
        $settings = SiteSetting::instance();
        $settings->fill([
            'mission' => ['en' => 'To develop innovative technology for Cameroon.'],
            'vision' => ['en' => 'To become the leading dual-mission tech company.'],
        ])->save();

        SiteSetting::forgetInstance();

        $this->get('/en/about')
            ->assertSee('To develop innovative technology for Cameroon.')
            ->assertSee('To become the leading dual-mission tech company.');
    }

    public function test_the_mission_section_is_absent_when_unset(): void
    {
        // Both fields start empty, so the band must not render as an empty
        // blue stripe with two headings and nothing under them.
        $this->get('/en/about')->assertDontSee('>Mission<', false);
    }

    public function test_the_site_never_publishes_the_old_placeholder_contact_details(): void
    {
        // The previous site carried a placeholder phone number and an email on
        // a domain that does not match the site. Neither may reappear.
        foreach (['/en', '/en/about', '/en/contact', '/fr/contact'] as $path) {
            $this->get($path)
                ->assertDontSee('+237 670 000 000')
                ->assertDontSee('innovacm.com');
        }
    }
    public function test_each_about_band_carries_a_seam_so_a_new_section_registers(): void
    {
        \App\Models\CompanyValue::create(['title' => ['en' => 'Innovation First']]);
        \App\Models\Milestone::create(['year' => '2022', 'title' => ['en' => 'The spark']]);

        $settings = SiteSetting::instance();
        $settings->about_story = ['en' => '<p>Founded in Bamenda in 2022.</p>'];
        $settings->save();
        SiteSetting::forgetInstance();

        $html = $this->get('/en/about')->getContent();

        // Story, values and timeline each open with a seam. Counted by the
        // tick, because section-seam--warm contains section-seam and would
        // double count the warm band.
        $this->assertSame(3, substr_count($html, 'seam-tick'), 'Expected three seamed bands on About.');

        // One of them is the warm variant, so consecutive bands alternate
        // rather than repeating the same tint.
        $this->assertStringContainsString('section-seam--warm', $html);
        $this->assertStringContainsString('seam-tick', $html);
    }

    public function test_the_year_chip_uses_the_darker_orange_for_contrast(): void
    {
        \App\Models\Milestone::create(['year' => '2025', 'title' => ['en' => 'Scale']]);

        // White on accent is 3.50:1 and the chip is 18px semibold, which is
        // under the size WCAG treats as large text. accent-dark is 4.61:1.
        $html = $this->get('/en/about')->getContent();

        preg_match('/<span class="inline-flex items-center rounded-full[^"]*"/', $html, $chip);

        $this->assertNotEmpty($chip, 'No year chip rendered.');
        $this->assertStringContainsString('bg-accent-dark', $chip[0]);
    }
}
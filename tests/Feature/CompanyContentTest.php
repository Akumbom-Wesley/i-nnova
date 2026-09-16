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
}

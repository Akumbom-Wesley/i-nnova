<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\CaseStudy;
use App\Models\CompanyValue;
use App\Models\KickstarterTrack;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_with_no_content_at_all(): void
    {
        // Every section is conditional, so an empty database must still give
        // a complete page rather than a stack of empty headers.
        $this->get('/')->assertSuccessful();
    }

    public function test_it_shows_live_products_and_holds_back_unlaunched_ones(): void
    {
        Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'tagline' => ['en' => 'Comprehensive school management system'],
            'status' => ProductStatus::Live,
        ]);

        Product::create([
            'name' => ['en' => 'Unreleased Thing'],
            'slug' => 'unreleased-thing',
            'status' => ProductStatus::ComingSoon,
        ]);

        $response = $this->get('/');

        $response->assertSee('EduTrust Schools');
        $response->assertSee('Comprehensive school management system');

        // A coming-soon product may be named, but never as though it shipped.
        $response->assertSee('In development');
        $response->assertSee('Unreleased Thing');
        $response->assertDontSee('Unreleased Thing</h3>', false);
    }

    public function test_it_carries_the_stem_positioning(): void
    {
        $response = $this->get('/');

        $response->assertSee('Driven by');
        $response->assertSee('STEM');
        $response->assertSee('real world problems');
    }

    public function test_it_names_the_institutions_behind_deployments(): void
    {
        CaseStudy::create([
            'institution' => 'Sapientia Higher Institute of the Diocese of Kumba',
            'slug' => 'sahik',
            'summary' => ['en' => 'A summary of the deployment.'],
            'is_featured' => true,
        ]);

        $this->get('/')->assertSee('Sapientia Higher Institute of the Diocese of Kumba');
    }

    public function test_it_shows_values_team_tracks_and_testimonials(): void
    {
        CompanyValue::create(['title' => ['en' => 'Community Impact']]);
        TeamMember::create(['name' => 'Ada Example', 'slug' => 'ada-example', 'role' => ['en' => 'Engineer'], 'is_featured' => true]);
        KickstarterTrack::create(['name' => ['en' => 'Cybersecurity'], 'slug' => 'cybersecurity', 'is_active' => true]);
        Testimonial::create(['quote' => ['en' => 'It runs every day.'], 'person_name' => 'Grace Example', 'is_featured' => true]);

        $response = $this->get('/');

        $response->assertSee('Community Impact');
        $response->assertSee('Ada Example');
        $response->assertSee('Cybersecurity');
        $response->assertSee('It runs every day.');
    }

    public function test_it_uses_the_editable_hero_and_contact_details(): void
    {
        $settings = SiteSetting::instance();
        $settings->hero_heading = ['en' => 'A heading set in the admin'];
        $settings->contact_phone = '+237 671 008 494';
        $settings->save();

        SiteSetting::forgetInstance();

        $response = $this->get('/');

        $response->assertSee('A heading set in the admin');
        $response->assertSee('+237 671 008 494');
    }

    public function test_every_section_is_animated_but_degrades_without_motion(): void
    {
        Stat::create(['context' => Stat::CONTEXT_SITE, 'value' => '4', 'label' => ['en' => 'Products live']]);

        $html = $this->get('/')->getContent();

        // The reveal hooks the IntersectionObserver binds to.
        $this->assertStringContainsString('data-reveal', $html);

        // The tech motif animation classes.
        $this->assertStringContainsString('circuit-packet', $html);
        $this->assertStringContainsString('circuit-trace', $html);

        // Content is present in the markup, not injected by the animation,
        // so a reader with motion disabled loses nothing.
        $this->assertStringContainsString('Products live', $html);
    }
    public function test_rendering_a_page_leaves_no_output_buffer_open(): void
    {
        // Blade treats ('name', null) as the start of a block section
        // and calls ob_start() waiting for an  that never comes, so a
        // nullable model attribute passed straight in leaks a buffer per render.
        // Site settings start empty, which is exactly that case.
        $before = ob_get_level();

        $this->get('/')->assertSuccessful();

        $this->assertSame($before, ob_get_level(), 'Rendering the home page leaked an output buffer.');
    }

}

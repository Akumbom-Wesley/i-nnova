<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\CaseStudy;
use App\Models\CompanyValue;
use App\Models\KickstarterMentor;
use App\Models\KickstarterTrack;
use App\Models\ProcessStep;
use App\Models\Product;
use App\Models\Sector;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InnerPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [
            'products' => ['/products'],
            'work' => ['/work'],
            'about' => ['/about'],
            'kickstarter' => ['/kickstarter'],
            'contact' => ['/contact'],
        ];
    }

    #[DataProvider('pages')]
    public function test_each_page_renders_with_an_empty_database(string $path): void
    {
        // Every section is conditional, so a page with no content behind it
        // must still be a complete page rather than a stack of empty headers.
        $this->get($path)->assertSuccessful();
    }

    #[DataProvider('pages')]
    public function test_each_page_leaves_no_output_buffer_open(string $path): void
    {
        $before = ob_get_level();

        $this->get($path)->assertSuccessful();

        $this->assertSame($before, ob_get_level(), "Rendering {$path} leaked an output buffer.");
    }

    public function test_a_live_product_shows_its_detail(): void
    {
        $product = Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'tagline' => ['en' => 'Comprehensive school management system'],
            'description' => ['en' => '<p>How it works in detail.</p>'],
            'features' => ['en' => ['Timetabling', 'Results and reports']],
            'status' => ProductStatus::Live,
        ]);

        $response = $this->get("/products/{$product->slug}");

        $response->assertSee('Comprehensive school management system');
        $response->assertSee('How it works in detail.', false);
        $response->assertSee('Timetabling');
        $response->assertDontSee('Still in development');
    }

    public function test_an_unlaunched_product_is_held_back_rather_than_described(): void
    {
        $product = Product::create([
            'name' => ['en' => 'Not Shipped Yet'],
            'slug' => 'not-shipped-yet',
            'description' => ['en' => '<p>Detail that must not be shown as though it ships.</p>'],
            'status' => ProductStatus::ComingSoon,
            'launch_date' => '2027-03-01',
        ]);

        $response = $this->get("/products/{$product->slug}");

        $response->assertSee('Still in development');
        $response->assertSee('March 2027');
        $response->assertDontSee('Detail that must not be shown as though it ships.', false);
    }

    public function test_the_work_index_filters_by_sector(): void
    {
        $education = Sector::create(['name' => ['en' => 'Education'], 'slug' => 'education']);
        $health = Sector::create(['name' => ['en' => 'Health'], 'slug' => 'health']);

        CaseStudy::create(['institution' => 'A School', 'slug' => 'a-school', 'sector_id' => $education->id]);
        CaseStudy::create(['institution' => 'A Hospital', 'slug' => 'a-hospital', 'sector_id' => $health->id]);

        $this->get('/work')
            ->assertSee('A School')
            ->assertSee('A Hospital');

        $this->get('/work?sector=education')
            ->assertSee('A School')
            ->assertDontSee('A Hospital');
    }

    public function test_an_empty_sector_filter_says_so_instead_of_showing_nothing(): void
    {
        $health = Sector::create(['name' => ['en' => 'Health'], 'slug' => 'health']);
        CaseStudy::create(['institution' => 'A Hospital', 'slug' => 'a-hospital', 'sector_id' => $health->id]);

        $this->get('/work?sector=does-not-exist')
            ->assertSuccessful()
            ->assertSee('Nothing published in this sector yet.');
    }

    public function test_a_case_study_shows_its_story_and_links_to_the_product(): void
    {
        $product = Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'status' => ProductStatus::Live,
        ]);

        $caseStudy = CaseStudy::create([
            'institution' => 'Sapientia Higher Institute',
            'slug' => 'sahik',
            'product_id' => $product->id,
            'challenge' => ['en' => '<p>The challenge faced.</p>'],
            'solution' => ['en' => '<p>What we built.</p>'],
            'results' => ['en' => '<p>What changed.</p>'],
            'quote' => ['en' => 'It runs every day.'],
            'quote_attribution' => 'A Registrar',
        ]);

        $response = $this->get("/work/{$caseStudy->slug}");

        $response->assertSee('The challenge faced.', false);
        $response->assertSee('What we built.', false);
        $response->assertSee('What changed.', false);
        $response->assertSee('It runs every day.');
        $response->assertSee('A Registrar');
        $response->assertSee('EduTrust Schools');
    }

    public function test_about_groups_the_full_team_by_department(): void
    {
        CompanyValue::create(['title' => ['en' => 'Tech Excellence']]);
        ProcessStep::create(['title' => ['en' => 'Understand the day'], 'summary' => ['en' => 'We sit with the users first.']]);

        TeamMember::create(['name' => 'Ada Example', 'slug' => 'ada', 'role' => ['en' => 'Engineer'], 'department' => ['en' => 'Engineering']]);
        TeamMember::create(['name' => 'Grace Example', 'slug' => 'grace', 'role' => ['en' => 'Founder'], 'department' => ['en' => 'Leadership']]);

        $response = $this->get('/about');

        $response->assertSee('Tech Excellence');
        $response->assertSee('Understand the day');
        $response->assertSee('Engineering');
        $response->assertSee('Leadership');
        $response->assertSee('Ada Example');
        $response->assertSee('Grace Example');
    }

    public function test_a_team_member_with_no_department_still_appears(): void
    {
        TeamMember::create(['name' => 'Unassigned Person', 'slug' => 'unassigned', 'role' => ['en' => 'Engineer']]);

        $this->get('/about')->assertSee('Unassigned Person');
    }

    public function test_kickstarter_shows_tracks_and_mentors(): void
    {
        KickstarterTrack::create(['name' => ['en' => 'Cybersecurity'], 'slug' => 'cybersecurity', 'summary' => ['en' => 'Protect systems and data.'], 'is_active' => true]);
        KickstarterMentor::create(['name' => 'A Mentor', 'title' => ['en' => 'Lead Engineer']]);

        $response = $this->get('/kickstarter');

        $response->assertSee('Cybersecurity');
        $response->assertSee('Protect systems and data.');
        $response->assertSee('A Mentor');
        $response->assertSee('Career Capital Score');
    }
}

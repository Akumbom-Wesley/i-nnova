<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Smoke coverage for the admin. Every content resource must at least render
 * its index and create pages, which is what catches a mistyped component
 * namespace or a bad relationship option closure.
 */
class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public static function resourceRoutes(): array
    {
        return [
            'products' => ['products'],
            'team members' => ['team-members'],
            'values' => ['company-values'],
            'testimonials' => ['testimonials'],
            'wording' => ['site-texts'],
            'photography' => ['gallery-images'],
            'timeline' => ['milestones'],
            'how we work' => ['process-steps'],
            'kickstarter tracks' => ['kickstarter-tracks'],
            'kickstarter mentors' => ['kickstarter-mentors'],
            'alumni outcomes' => ['alumni-outcomes'],
            'stats' => ['stats'],
            'clients' => ['clients'],
            'partners' => ['partners'],
            'sectors' => ['sectors'],
            'leads' => ['leads'],
        ];
    }

    #[DataProvider('resourceRoutes')]
    public function test_resource_index_renders(string $slug): void
    {
        $this->actingAs(User::factory()->create())
            ->get("/admin/{$slug}")
            ->assertSuccessful();
    }

    #[DataProvider('resourceRoutes')]
    public function test_resource_create_renders(string $slug): void
    {
        if (in_array($slug, ['leads', 'site-texts'], true)) {
            // Leads arrive from the contact form and wording rows come from
            // scanning the templates. Neither is created by hand.
            $this->markTestSkipped("{$slug} are not created in the admin.");
        }

        $this->actingAs(User::factory()->create())
            ->get("/admin/{$slug}/create")
            ->assertSuccessful();
    }
}

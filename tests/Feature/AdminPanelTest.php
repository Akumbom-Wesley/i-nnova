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
            'case studies' => ['case-studies'],
            'team members' => ['team-members'],
            'values' => ['company-values'],
            'testimonials' => ['testimonials'],
            'photography' => ['gallery-images'],
            'timeline' => ['milestones'],
            'how we work' => ['process-steps'],
            'kickstarter tracks' => ['kickstarter-tracks'],
            'kickstarter mentors' => ['kickstarter-mentors'],
            'alumni outcomes' => ['alumni-outcomes'],
            'stats' => ['stats'],
            'clients' => ['clients'],
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
        if ($slug === 'leads') {
            // Leads arrive from the public contact form, never created by hand.
            $this->markTestSkipped('Leads are not created in the admin.');
        }

        $this->actingAs(User::factory()->create())
            ->get("/admin/{$slug}/create")
            ->assertSuccessful();
    }
}

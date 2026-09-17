<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_dashboard_renders(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSuccessful();
    }

    public function test_it_shows_the_content_overview(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Products live')
            ->assertSee('Unread enquiries');
    }

    public function test_it_lists_recent_enquiries(): void
    {
        Lead::create([
            'name' => 'Ada Example',
            'email' => 'ada@example.com',
            'message' => 'We are looking at replacing our student records system.',
        ]);

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Latest enquiries')
            ->assertSee('Ada Example');
    }
    public function test_the_launch_checklist_lists_what_is_outstanding(): void
    {
        \App\Models\Client::create(['name' => 'Unconfirmed Institution', 'is_verified' => false]);

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Before launch')
            ->assertSee('Clients awaiting verification');
    }

    public function test_the_checklist_says_so_when_nothing_is_outstanding(): void
    {
        // Contact details are the only check that trips on an empty database,
        // so filling them clears the board.
        $settings = \App\Models\SiteSetting::instance();
        $settings->fill([
            'contact_email' => 'contact@example.com',
            'contact_phone' => '+237 671 008 494',
            'whatsapp_number' => '+237671008494',
            'address' => ['en' => 'Bamenda'],
        ])->save();

        \App\Models\SiteSetting::forgetInstance();

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Nothing outstanding');
    }

    public function test_an_item_disappears_once_it_is_done(): void
    {
        $client = \App\Models\Client::create(['name' => 'Pending', 'is_verified' => false]);

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Clients awaiting verification');

        $client->update(['is_verified' => true]);

        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertDontSee('Clients awaiting verification');
    }

    public function test_it_charts_enquiries_over_the_year(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertSee('Enquiries over the past year');
    }
}
<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * One order, two pages.
 *
 * The home page shows the executives and the About page shows everyone, but
 * both read the same order set in the admin. These guard the thing that would
 * be easy to break: a second control creeping back in, so that dragging a row
 * changes one page and not the other.
 */
class TeamOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_shows_only_the_leadership_count(): void
    {
        $this->makeTeam(7);

        $html = $this->get('/en')->getContent();

        $this->assertStringContainsString('Person 1', $html);
        $this->assertStringContainsString('Person 3', $html);
        $this->assertStringNotContainsString('Person 4', $html);
    }

    public function test_the_home_page_takes_the_first_three_by_order(): void
    {
        $this->makeTeam(5);

        // Promote the fifth to the top. Nothing else changes.
        TeamMember::query()->where('name', 'Person 5')->update(['sort_order' => -1]);

        $html = $this->get('/en')->getContent();

        $this->assertStringContainsString('Person 5', $html);

        // Which pushes the old third off the end.
        $this->assertStringNotContainsString('Person 3', $html);
    }

    public function test_the_order_on_the_home_page_is_the_order_shown(): void
    {
        $this->makeTeam(3);

        TeamMember::query()->where('name', 'Person 3')->update(['sort_order' => 0]);
        TeamMember::query()->where('name', 'Person 1')->update(['sort_order' => 2]);

        $html = $this->get('/en')->getContent();

        $this->assertLessThan(
            strpos($html, 'Person 1'),
            strpos($html, 'Person 3'),
            'The home page should list people in the order set in the admin.',
        );
    }

    public function test_the_about_page_shows_everyone_in_the_same_order(): void
    {
        $this->makeTeam(7);

        $html = $this->get('/en/about')->getContent();

        // Everyone, not just the three.
        $this->assertStringContainsString('Person 7', $html);

        $this->assertLessThan(
            strpos($html, 'Person 4'),
            strpos($html, 'Person 2'),
            'The About page should use the same order as the home page.',
        );
    }

    public function test_one_order_drives_both_pages(): void
    {
        $this->makeTeam(5);

        // The single edit an admin would make.
        TeamMember::query()->where('name', 'Person 4')->update(['sort_order' => -5]);

        $home = $this->get('/en')->getContent();
        $about = $this->get('/en/about')->getContent();

        // It reaches the home page,
        $this->assertStringContainsString('Person 4', $home);

        // and it leads on the About page too, rather than the two disagreeing.
        $this->assertLessThan(
            strpos($about, 'Person 1'),
            strpos($about, 'Person 4'),
            'Reordering should move someone on both pages, not just one.',
        );
    }

    public function test_the_section_is_about_leadership_not_a_sample(): void
    {
        $this->makeTeam(5);

        $this->get('/en')
            ->assertSee('Leadership')
            ->assertSee('The people who run it')
            // And still offers the way through to everyone else.
            ->assertSee('Meet the full team');
    }

    public function test_a_team_smaller_than_the_limit_still_renders(): void
    {
        $this->makeTeam(2);

        $this->get('/en')
            ->assertSuccessful()
            ->assertSee('Person 1')
            ->assertSee('Person 2');
    }

    private function makeTeam(int $count): void
    {
        foreach (range(1, $count) as $index) {
            TeamMember::create([
                'name' => "Person {$index}",
                'slug' => "person-{$index}",
                'role' => ['en' => 'Director'],
                'sort_order' => $index,
            ]);
        }
    }
}

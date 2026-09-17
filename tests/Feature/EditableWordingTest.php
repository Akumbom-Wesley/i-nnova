<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\SiteText;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Fixed strings in the templates are editable in the admin. The language files
 * remain the defaults, so an untouched or cleared string still renders.
 */
class EditableWordingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        SiteText::forgetCache();

        // The timeline section is conditional, and its heading is the string
        // these tests exercise, so it needs something to render.
        Milestone::create(['year' => '2022', 'title' => ['en' => 'The spark']]);
    }

    public function test_the_site_renders_with_no_rows_at_all(): void
    {
        // The templates have to work before anything is synced, and on a fresh
        // clone where the table may be empty.
        $this->assertSame(0, SiteText::count());

        $this->get('/en/about')
            ->assertSuccessful()
            ->assertSee('How we got here');
    }

    public function test_an_override_reaches_the_page(): void
    {
        SiteText::create([
            'key' => 'How we got here',
            'group' => 'Section: Timeline',
            'value' => ['en' => 'The road so far'],
        ]);

        $this->get('/en/about')
            ->assertSee('The road so far')
            ->assertDontSee('How we got here');
    }

    public function test_an_override_is_per_language(): void
    {
        SiteText::create([
            'key' => 'How we got here',
            'value' => ['en' => 'The road so far'],
        ]);

        // French had no override, so it keeps the language file wording.
        $this->get('/fr/about')->assertSee('Comment nous en sommes arrives la');
        $this->get('/en/about')->assertSee('The road so far');
    }

    public function test_clearing_a_value_falls_back_rather_than_blanking_the_site(): void
    {
        $text = SiteText::create([
            'key' => 'How we got here',
            'value' => ['en' => 'The road so far'],
        ]);

        $this->get('/en/about')->assertSee('The road so far');

        $text->update(['value' => ['en' => '']]);
        SiteText::forgetCache();

        $this->get('/en/about')->assertSee('How we got here');
    }

    public function test_saving_clears_the_cache_so_an_edit_shows_immediately(): void
    {
        $text = SiteText::create(['key' => 'How we got here', 'value' => ['en' => 'First wording']]);

        $this->get('/en/about')->assertSee('First wording');

        // No manual cache clearing: the model does it on save.
        $text->update(['value' => ['en' => 'Second wording']]);

        $this->get('/en/about')
            ->assertSee('Second wording')
            ->assertDontSee('First wording');
    }

    public function test_the_sync_command_collects_every_string_and_groups_it(): void
    {
        Artisan::call('site:sync-texts');

        $this->assertGreaterThan(100, SiteText::count());

        // Grouped by where it appears, not dumped in one list.
        $this->assertGreaterThan(5, SiteText::query()->distinct()->count('group'));

        $timeline = SiteText::where('key', 'How we got here')->sole();
        $this->assertSame('Section: Timeline', $timeline->group);

        // English has no language file, so the key seeds its own value.
        $this->assertSame('How we got here', $timeline->getTranslation('value', 'en', false));
        $this->assertSame('Comment nous en sommes arrives la', $timeline->getTranslation('value', 'fr', false));
    }

    public function test_the_sync_command_does_not_overwrite_an_edit(): void
    {
        SiteText::create(['key' => 'How we got here', 'value' => ['en' => 'Already edited']]);

        Artisan::call('site:sync-texts');

        $this->assertSame(
            'Already edited',
            SiteText::where('key', 'How we got here')->sole()->getTranslation('value', 'en', false),
        );
    }

    public function test_the_admin_lists_the_wording_but_offers_no_create(): void
    {
        Artisan::call('site:sync-texts');

        $this->actingAs(User::factory()->create())
            ->get('/admin/site-texts')
            ->assertSuccessful();

        // Not asserting a particular row on the first page: the list is
        // alphabetical and paginated, so which rows land on page one is a
        // detail that would make this brittle.
        $this->assertTrue(SiteText::where('key', 'How we got here')->exists());

        $this->assertFalse(\App\Filament\Resources\SiteTexts\SiteTextResource::canCreate());
    }
}

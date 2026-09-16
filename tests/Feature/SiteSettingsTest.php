<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSiteSettings;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_page_renders(): void
    {
        $this->get('/admin/manage-site-settings')->assertSuccessful();
    }

    public function test_it_keeps_a_single_row(): void
    {
        SiteSetting::instance();
        SiteSetting::instance();

        $this->assertSame(1, SiteSetting::query()->count());
    }

    public function test_it_saves_translated_settings(): void
    {
        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'hero_heading' => ['en' => 'Built in Bamenda', 'fr' => 'Construit a Bamenda'],
                'contact_email' => 'hello@example.com',
                'socials' => ['linkedin' => 'https://linkedin.com/company/example'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = SiteSetting::instance()->refresh();

        $this->assertSame('Built in Bamenda', $settings->getTranslation('hero_heading', 'en'));
        $this->assertSame('Construit a Bamenda', $settings->getTranslation('hero_heading', 'fr'));
        $this->assertSame('hello@example.com', $settings->contact_email);
        $this->assertSame('https://linkedin.com/company/example', $settings->socials['linkedin']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_root_redirects_to_the_default_locale(): void
    {
        $this->get('/')->assertRedirect('/en');
    }

    public function test_an_unprefixed_path_redirects_while_keeping_the_path(): void
    {
        $this->get('/products')->assertRedirect('/en/products');
        $this->get('/work/something')->assertRedirect('/en/work/something');
    }

    public function test_both_locales_serve(): void
    {
        $this->get('/en')->assertSuccessful();
        $this->get('/fr')->assertSuccessful();
    }

    public function test_an_unknown_locale_is_not_treated_as_one(): void
    {
        // "de" is not configured, so it is a path to redirect, not a locale.
        $this->get('/de')->assertRedirect('/en/de');
    }

    public function test_static_strings_follow_the_locale(): void
    {
        $this->get('/en')->assertSee('Our solutions');
        $this->get('/fr')->assertSee('Nos solutions');
    }

    public function test_translated_content_follows_the_locale(): void
    {
        Product::create([
            'name' => ['en' => 'EduTrust Schools', 'fr' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'tagline' => [
                'en' => 'Comprehensive school management system',
                'fr' => 'Systeme complet de gestion scolaire',
            ],
            'status' => ProductStatus::Live,
        ]);

        $this->get('/en/products')->assertSee('Comprehensive school management system');
        $this->get('/fr/products')->assertSee('Systeme complet de gestion scolaire');
    }

    public function test_the_switcher_offers_the_same_page_in_the_other_language(): void
    {
        Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'status' => ProductStatus::Live,
        ]);

        // Switching languages must not dump the reader back on the home page.
        $this->get('/en/products/edutrust-schools')
            ->assertSee('/fr/products/edutrust-schools', false);

        $this->get('/fr/products/edutrust-schools')
            ->assertSee('/en/products/edutrust-schools', false);
    }

    public function test_the_html_lang_attribute_matches(): void
    {
        $this->get('/en')->assertSee('<html lang="en"', false);
        $this->get('/fr')->assertSee('<html lang="fr"', false);
    }

    public function test_route_model_binding_survives_the_locale_prefix(): void
    {
        // The locale is dropped from the route parameters in middleware. If it
        // were not, it would arrive as the controller's first argument and
        // shift the model binding along by one.
        Product::create([
            'name' => ['en' => 'EduTrust Schools'],
            'slug' => 'edutrust-schools',
            'status' => ProductStatus::Live,
        ]);

        $this->get('/en/products/edutrust-schools')->assertSuccessful();
        $this->get('/fr/products/edutrust-schools')->assertSuccessful();
        $this->get('/en/products/no-such-product')->assertNotFound();
    }
}

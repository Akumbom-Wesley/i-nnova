<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Models\Product;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * The locale tabs bind straight to Spatie's translation array rather than
 * going through a plugin, so this proves the round trip: a form field named
 * "name.fr" must land in the French translation and come back into the form.
 */
class TranslatableContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_it_creates_a_product_in_both_locales(): void
    {
        $sector = Sector::create([
            'name' => ['en' => 'Education', 'fr' => 'Education'],
            'slug' => 'education',
        ]);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => ['en' => 'SAHIK', 'fr' => 'SAHIK'],
                'slug' => 'sahik',
                'tagline' => ['en' => 'School management', 'fr' => 'Gestion scolaire'],
                'features' => ['en' => ['Timetables', 'Results'], 'fr' => ['Emplois du temps', 'Resultats']],
                'sector_id' => $sector->id,
                'status' => ProductStatus::Live->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::query()->where('slug', 'sahik')->firstOrFail();

        $this->assertSame('School management', $product->getTranslation('tagline', 'en'));
        $this->assertSame('Gestion scolaire', $product->getTranslation('tagline', 'fr'));
        $this->assertSame(['Emplois du temps', 'Resultats'], $product->getTranslation('features', 'fr'));
        $this->assertTrue($product->isLive());
        $this->assertSame($sector->id, $product->sector_id);
    }

    public function test_it_fills_the_edit_form_from_both_locales(): void
    {
        $product = Product::create([
            'name' => ['en' => 'PAXHI', 'fr' => 'PAXHI'],
            'slug' => 'paxhi',
            'tagline' => ['en' => 'Hotel operations', 'fr' => 'Gestion hoteliere'],
            'status' => ProductStatus::Live,
        ]);

        Livewire::test(EditProduct::class, ['record' => $product->getKey()])
            ->assertFormSet([
                'tagline' => ['en' => 'Hotel operations', 'fr' => 'Gestion hoteliere'],
            ]);
    }

    public function test_the_default_locale_name_is_required(): void
    {
        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => ['en' => '', 'fr' => 'Quelque chose'],
                'slug' => 'no-english-name',
            ])
            ->call('create')
            ->assertHasFormErrors(['name.en' => 'required']);
    }
}

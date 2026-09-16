<?php

namespace Tests\Feature;

use App\Enums\PartnerLockup;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PartnersTest extends TestCase
{
    use RefreshDatabase;

    private function partner(array $attributes = []): Partner
    {
        return Partner::create(array_merge([
            'name' => 'A Real Partner',
            'relationship' => ['en' => 'Education partner'],
            'is_verified' => true,
            'is_featured' => false,
        ], $attributes));
    }

    public static function pages(): array
    {
        return [
            'home' => ['/en'],
            'about' => ['/en/about'],
        ];
    }

    #[DataProvider('pages')]
    public function test_the_section_is_absent_until_a_partnership_is_confirmed(string $path): void
    {
        // The locked decision was to remove partner logos because the
        // relationships were not real. Nothing appears until somebody
        // confirms one.
        $this->partner(['is_verified' => false]);

        $this->get($path)
            ->assertSuccessful()
            ->assertDontSee('Organisations we build alongside')
            ->assertDontSee('A Real Partner');
    }

    #[DataProvider('pages')]
    public function test_a_confirmed_partner_appears(string $path): void
    {
        $this->partner();

        $this->get($path)
            ->assertSee('Organisations we build alongside')
            ->assertSee('A Real Partner');
    }

    public function test_the_wall_refuses_an_unconfirmed_partner_handed_straight_to_it(): void
    {
        $this->partner(['name' => 'Confirmed Partner']);
        $this->partner(['name' => 'Unconfirmed Partner', 'is_verified' => false]);

        // Deliberately passing every partner to prove the component filters
        // rather than trusting its caller.
        $html = Blade::render(
            '<x-ui.partner-wall :partners="$partners" />',
            ['partners' => Partner::all()],
        );

        $this->assertStringContainsString('Confirmed Partner', $html);
        $this->assertStringNotContainsString('Unconfirmed Partner', $html);
    }

    public function test_a_pairing_uses_the_logo_carrying_the_trademark_name(): void
    {
        // Brand guide, Section 4: "Don't pair the stand alone logo before
        // adding the partner organization. We should always use the logo with
        // the trademark name."
        $partner = $this->partner(['is_featured' => true]);

        $html = Blade::render(
            '<x-brand.partner-lockup :partner="$partner" />',
            ['partner' => $partner],
        );

        $this->assertStringContainsString('images/logo.png', $html);
        $this->assertStringNotContainsString('logo-mark.png', $html);
    }

    public function test_both_pairing_arrangements_from_the_guide_are_available(): void
    {
        foreach ([PartnerLockup::Horizontal, PartnerLockup::Vertical] as $lockup) {
            $partner = $this->partner(['name' => 'Partner ' . $lockup->value, 'lockup' => $lockup]);

            $html = Blade::render(
                '<x-brand.partner-lockup :partner="$partner" />',
                ['partner' => $partner],
            );

            $this->assertStringContainsString(
                $lockup === PartnerLockup::Vertical ? 'flex-col' : 'flex-row',
                $html,
            );
        }
    }

    public function test_a_featured_partner_gets_the_pairing_and_the_rest_get_the_row(): void
    {
        $this->partner(['name' => 'Featured Partner', 'is_featured' => true]);
        $this->partner(['name' => 'Row Partner', 'sort_order' => 1]);

        $html = $this->get('/en')->getContent();

        $this->assertStringContainsString('Featured Partner', $html);
        $this->assertStringContainsString('Row Partner', $html);

        // Only the featured one brings our own logo into a pairing. Counted
        // as an image tag, because the Organization schema also names the
        // logo file and that is not a pairing.
        preg_match_all('/<img [^>]*images\/logo\.png[^>]*>/', preg_replace('/\s+/', ' ', $html), $matches);

        $this->assertCount(1, $matches[0]);
    }

    public function test_the_admin_counts_partnerships_awaiting_confirmation(): void
    {
        $this->partner(['name' => 'Confirmed']);
        $this->partner(['name' => 'Waiting One', 'is_verified' => false]);
        $this->partner(['name' => 'Waiting Two', 'is_verified' => false]);

        $this->actingAs(User::factory()->create())
            ->get('/admin/partners')
            ->assertSuccessful();

        $this->assertSame('2', \App\Filament\Resources\Partners\PartnerResource::getNavigationBadge());
    }
}

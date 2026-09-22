<?php

namespace Tests\Feature;

use App\Enums\PartnerLockup;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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
            ->assertDontSee('id="partners"', false)
            ->assertDontSee('A Real Partner');
    }

    #[DataProvider('pages')]
    public function test_a_confirmed_partner_appears(string $path): void
    {
        $this->partner();

        $this->get($path)
            ->assertSee('id="partners"', false)
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

    public function test_a_partner_is_a_mark_and_nothing_else(): void
    {
        // A partner has a website on the record, and it is deliberately not
        // rendered. The band is a row of marks; a link under one of them makes
        // that partner look more important than the rest of the row.
        $this->partner(['name' => 'A Partner', 'website_url' => 'https://partner.example']);

        $html = $this->get('/en')->getContent();

        $this->assertStringContainsString('A Partner', $html);
        $this->assertStringNotContainsString('https://partner.example', $html);
    }

    public function test_the_band_is_partners_only(): void
    {
        // Clients used to share this band. They have their own places now, and
        // a client appearing here again would be the merge being undone.
        $this->partner(['name' => 'A Partner']);

        \App\Models\Client::create([
            'name' => 'A Client Institution',
            'is_verified' => true,
            'is_featured' => false,
        ]);

        $html = $this->get('/en')->getContent();

        $band = substr($html, (int) strpos($html, 'id="partners"'));

        $this->assertStringContainsString('A Partner', $band);
        $this->assertStringNotContainsString('A Client Institution', $band);
    }

    public function test_an_unconfirmed_client_is_not_published_either(): void
    {
        \App\Models\Client::create([
            'name' => 'Unconfirmed Client',
            'website_url' => 'https://nope.example',
            'is_verified' => false,
        ]);

        $this->get('/en')
            ->assertDontSee('Unconfirmed Client')
            ->assertDontSee('https://nope.example');
    }

    public function test_the_wall_artwork_is_large_enough_for_the_size_it_renders(): void
    {
        // The band was made much bigger, and artwork sized for the old row is
        // soft at the new one. This is the guard on that: shrink the
        // conversion back and the logos go blurry on the site, which is the
        // sort of thing nobody notices until it is live.
        $partner = $this->partner();

        $partner->addMedia(UploadedFile::fake()->image('logo.png', 1600, 800))
            ->toMediaCollection('logo');

        $conversion = $partner->getFirstMedia('logo')->getPath('wall');

        $this->assertFileExists($conversion);

        [$width, $height] = getimagesize($conversion);

        // The mark is 112px tall on a large screen, and a phone with a 3x
        // display asks for every one of those pixels.
        $this->assertGreaterThanOrEqual(336, $height, 'The wall conversion is too small for the size the mark renders at.');
        $this->assertGreaterThanOrEqual(336, $width);
    }

    public function test_a_logo_is_shown_as_supplied_rather_than_waiting_for_a_hover(): void
    {
        // The first version held every mark to one tone and restored the real
        // logo on hover. With monochrome artwork that looks tidy; with a logo
        // that carries its own background it is a grey rectangle until the
        // reader happens to point at it.
        $partner = $this->partner();
        $partner->addMedia(UploadedFile::fake()->image('logo.png', 1200, 600))
            ->toMediaCollection('logo');

        $html = $this->get('/en')->getContent();

        $this->assertStringNotContainsString('brightness-0', $html);
        $this->assertStringNotContainsString('invert', $html);
    }

    public function test_the_duplicate_row_is_hidden_from_assistive_technology(): void
    {
        // A marquee needs a second copy of the row to loop without a seam.
        // That copy is decoration, and without this a screen reader reads
        // every partner twice as the price of a visual trick.
        $this->partner(['name' => 'A Real Partner']);

        $html = $this->get('/en')->getContent();

        preg_match_all('/<ul[^>]*class="partner-list"[^>]*>/', $html, $lists);

        $this->assertCount(2, $lists[0], 'The wall should draw the row twice so the loop has no seam.');

        $hidden = array_filter($lists[0], fn (string $tag) => str_contains($tag, 'aria-hidden="true"'));

        $this->assertCount(1, $hidden, 'Exactly one of the two rows is real; the other must be hidden from assistive technology.');

        // Both copies carry the name, which is the point: one is read, one is
        // only looked at.
        $this->assertSame(2, substr_count($html, 'A Real Partner'));
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

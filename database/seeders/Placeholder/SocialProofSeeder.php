<?php

namespace Database\Seeders\Placeholder;

use App\Enums\PartnerLockup;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Testimonial;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Testimonials and client logos.
 */
class SocialProofSeeder extends Seeder
{
    use AttachesPlaceholderImages;

    public function run(): void
    {
        $this->testimonials();
        $this->clients();
        $this->partners();
    }

    private function testimonials(): void
    {
        $rows = [
            ['person' => 'Placeholder Speaker A', 'organisation' => 'Placeholder Hotel Group', 'product' => 'paxhi'],
            ['person' => 'Placeholder Speaker B', 'organisation' => 'Placeholder University', 'product' => 'sahik'],
        ];

        foreach ($rows as $order => $row) {
            $testimonial = Testimonial::updateOrCreate(
                ['person_name' => $row['person']],
                [
                    'quote' => [
                        'en' => 'Placeholder testimonial. Replaced with a real, attributable quote in Sprint 5.',
                        'fr' => 'Temoignage provisoire. Remplace par une vraie citation attribuable au Sprint 5.',
                    ],
                    'person_role' => ['en' => 'Placeholder role', 'fr' => 'Role provisoire'],
                    'organisation' => $row['organisation'],
                    'product_id' => Product::query()->where('slug', $row['product'])->value('id'),
                    'sort_order' => $order,
                    'is_featured' => true,
                ],
            );

            $this->attachImage($testimonial, 'photo', $row['person'], 600, 600, 'ink');
        }
    }

    private function clients(): void
    {
        // Seeded unverified on purpose. Client::verified() is what the front
        // end reads, so placeholder logos cannot reach a page by accident.
        $names = ['Placeholder Institution A', 'Placeholder Institution B', 'Placeholder Institution C'];

        foreach ($names as $order => $name) {
            $client = Client::updateOrCreate(
                ['name' => $name],
                ['is_verified' => false, 'sort_order' => $order],
            );

            $this->attachImage($client, 'logo', $name, 800, 400, 'paper');
        }
    }

    /**
     * Partner organisations.
     *
     * Seeded unconfirmed on purpose, so the admin has rows to look at and the
     * arrangement can be tried, while nothing reaches a page. The previous
     * site published partner logos for relationships that did not exist, and
     * the locked decision was to remove them. These become visible only when
     * somebody ticks "confirmed partnership" on a real one.
     */
    private function partners(): void
    {
        $rows = [
            ['Unconfirmed Partner A', 'Education partner', false, PartnerLockup::Horizontal],
            ['Unconfirmed Partner B', 'Technology partner', false, PartnerLockup::Horizontal],
            ['Unconfirmed Partner C', 'Training partner', false, PartnerLockup::Horizontal],
            ['Unconfirmed Partner D', 'Community partner', true, PartnerLockup::Horizontal],
            ['Unconfirmed Partner E', 'Institutional partner', true, PartnerLockup::Vertical],
        ];

        foreach ($rows as $order => [$name, $relationship, $featured, $lockup]) {
            $partner = Partner::updateOrCreate(
                ['name' => $name],
                [
                    'relationship' => ['en' => $relationship, 'fr' => $relationship],
                    'lockup' => $lockup,
                    'is_featured' => $featured,
                    'is_verified' => false,
                    'sort_order' => $order,
                ],
            );

            $this->attachImage($partner, 'logo', $name, 480, 240, 'paper');
        }
    }
}

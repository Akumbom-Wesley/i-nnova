<?php

namespace Database\Seeders\Placeholder;

use App\Enums\ProductStatus;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sector;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Sectors, products and case studies.
 *
 * The product line and their one-line descriptions come from the company
 * roll-up, so the names and positioning are real. The long-form copy is still
 * placeholder and Sprint 5 replaces it.
 *
 * PAXHI and SAHIK are clients, not products. SAHIK is Sapientia
 * Higher Institute of the Diocese of Kumba. Both are seeded as case studies
 * carrying their own crest.
 */
class CatalogueSeeder extends Seeder
{
    use AttachesPlaceholderImages;

    public function run(): void
    {
        $sectors = $this->sectors();
        $products = $this->products($sectors);

        $this->clients($sectors, $products);
    }

    /** @return array<string, Sector> */
    private function sectors(): array
    {
        $rows = [
            'education' => ['en' => 'Education', 'fr' => 'Education'],
            'health' => ['en' => 'Health', 'fr' => 'Sante'],
            'hospitality' => ['en' => 'Hospitality', 'fr' => 'Hotellerie'],
            'retail' => ['en' => 'Retail', 'fr' => 'Commerce'],
        ];

        $sectors = [];
        $order = 0;

        foreach ($rows as $slug => $name) {
            $sectors[$slug] = Sector::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'sort_order' => $order++],
            );
        }

        return $sectors;
    }

    /**
     * @param  array<string, Sector>  $sectors
     * @return array<string, Product>
     */
    private function products(array $sectors): array
    {
        // The roll-up is the source of truth for the product line: these are
        // the four solutions it names, plus the custom work it calls "and
        // more". Nothing here comes from the older website.
        $rows = [
            [
                'slug' => 'innova-pos',
                'sector' => 'retail',
                'name' => ['en' => 'I-NNOVA POS', 'fr' => 'I-NNOVA POS'],
                'tagline' => [
                    'en' => 'Smart POS for modern businesses',
                    'fr' => 'Un point de vente intelligent pour les entreprises modernes',
                ],
                'features' => [
                    'en' => ['Till and receipts', 'Inventory management', 'Analytics', 'Multi location'],
                    'fr' => ['Caisse et recus', 'Gestion de stock', 'Analyses', 'Multi sites'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'integrated-hospital-software',
                'sector' => 'health',
                'name' => ['en' => 'I-NNOVA Integrated Hospital Software', 'fr' => 'Logiciel Hospitalier Integre I-NNOVA'],
                'tagline' => [
                    'en' => 'Complete solution for hospital management',
                    'fr' => 'Solution complete pour la gestion hospitaliere',
                ],
                'features' => [
                    'en' => ['Patient records', 'Consultations and wards', 'Pharmacy and stock', 'Billing and insurance'],
                    'fr' => ['Dossiers patients', 'Consultations et services', 'Pharmacie et stock', 'Facturation et assurance'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'edutrust-schools',
                'sector' => 'education',
                'name' => ['en' => 'EduTrust Schools', 'fr' => 'EduTrust Schools'],
                'tagline' => [
                    'en' => 'Comprehensive school management system',
                    'fr' => 'Systeme complet de gestion scolaire',
                ],
                'features' => [
                    'en' => ['Enrolment and records', 'Timetabling', 'Results and reports', 'Parent messaging'],
                    'fr' => ['Inscriptions et dossiers', 'Emplois du temps', 'Resultats et bulletins', 'Messagerie parents'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'hotel-booking-system',
                'sector' => 'hospitality',
                'name' => ['en' => 'Hotel Booking System', 'fr' => 'Systeme de Reservation Hoteliere'],
                'tagline' => [
                    'en' => 'Smart booking made simple',
                    'fr' => 'La reservation intelligente, simplifiee',
                ],
                'features' => [
                    'en' => ['Rooms and availability', 'Reservations', 'Billing and receipts', 'Housekeeping boards'],
                    'fr' => ['Chambres et disponibilite', 'Reservations', 'Facturation et recus', 'Tableaux de menage'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'custom-development',
                'sector' => 'retail',
                'name' => ['en' => 'Custom Development', 'fr' => 'Developpement sur mesure'],
                'tagline' => [
                    'en' => 'Custom solutions for your unique needs',
                    'fr' => 'Des solutions sur mesure pour vos besoins particuliers',
                ],
                'features' => [
                    'en' => ['Discovery and scoping', 'Build and deploy', 'Training', 'Ongoing support'],
                    'fr' => ['Cadrage', 'Developpement et deploiement', 'Formation', 'Support continu'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => false,
            ],
        ];

        $products = [];
        $order = 0;

        foreach ($rows as $row) {
            $slug = $row['slug'];
            $sectorKey = $row['sector'];
            unset($row['slug'], $row['sector']);

            $product = Product::updateOrCreate(['slug' => $slug], $row + [
                'sector_id' => $sectors[$sectorKey]->id,
                'description' => [
                    'en' => '<p>Placeholder description. Replaced with real copy in Sprint 5.</p>',
                    'fr' => '<p>Description provisoire. Remplacee par le vrai texte au Sprint 5.</p>',
                ],
                'sort_order' => $order++,
            ]);

            $label = $product->getTranslation('name', 'en');

            $this->attachImage($product, 'logo', $label, 600, 600);
            $this->attachImage($product, 'cover', $label, 1600, 900);
            $this->attachImage($product, 'screenshots', $label . ' dashboard', 1440, 900, 'ink');
            $this->attachImage($product, 'screenshots', $label . ' detail', 1440, 900, 'ink');

            $products[$slug] = $product;
        }

        return $products;
    }

    /**
     * @param  array<string, Sector>  $sectors
     * @param  array<string, Product>  $products
     */
    /**
     * PAXHI and SAHIK are clients running our software, which is all a case
     * study ever was. They are seeded verified, unlike the placeholder logos
     * in SocialProofSeeder, because these two are real relationships.
     */
    private function clients(array $sectors, array $products): void
    {
        $rows = [
            [
                'slug' => 'sahik',
                'name' => 'Sapientia Higher Institute of the Diocese of Kumba',
                'short' => 'SAHIK',
                'sector' => 'education',
                'products' => ['edutrust-schools'],
                'logo' => 'sahik.png',
            ],
            [
                'slug' => 'paxhi',
                'name' => 'PAXHI',
                'short' => 'PAXHI',
                'sector' => null,
                'products' => [],
                'logo' => 'paxhi.png',
            ],
        ];

        foreach ($rows as $order => $row) {
            $client = Client::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'sector_id' => $row['sector'] ? $sectors[$row['sector']]->id : null,
                    'summary' => [
                        'en' => 'Placeholder summary of the deployment, one paragraph long.',
                        'fr' => 'Resume provisoire du deploiement, un paragraphe.',
                    ],
                    'challenge' => ['en' => '<p>Placeholder challenge.</p>', 'fr' => '<p>Defi provisoire.</p>'],
                    'solution' => ['en' => '<p>Placeholder solution.</p>', 'fr' => '<p>Solution provisoire.</p>'],
                    'results' => ['en' => '<p>Placeholder results.</p>', 'fr' => '<p>Resultats provisoires.</p>'],
                    'quote' => ['en' => 'Placeholder quote from the institution.', 'fr' => 'Citation provisoire.'],
                    'quote_attribution' => 'Placeholder Name',
                    'quote_role' => ['en' => 'Placeholder role', 'fr' => 'Role provisoire'],
                    'sort_order' => $order,
                    'is_verified' => true,
                    'is_featured' => true,
                ],
            );

            $client->products()->sync(
                collect($row['products'])->map(fn (string $slug): int => $products[$slug]->id)->all(),
            );

            // The real crest, not a generated stand-in.
            $this->attachFile($client, 'logo', public_path('images/' . $row['logo']), $row['short']);

            $this->attachImage($client, 'cover', $row['short'], 1600, 900);
        }
    }
}

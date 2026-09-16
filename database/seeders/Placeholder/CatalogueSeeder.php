<?php

namespace Database\Seeders\Placeholder;

use App\Enums\ProductStatus;
use App\Models\CaseStudy;
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
 * PAXHI and SAHIK are client institutions, not products. SAHIK is Sapientia
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

        $this->caseStudies($sectors, $products);
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
        // The union of the product line on the company's own site and the one
        // on the roll-up. Where the two disagree on a name, the site's is used
        // because that is what customers have already seen.
        $rows = [
            [
                'slug' => 'innova-marketplace',
                'sector' => 'retail',
                'name' => ['en' => 'INNOVA Marketplace', 'fr' => 'INNOVA Marketplace'],
                'tagline' => [
                    'en' => 'Multi-vendor e-commerce built for Cameroonian and African markets',
                    'fr' => 'Commerce en ligne multi-vendeurs concu pour les marches camerounais et africains',
                ],
                'features' => [
                    'en' => ['Multi vendor storefronts', 'Local payment methods', 'Orders and fulfilment', 'Seller analytics'],
                    'fr' => ['Boutiques multi-vendeurs', 'Moyens de paiement locaux', 'Commandes et livraison', 'Analyses vendeur'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'innova-pos',
                'sector' => 'retail',
                'name' => ['en' => 'INNOVA POS', 'fr' => 'INNOVA POS'],
                'tagline' => [
                    'en' => 'Cloud point of sale with inventory, analytics and multi-location support',
                    'fr' => 'Point de vente cloud avec stock, analyses et gestion multi-sites',
                ],
                'features' => [
                    'en' => ['Till and receipts', 'Inventory management', 'Analytics', 'Multi location'],
                    'fr' => ['Caisse et recus', 'Gestion de stock', 'Analyses', 'Multi sites'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'innova-school',
                'sector' => 'education',
                'name' => ['en' => 'INNOVA School', 'fr' => 'INNOVA School'],
                'tagline' => [
                    'en' => 'Comprehensive school management, from enrolment to results',
                    'fr' => 'Gestion scolaire complete, de l inscription aux resultats',
                ],
                'features' => [
                    'en' => ['Enrolment and records', 'Timetabling', 'Results and reports', 'Parent messaging'],
                    'fr' => ['Inscriptions et dossiers', 'Emplois du temps', 'Resultats et bulletins', 'Messagerie parents'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'innova-hotel',
                'sector' => 'hospitality',
                'name' => ['en' => 'INNOVA Hotel', 'fr' => 'INNOVA Hotel'],
                'tagline' => [
                    'en' => 'Reservations, housekeeping, billing and guest experience in one place',
                    'fr' => 'Reservations, menage, facturation et experience client au meme endroit',
                ],
                'features' => [
                    'en' => ['Reservations', 'Housekeeping boards', 'Billing and receipts', 'Guest experience'],
                    'fr' => ['Reservations', 'Tableaux de menage', 'Facturation et recus', 'Experience client'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'bookit',
                'sector' => 'retail',
                'name' => ['en' => 'BookIt', 'fr' => 'BookIt'],
                'tagline' => [
                    'en' => 'Ride sharing and delivery, built for how people actually move here',
                    'fr' => 'Covoiturage et livraison, concus pour les deplacements reels d ici',
                ],
                'features' => [
                    'en' => ['Ride booking', 'Delivery jobs', 'Driver management', 'Live tracking'],
                    'fr' => ['Reservation de courses', 'Livraisons', 'Gestion des chauffeurs', 'Suivi en direct'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => false,
            ],
            [
                // On the roll-up but not on the company's own site, so it is
                // listed as in development rather than as already shipped.
                'slug' => 'integrated-hospital-software',
                'sector' => 'health',
                'name' => ['en' => 'I-NNOVA Integrated Hospital Software', 'fr' => 'Logiciel Hospitalier Integre I-NNOVA'],
                'tagline' => [
                    'en' => 'A complete solution for hospital management',
                    'fr' => 'Une solution complete pour la gestion hospitaliere',
                ],
                'features' => ['en' => [], 'fr' => []],
                'status' => ProductStatus::ComingSoon,
                'is_featured' => false,
            ],
            [
                'slug' => 'custom-development',
                'sector' => 'retail',
                'name' => ['en' => 'Custom Development', 'fr' => 'Developpement sur mesure'],
                'tagline' => [
                    'en' => 'Tailor-made software built to fit a business nothing off the shelf suits',
                    'fr' => 'Des logiciels sur mesure pour une activite qu aucune solution standard ne couvre',
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
    private function caseStudies(array $sectors, array $products): void
    {
        $rows = [
            [
                'slug' => 'sahik',
                'institution' => 'Sapientia Higher Institute of the Diocese of Kumba',
                'short' => 'SAHIK',
                'sector' => 'education',
                'product' => 'innova-school',
                'logo' => 'sahik.png',
            ],
            [
                'slug' => 'paxhi',
                'institution' => 'PAXHI',
                'short' => 'PAXHI',
                'sector' => null,
                'product' => null,
                'logo' => 'paxhi.png',
            ],
        ];

        foreach ($rows as $order => $row) {
            $caseStudy = CaseStudy::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'institution' => $row['institution'],
                    'sector_id' => $row['sector'] ? $sectors[$row['sector']]->id : null,
                    'product_id' => $row['product'] ? $products[$row['product']]->id : null,
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
                    'is_featured' => true,
                ],
            );

            // The real crest, not a generated stand-in.
            $this->attachFile($caseStudy, 'logo', public_path('images/' . $row['logo']), $row['short']);

            $this->attachImage($caseStudy, 'cover', $row['short'], 1600, 900);
        }
    }
}

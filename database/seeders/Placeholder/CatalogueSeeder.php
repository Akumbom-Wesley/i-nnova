<?php

namespace Database\Seeders\Placeholder;

use App\Enums\ProductStatus;
use App\Models\CaseStudy;
use App\Models\Product;
use App\Models\Sector;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Sectors, products and case studies. Placeholder copy throughout.
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
            'hospitality' => ['en' => 'Hospitality', 'fr' => 'Hotellerie'],
            'retail' => ['en' => 'Retail', 'fr' => 'Commerce'],
            'health' => ['en' => 'Health', 'fr' => 'Sante'],
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
        $rows = [
            [
                'slug' => 'paxhi',
                'sector' => 'hospitality',
                'name' => ['en' => 'PAXHI', 'fr' => 'PAXHI'],
                'tagline' => [
                    'en' => 'Hotel operations that hold up on a bad night',
                    'fr' => 'Une gestion hoteliere qui tient la nuit difficile',
                ],
                'features' => [
                    'en' => ['Front desk and reservations', 'Billing and receipts', 'Housekeeping boards'],
                    'fr' => ['Reception et reservations', 'Facturation et recus', 'Tableaux de menage'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'sahik',
                'sector' => 'education',
                'name' => ['en' => 'SAHIK', 'fr' => 'SAHIK'],
                'tagline' => [
                    'en' => 'School administration built for real timetables',
                    'fr' => 'Administration scolaire pensee pour de vrais emplois du temps',
                ],
                'features' => [
                    'en' => ['Enrolment and records', 'Timetabling', 'Results and reports'],
                    'fr' => ['Inscriptions et dossiers', 'Emplois du temps', 'Resultats et bulletins'],
                ],
                'status' => ProductStatus::Live,
                'is_featured' => true,
            ],
            [
                'slug' => 'placeholder-retail',
                'sector' => 'retail',
                'name' => ['en' => 'Placeholder Retail', 'fr' => 'Commerce Placeholder'],
                'tagline' => [
                    'en' => 'Stock and till for small retailers',
                    'fr' => 'Stock et caisse pour petits commerces',
                ],
                'features' => ['en' => ['Stock control', 'Till'], 'fr' => ['Gestion de stock', 'Caisse']],
                'status' => ProductStatus::ComingSoon,
                'is_featured' => false,
            ],
        ];

        $products = [];
        $order = 0;

        foreach ($rows as $row) {
            $slug = $row['slug'];
            $sectorKey = $row['sector'];
            unset($row['slug'], $row['sector']);

            $attributes = $row + [
                'sector_id' => $sectors[$sectorKey]->id,
                'description' => [
                    'en' => '<p>Placeholder description. Replaced with real copy in Sprint 5.</p>',
                    'fr' => '<p>Description provisoire. Remplacee par le vrai texte au Sprint 5.</p>',
                ],
                'sort_order' => $order++,
            ];

            if ($attributes['status'] === ProductStatus::ComingSoon) {
                $attributes['launch_date'] = now()->addMonths(6)->startOfMonth();
            }

            $product = Product::updateOrCreate(['slug' => $slug], $attributes);
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
            ['slug' => 'paxhi-deployment', 'institution' => 'Placeholder Hotel Group', 'sector' => 'hospitality', 'product' => 'paxhi'],
            ['slug' => 'sahik-deployment', 'institution' => 'Placeholder University', 'sector' => 'education', 'product' => 'sahik'],
        ];

        foreach ($rows as $order => $row) {
            $caseStudy = CaseStudy::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'institution' => $row['institution'],
                    'sector_id' => $sectors[$row['sector']]->id,
                    'product_id' => $products[$row['product']]->id,
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

            $this->attachImage($caseStudy, 'logo', $caseStudy->institution, 600, 600, 'paper');
            $this->attachImage($caseStudy, 'cover', $caseStudy->institution, 1600, 900);
            $this->attachImage($caseStudy, 'images', $caseStudy->institution . ' in use', 1200, 800, 'ink');
        }
    }
}

<?php

namespace Database\Seeders\Placeholder;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * The company's own photographs, attached as real uploads rather than as
 * stand-in addresses. Each is placed where its subject belongs: the team and
 * the deployments lead the hero, the cohort sessions carry Kickstarter, and
 * the workroom shots carry About.
 *
 * Source files live in database/seeders/assets/photography. Media Library
 * copies them into storage, so an editor can replace any one of them in the
 * admin without touching this file.
 */
class PhotographySeeder extends Seeder
{
    use AttachesPlaceholderImages;

    private function assetPath(string $file): string
    {
        return database_path('seeders/assets/photography/' . $file);
    }

    public function run(): void
    {
        $rows = [
            // The hero. The widest, most representative four: who we are,
            // the programme running, and the products in service on site.
            [
                'hero', 'team-banner-wide.jpeg',
                'The team',
                'The I-NNOVA team in Bamenda',
            ],
            [
                'hero', 'cohort-full-room.jpeg',
                'A cohort at work',
                'A full Kickstarter session, everyone building',
            ],
            [
                'hero', 'onsite-school-handover.jpeg',
                'On site',
                'Handing a school management system over to the staff who will run it',
            ],
            [
                'hero', 'onsite-school-dashboard.jpeg',
                'In service',
                'The school administration dashboard running at an institution',
            ],

            // The home page strip. Portrait tiles, so the two portrait
            // orientation team photographs sit here without cropping.
            [
                'home', 'team-banner-five.jpeg',
                'Driven by STEM',
                'The team in front of the roll-up in Bamenda',
            ],
            [
                'home', 'team-banner-four.jpeg',
                'Hands-on learning',
                'Engineers who build the products and teach the programme',
            ],

            // Three beside the Kickstarter heading, and the programme itself
            // in the gallery. The first three are marked featured, so they are
            // what the band on the Kickstarter page shows; the rest wait on
            // the gallery page. A gallery is the one place repetition is fine:
            // it is meant to hold everything, and the cluster is a selection
            // rather than a separate set. The About header is a designed
            // panel, so nothing repeats across the two pages.
            [
                'kickstarter_feature', 'office-discussion.jpeg',
                'In the room',
                'A session under way at the Bamenda office',
            ],
            [
                'kickstarter_feature', 'cohort-workroom.jpeg',
                'The workroom',
                'Laptops open, work in progress',
            ],
            [
                'kickstarter_feature', 'cohort-tables.jpeg',
                'Heads down',
                'Around the tables, mid build',
            ],
            [
                'kickstarter', 'kickstarter-security-session.jpeg',
                'Cybersecurity, in session',
                'Working through what happens to a secret committed to a repository',
            ],
            [
                'kickstarter', 'kickstarter-deployment-session.jpeg',
                'From a laptop to production',
                'Taking a project from files on a laptop to a public HTTPS site',
            ],
            [
                'kickstarter', 'kickstarter-projector.jpeg',
                'Following along',
                'A cohort working through a walkthrough together',
            ],
            [
                'kickstarter', 'cohort-full-room.jpeg',
                'A full room',
                'Every seat taken, a cohort mid session',
            ],
            [
                'kickstarter', 'cohort-workroom.jpeg',
                'The workroom',
                'Laptops open, work in progress',
            ],
            [
                'kickstarter', 'cohort-tables.jpeg',
                'Around the tables',
                'Heads down, mid build',
            ],
            [
                'kickstarter', 'office-discussion.jpeg',
                'Talking it through',
                'A problem worked out loud at the Bamenda office',
            ],
        ];

        // The three the Kickstarter page itself shows. Everything else is on
        // the gallery page behind them.
        $featured = [
            'kickstarter-security-session.jpeg',
            'kickstarter-deployment-session.jpeg',
            'kickstarter-projector.jpeg',
        ];

        foreach ($rows as $order => [$placement, $file, $title, $caption]) {
            $image = GalleryImage::updateOrCreate(
                ['external_url' => null, 'placement' => GalleryPlacement::from($placement), 'sort_order' => $order],
                [
                    'title' => ['en' => $title, 'fr' => $title],
                    'alt' => ['en' => $caption, 'fr' => $caption],
                    'caption' => ['en' => $caption, 'fr' => $caption],
                    'is_active' => true,
                    'is_featured' => in_array($file, $featured, true),
                ],
            );

            $this->attachFile($image, 'image', $this->assetPath($file), $title);
        }
    }
}

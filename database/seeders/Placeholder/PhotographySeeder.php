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

            // Kickstarter carries all the photography of the programme: three
            // in the header cluster and the rest in the gallery below it. The
            // About header is a designed panel rather than photographs, so
            // nothing is shown twice across the two pages.
            [
                'kickstarter', 'office-discussion.jpeg',
                'In the room',
                'A session under way at the Bamenda office',
            ],
            [
                'kickstarter', 'cohort-workroom.jpeg',
                'The workroom',
                'Laptops open, work in progress',
            ],
            [
                'kickstarter', 'cohort-tables.jpeg',
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
        ];

        foreach ($rows as $order => [$placement, $file, $title, $caption]) {
            $image = GalleryImage::updateOrCreate(
                ['external_url' => null, 'placement' => GalleryPlacement::from($placement), 'sort_order' => $order],
                [
                    'title' => ['en' => $title, 'fr' => $title],
                    'alt' => ['en' => $caption, 'fr' => $caption],
                    'caption' => ['en' => $caption, 'fr' => $caption],
                    'is_active' => true,
                ],
            );

            $this->attachFile($image, 'image', $this->assetPath($file), $title);
        }
    }
}

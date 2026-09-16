<?php

namespace Database\Seeders\Placeholder;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

/**
 * Stand-in photography, chosen to match the company rather than fill a hole.
 *
 * Every address below was checked and returns a real JPEG. They show African
 * engineers at work, training in progress, teams collaborating and Cameroonian
 * streets, so the layouts can be judged against something close to the real
 * thing rather than against abstract scenery.
 *
 * They are still stand-ins. Uploading a photograph to a record in the admin
 * replaces the address automatically, and the Photography list carries a
 * count of how many are still outstanding.
 *
 * Source: Unsplash, whose licence permits commercial use without attribution.
 * Hotlinking their CDN is fine for placeholders but should not survive to
 * launch: Sprint 5 replaces these with the company's own photographs.
 */
class PhotographySeeder extends Seeder
{
    private function unsplash(string $id, int $width = 1600): string
    {
        return "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$width}&q=70";
    }

    public function run(): void
    {
        $rows = [
            // The hero slideshow. Four reads best: enough range, few enough
            // that a visitor sees them all before scrolling past.
            ['hero', '1573164574572-cb89e39749b4', 1920, 'Building together', 'The team working through a release'],
            ['hero', '1655720348590-c739c860beed', 1920, 'A cohort at work', 'Kickstarter engineers pairing on a build'],
            ['hero', '1632454005865-1ea2afe2074f', 1920, 'Deep in the work', 'Writing the systems institutions run on'],
            ['hero', '1659947234309-804b7fa01cf2', 1920, 'Where we are', 'Bamenda, North West Region'],

            // The home page band: a cross section of the whole company.
            ['home', '1528901166007-3784c7dd3653', 1200, 'At the keyboard', 'An engineer mid feature'],
            ['home', '1655720352328-87e021d2a84e', 1200, 'Mentoring', 'Working through a problem side by side'],
            ['home', '1573164574511-73c773193279', 1200, 'In the room', 'Planning the next delivery'],
            ['home', '1659947234294-b217aaeb25f8', 1200, 'The city', 'The streets the company was built in'],

            // About: the place, and the people in it.
            ['about', '1573164574397-dd250bc8a598', 1200, 'The workspace', 'Where the products get built'],
            ['about', '1593910409015-59ae3c6aff04', 1200, 'Focus', 'A morning on a school timetable engine'],
            ['about', '1684337399050-0412ebed8005', 1200, 'Tools of the trade', 'A working machine, mid project'],
            ['about', '1594386479412-fa62932f4cdc', 1200, 'Bamenda', 'The city the company was built in'],
            ['about', '1622295023825-6e319464b810', 1200, 'The team', 'One of us, on a Friday'],
            ['about', '1615463738213-b9381d217b4e', 1200, 'North West Region', 'The hills above the town'],

            // Kickstarter: the programme actually running.
            ['kickstarter', '1744809482817-9a9d4fc280af', 1200, 'A cohort mid track', 'Software Development, week six'],
            ['kickstarter', '1620829813573-7c9e1877706f', 1200, 'Building', 'An intern shipping a first feature'],
            ['kickstarter', '1632215861513-130b66fe97f4', 1200, 'Teaching', 'A mentor taking a session'],
            ['kickstarter', '1694175271713-a6e2cc378980', 1200, 'Hands on', 'Real projects, not exercises'],
            ['kickstarter', '1632932693914-89b90ae3d16d', 1200, 'The cohort', 'A full intake, at the start'],
            ['kickstarter', '1719314319573-6abae1dfc520', 1200, 'Demo day', 'Presenting a finished project'],
        ];

        foreach ($rows as $order => [$placement, $id, $width, $title, $caption]) {
            GalleryImage::updateOrCreate(
                ['external_url' => $this->unsplash($id, $width)],
                [
                    'placement' => GalleryPlacement::from($placement),
                    'title' => ['en' => $title, 'fr' => $title],
                    'alt' => ['en' => $caption, 'fr' => $caption],
                    'caption' => ['en' => $caption, 'fr' => $caption],
                    'sort_order' => $order,
                    'is_active' => true,
                ],
            );
        }
    }
}

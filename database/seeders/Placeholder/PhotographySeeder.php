<?php

namespace Database\Seeders\Placeholder;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use Illuminate\Database\Seeder;

/**
 * Stand-in photography.
 *
 * These rows point at remote images so the galleries can be designed and
 * reviewed against real-looking photographs before the company's own arrive.
 * Every one is replaced by uploading a file to the record in the admin: an
 * upload always wins over the address, and nothing in the templates changes.
 *
 * The admin shows a "Stand-in" badge on each of these and a count in the
 * sidebar, so how many are left is never a guess.
 */
class PhotographySeeder extends Seeder
{
    /**
     * Deterministic per subject, so a given row keeps the same stand-in
     * between runs rather than shuffling on every reseed.
     */
    private function standIn(string $seed, int $width = 1200, int $height = 900): string
    {
        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }

    public function run(): void
    {
        $rows = [
            // The home page band: a cross section of the whole company.
            ['home', 'innova-team-standup', 'The team at work', 'Morning stand-up in the Bamenda office'],
            ['home', 'innova-cohort-lab', 'A cohort in session', 'Kickstarter engineers pairing on a build'],
            ['home', 'innova-office-bamenda', 'The office', 'Belgocam Building, Bamenda'],
            ['home', 'innova-deployment-day', 'On site', 'Installing at a partner institution'],

            // About: the place and the people.
            ['about', 'innova-workspace-wide', 'The workspace', 'Where the products get built'],
            ['about', 'innova-team-whiteboard', 'Working a problem', 'Mapping a school timetable engine'],
            ['about', 'innova-bamenda-street', 'Bamenda', 'The city the company was built in'],
            ['about', 'innova-team-review', 'Code review', 'Two engineers reading a change together'],
            ['about', 'innova-team-portrait', 'The team', 'Most of I-NNOVA, on a Friday'],
            ['about', 'innova-desk-detail', 'Detail', 'A working desk, mid project'],

            // Kickstarter: the internship actually running.
            ['kickstarter', 'innova-internship-class', 'A cohort mid track', 'Software Development, week six'],
            ['kickstarter', 'innova-mentor-session', 'Mentoring', 'A mentor working through a review'],
            ['kickstarter', 'innova-intern-screen', 'Building', 'An intern shipping their first feature'],
            ['kickstarter', 'innova-demo-day', 'Demo day', 'Presenting a finished project'],
            ['kickstarter', 'innova-cohort-group', 'The cohort', 'A full intake, at the start'],
            ['kickstarter', 'innova-lab-evening', 'Late session', 'The lab, after hours'],
        ];

        foreach ($rows as $order => [$placement, $seed, $title, $caption]) {
            GalleryImage::updateOrCreate(
                ['external_url' => $this->standIn($seed)],
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

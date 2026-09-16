<?php

namespace Database\Seeders\Placeholder;

use App\Models\AlumniOutcome;
use App\Models\KickstarterMentor;
use App\Models\KickstarterTrack;
use App\Models\Stat;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Kickstarter tracks, mentors and alumni outcomes, plus the stat blocks for
 * both the main site and the Kickstarter page.
 */
class KickstarterSeeder extends Seeder
{
    use AttachesPlaceholderImages;

    public function run(): void
    {
        $tracks = $this->tracks();

        $this->mentors();
        $this->alumni($tracks);
        $this->stats();
    }

    /** @return array<int, KickstarterTrack> */
    private function tracks(): array
    {
        $rows = [
            ['slug' => 'software-engineering', 'en' => 'Software Engineering', 'fr' => 'Genie Logiciel', 'duration' => ['en' => '12 weeks', 'fr' => '12 semaines']],
            ['slug' => 'product-design', 'en' => 'Product Design', 'fr' => 'Design Produit', 'duration' => ['en' => '8 weeks', 'fr' => '8 semaines']],
        ];

        $tracks = [];

        foreach ($rows as $order => $row) {
            $track = KickstarterTrack::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => ['en' => $row['en'], 'fr' => $row['fr']],
                    'duration' => $row['duration'],
                    'summary' => ['en' => 'Placeholder track summary.', 'fr' => 'Resume provisoire du parcours.'],
                    'description' => ['en' => '<p>Placeholder description.</p>', 'fr' => '<p>Description provisoire.</p>'],
                    'sort_order' => $order,
                    'is_active' => true,
                ],
            );

            $this->attachImage($track, 'image', $row['en'], 1200, 800, 'orange');

            $tracks[] = $track;
        }

        return $tracks;
    }

    private function mentors(): void
    {
        foreach (['Placeholder Mentor A', 'Placeholder Mentor B'] as $order => $name) {
            $mentor = KickstarterMentor::updateOrCreate(
                ['name' => $name],
                [
                    'title' => ['en' => 'Placeholder title', 'fr' => 'Titre provisoire'],
                    'credentials' => ['en' => 'Placeholder credentials', 'fr' => 'References provisoires'],
                    'bio' => ['en' => '<p>Placeholder bio.</p>', 'fr' => '<p>Biographie provisoire.</p>'],
                    'sort_order' => $order,
                ],
            );

            $this->attachImage($mentor, 'photo', $name, 800, 800, 'ink');
        }
    }

    /** @param  array<int, KickstarterTrack>  $tracks */
    private function alumni(array $tracks): void
    {
        $names = ['Placeholder Alumni A', 'Placeholder Alumni B', 'Placeholder Alumni C'];

        foreach ($names as $order => $name) {
            $alumni = AlumniOutcome::updateOrCreate(
                ['name' => $name],
                [
                    'role' => ['en' => 'Junior Engineer', 'fr' => 'Ingenieur Junior'],
                    'organisation' => 'Placeholder Employer',
                    'outcome' => ['en' => 'Placeholder outcome.', 'fr' => 'Resultat provisoire.'],
                    'quote' => ['en' => 'Placeholder alumni quote.', 'fr' => 'Citation provisoire.'],
                    'kickstarter_track_id' => $tracks[$order % count($tracks)]->id,
                    'cohort_year' => (int) now()->subYear()->format('Y'),
                    'sort_order' => $order,
                ],
            );

            $this->attachImage($alumni, 'photo', $name, 600, 600, 'ink');
        }
    }

    private function stats(): void
    {
        $rows = [
            [Stat::CONTEXT_SITE, '2', ['en' => 'Products live', 'fr' => 'Produits en service']],
            [Stat::CONTEXT_SITE, '4', ['en' => 'Sectors served', 'fr' => 'Secteurs servis']],
            [Stat::CONTEXT_SITE, '100%', ['en' => 'Built in Cameroon', 'fr' => 'Concu au Cameroun']],
            [Stat::CONTEXT_KICKSTARTER, '2', ['en' => 'Tracks', 'fr' => 'Parcours']],
            [Stat::CONTEXT_KICKSTARTER, '12', ['en' => 'Weeks', 'fr' => 'Semaines']],
            [Stat::CONTEXT_KICKSTARTER, '3', ['en' => 'Alumni placed', 'fr' => 'Diplomes places']],
        ];

        foreach ($rows as $order => [$context, $value, $label]) {
            Stat::updateOrCreate(
                ['context' => $context, 'value' => $value],
                ['label' => $label, 'sort_order' => $order],
            );
        }
    }
}

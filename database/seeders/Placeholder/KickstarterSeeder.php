<?php

namespace Database\Seeders\Placeholder;

use App\Models\AlumniOutcome;
use App\Models\KickstarterMentor;
use App\Models\KickstarterTrack;
use App\Models\Stat;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Kickstarter accelerator tracks, mentors and alumni outcomes, plus the stat
 * blocks for both the main site and the Kickstarter page.
 *
 * The four tracks and their descriptions are the real ones from the company
 * roll-up. Mentors and alumni are still placeholder.
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
            [
                'slug' => 'software-development',
                'name' => ['en' => 'Software Development', 'fr' => 'Developpement Logiciel'],
                'summary' => [
                    'en' => 'Build modern web, mobile and desktop applications.',
                    'fr' => 'Construire des applications web, mobiles et bureau modernes.',
                ],
            ],
            [
                'slug' => 'ai-machine-learning',
                'name' => ['en' => 'AI and Machine Learning', 'fr' => 'IA et Apprentissage Automatique'],
                'summary' => [
                    'en' => 'Create intelligent systems and predictive solutions.',
                    'fr' => 'Creer des systemes intelligents et des solutions predictives.',
                ],
            ],
            [
                'slug' => 'cybersecurity',
                'name' => ['en' => 'Cybersecurity', 'fr' => 'Cybersecurite'],
                'summary' => [
                    'en' => 'Protect systems, data and networks. Build a secure digital future.',
                    'fr' => 'Proteger les systemes, les donnees et les reseaux.',
                ],
            ],
            [
                'slug' => 'data-science',
                'name' => ['en' => 'Data Science', 'fr' => 'Science des Donnees'],
                'summary' => [
                    'en' => 'Turn data into insights. Solve problems with data driven decisions.',
                    'fr' => 'Transformer les donnees en decisions.',
                ],
            ],
        ];

        $tracks = [];

        foreach ($rows as $order => $row) {
            $track = KickstarterTrack::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'summary' => $row['summary'],
                    'description' => ['en' => '<p>Placeholder description.</p>', 'fr' => '<p>Description provisoire.</p>'],
                    'sort_order' => $order,
                    'is_active' => true,
                ],
            );

            $this->attachImage($track, 'image', $row['name']['en'], 1200, 800, $order % 2 === 0 ? 'blue' : 'orange');

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

    /**
     * Keyed on context plus order rather than value, because two stats in the
     * same context can legitimately share a number.
     */
    private function stats(): void
    {
        $rows = [
            [Stat::CONTEXT_SITE, '4', ['en' => 'Products live', 'fr' => 'Produits en service']],
            [Stat::CONTEXT_SITE, '4', ['en' => 'Sectors served', 'fr' => 'Secteurs servis']],
            [Stat::CONTEXT_SITE, '100%', ['en' => 'Built in Cameroon', 'fr' => 'Concu au Cameroun']],
            [Stat::CONTEXT_KICKSTARTER, '4', ['en' => 'Accelerator tracks', 'fr' => 'Parcours accelerateur']],
            [Stat::CONTEXT_KICKSTARTER, '5', ['en' => 'Career Capital measures', 'fr' => 'Mesures du Capital Carriere']],
            [Stat::CONTEXT_KICKSTARTER, '100', ['en' => 'Career Capital scale', 'fr' => 'Echelle du Capital Carriere']],
        ];

        foreach ($rows as $order => [$context, $value, $label]) {
            Stat::updateOrCreate(
                ['context' => $context, 'sort_order' => $order],
                ['value' => $value, 'label' => $label],
            );
        }
    }
}

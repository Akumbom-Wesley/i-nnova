<?php

namespace Database\Seeders\Placeholder;

use App\Models\AlumniOutcome;
use App\Models\KickstarterMentor;
use App\Models\KickstarterTrack;
use App\Enums\StatSource;
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
        // Five tracks: the four the company lists on its own site, plus
        // cybersecurity from the roll-up. "Software Development" from the
        // roll-up is not repeated because Web and Mobile already cover it.
        // The five tracks named on the roll-up, which is the current statement
        // of the programme.
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
                    'fr' => 'Proteger les systemes, les donnees et les reseaux. Construire un avenir numerique sur.',
                ],
            ],
            [
                'slug' => 'data-science',
                'name' => ['en' => 'Data Science', 'fr' => 'Science des Donnees'],
                'summary' => [
                    'en' => 'Turn data into insights. Solve problems with data driven decisions.',
                    'fr' => 'Transformer les donnees en decisions fondees sur les faits.',
                ],
            ],
            [
                'slug' => 'cloud-engineering',
                'name' => ['en' => 'Cloud Engineering', 'fr' => 'Ingenierie Cloud'],
                'summary' => [
                    'en' => 'Design, build and manage scalable cloud infrastructure.',
                    'fr' => 'Concevoir, construire et gerer une infrastructure cloud evolutive.',
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
        // The three that read as claims are counted rather than typed, so they
        // cannot drift as the site grows. Source, value, suffix, label.
        $rows = [
            [Stat::CONTEXT_SITE, StatSource::ProductsLive, '', '', ['en' => 'Products live', 'fr' => 'Produits en service']],
            [Stat::CONTEXT_SITE, StatSource::BusinessesServed, '', '+', ['en' => 'Businesses served', 'fr' => 'Entreprises servies']],
            [Stat::CONTEXT_SITE, StatSource::YearsBuilding, '', '+', ['en' => 'Years building', 'fr' => 'Annees de construction']],
            [Stat::CONTEXT_KICKSTARTER, StatSource::Manual, '500', '+', ['en' => 'Students reached', 'fr' => 'Etudiants touches']],
            [Stat::CONTEXT_KICKSTARTER, StatSource::Manual, '90%', '+', ['en' => 'Employed within 3 months', 'fr' => 'Employes en moins de 3 mois']],
            [Stat::CONTEXT_KICKSTARTER, StatSource::AcceleratorTracks, '', '', ['en' => 'Accelerator tracks', 'fr' => 'Parcours accelerateur']],
        ];

        foreach ($rows as $order => [$context, $source, $value, $suffix, $label]) {
            Stat::updateOrCreate(
                ['context' => $context, 'sort_order' => $order],
                ['source' => $source, 'value' => $value, 'suffix' => $suffix, 'label' => $label],
            );
        }
    }
}

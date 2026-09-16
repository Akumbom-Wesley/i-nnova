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
        // Five tracks: the four the company lists on its own site, plus
        // cybersecurity from the roll-up. "Software Development" from the
        // roll-up is not repeated because Web and Mobile already cover it.
        $rows = [
            [
                'slug' => 'web-development',
                'name' => ['en' => 'Web Development', 'fr' => 'Developpement Web'],
                'summary' => [
                    'en' => 'Frontend, backend and full-stack. Build modern web applications end to end.',
                    'fr' => 'Frontend, backend et full-stack. Construire des applications web de bout en bout.',
                ],
            ],
            [
                'slug' => 'mobile-development',
                'name' => ['en' => 'Mobile Development', 'fr' => 'Developpement Mobile'],
                'summary' => [
                    'en' => 'Android, iOS and cross-platform. Ship to the device people actually carry.',
                    'fr' => 'Android, iOS et multiplateforme. Livrer sur l appareil que les gens portent vraiment.',
                ],
            ],
            [
                'slug' => 'ui-ux-design',
                'name' => ['en' => 'UI and UX Design', 'fr' => 'Design UI et UX'],
                'summary' => [
                    'en' => 'Design thinking and prototyping. Decide what to build before building it.',
                    'fr' => 'Design thinking et prototypage. Decider quoi construire avant de le construire.',
                ],
            ],
            [
                'slug' => 'data-and-ai',
                'name' => ['en' => 'Data and AI', 'fr' => 'Donnees et IA'],
                'summary' => [
                    'en' => 'Data science and machine learning. Turn data into decisions.',
                    'fr' => 'Science des donnees et apprentissage automatique. Transformer les donnees en decisions.',
                ],
            ],
            [
                // Legible on the physical roll-up in the team photograph, but
                // not in the roll-up file, so it was missed first time round.
                'slug' => 'cloud-engineering',
                'name' => ['en' => 'Cloud Engineering', 'fr' => 'Ingenierie Cloud'],
                'summary' => [
                    'en' => 'Design, build and manage scalable cloud infrastructure.',
                    'fr' => 'Concevoir, construire et gerer une infrastructure cloud evolutive.',
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
            [Stat::CONTEXT_SITE, '6', ['en' => 'Products and growing', 'fr' => 'Produits, et ca continue']],
            [Stat::CONTEXT_SITE, '100+', ['en' => 'Businesses served', 'fr' => 'Entreprises servies']],
            [Stat::CONTEXT_SITE, '3+', ['en' => 'Years building', 'fr' => 'Annees de construction']],
            [Stat::CONTEXT_KICKSTARTER, '500+', ['en' => 'Students reached', 'fr' => 'Etudiants touches']],
            [Stat::CONTEXT_KICKSTARTER, '90%+', ['en' => 'Employed within 3 months', 'fr' => 'Employes en moins de 3 mois']],
            [Stat::CONTEXT_KICKSTARTER, '6', ['en' => 'Accelerator tracks', 'fr' => 'Parcours accelerateur']],
        ];

        foreach ($rows as $order => [$context, $value, $label]) {
            Stat::updateOrCreate(
                ['context' => $context, 'sort_order' => $order],
                ['value' => $value, 'label' => $label],
            );
        }
    }
}

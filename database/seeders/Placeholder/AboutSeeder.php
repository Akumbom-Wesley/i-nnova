<?php

namespace Database\Seeders\Placeholder;

use App\Models\CompanyValue;
use App\Models\Milestone;
use App\Models\ProcessStep;
use App\Models\TeamMember;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Values and team. The values copy is drawn from the brand guide's voice
 * section rather than invented.
 */
class AboutSeeder extends Seeder
{
    use AttachesPlaceholderImages;

    public function run(): void
    {
        $this->values();
        $this->team();
        $this->process();
        $this->milestones();
    }

    /**
     * Six values: the five the company states on its own site, plus
     * "empowering innovators" from the roll-up, which the others do not say.
     */
    /**
     * The four pillars stated on the roll-up, which is the current statement
     * of what the company says it stands for.
     */
    private function values(): void
    {
        $rows = [
            [
                'title' => ['en' => 'Innovative Solutions', 'fr' => 'Solutions Innovantes'],
                'body' => [
                    'en' => '<p>Smart solutions that solve real world problems rather than demonstrate technology.</p>',
                    'fr' => '<p>Des solutions intelligentes qui resolvent de vrais problemes plutot que de demontrer la technologie.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Community Impact', 'fr' => 'Impact Communautaire'],
                'body' => [
                    'en' => '<p>Transforming communities, empowering innovators. The work is measured by what it changes locally.</p>',
                    'fr' => '<p>Transformer les communautes, autonomiser les innovateurs. Le travail se mesure a ce qu il change localement.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Tech Excellence', 'fr' => 'Excellence Technique'],
                'body' => [
                    'en' => '<p>Driven by STEM. Industry standard tools and workflows, applied with discipline.</p>',
                    'fr' => '<p>Portes par les STEM. Outils et methodes standards, appliques avec rigueur.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Empowering Innovators', 'fr' => 'Autonomiser les Innovateurs'],
                'body' => [
                    'en' => '<p>Built for the next generation of tech leaders. Learn, build, collaborate, transform.</p>',
                    'fr' => '<p>Concu pour la prochaine generation de leaders tech. Apprendre, construire, collaborer, transformer.</p>',
                ],
            ],
        ];

        // Anything beyond the four the roll-up states is removed, so the page
        // and the printed material say the same thing.
        CompanyValue::query()->whereNotIn('id', range(1, count($rows)))->delete();

        foreach ($rows as $order => $row) {
            $value = CompanyValue::updateOrCreate(
                ['id' => $order + 1],
                $row + ['sort_order' => $order],
            );

            $this->attachImage($value, 'image', $row['title']['en'], 1200, 800);
        }
    }
    /**
     * The real leadership team, taken from the company's own site. Anyone
     * beyond these three is added in the admin.
     */
    private function team(): void
    {
        $rows = [
            [
                'name' => 'Obed Destine Atangabua',
                'role' => ['en' => 'Founder and CEO', 'fr' => 'Fondateur et Directeur General'],
                'department' => ['en' => 'Leadership', 'fr' => 'Direction'],
                'credentials' => [
                    'en' => 'Software engineer, 8+ years',
                    'fr' => 'Ingenieur logiciel, plus de 8 ans',
                ],
                'bio' => [
                    'en' => '<p>Visionary leader and software engineer with over eight years building digital solutions for African businesses.</p>',
                    'fr' => '<p>Dirigeant visionnaire et ingenieur logiciel, plus de huit ans a construire des solutions numeriques pour les entreprises africaines.</p>',
                ],
                'featured' => true,
            ],
            [
                'name' => 'Akumbom Wesley',
                'role' => ['en' => 'CTO and Lead Developer', 'fr' => 'Directeur Technique et Developpeur Principal'],
                'department' => ['en' => 'Engineering', 'fr' => 'Ingenierie'],
                'credentials' => [
                    'en' => 'Laravel, Vue.js, cloud infrastructure',
                    'fr' => 'Laravel, Vue.js, infrastructure cloud',
                ],
                'bio' => [
                    'en' => '<p>Full-stack developer and architect specialising in Laravel, Vue.js and cloud infrastructure.</p>',
                    'fr' => '<p>Developpeur full-stack et architecte, specialise en Laravel, Vue.js et infrastructure cloud.</p>',
                ],
                'featured' => true,
            ],
            [
                'name' => 'Feteh Ndimbe Diran',
                'role' => ['en' => 'Head of IKS Programme', 'fr' => 'Responsable du Programme IKS'],
                'department' => ['en' => 'Kickstarter', 'fr' => 'Kickstarter'],
                'credentials' => [
                    'en' => 'Educator and developer',
                    'fr' => 'Educateur et developpeur',
                ],
                'bio' => [
                    'en' => '<p>Educator and developer, set on nurturing the next generation of African tech talent.</p>',
                    'fr' => '<p>Educateur et developpeur, engage a former la prochaine generation de talents tech africains.</p>',
                ],
                'featured' => true,
            ],
        ];

        foreach ($rows as $order => $row) {
            $member = TeamMember::updateOrCreate(
                ['slug' => str($row['name'])->slug()->value()],
                [
                    'name' => $row['name'],
                    'role' => $row['role'],
                    'department' => $row['department'],
                    'credentials' => $row['credentials'],
                    'bio' => $row['bio'],
                    'sort_order' => $order,
                    'is_featured' => $row['featured'],
                ],
            );

            // Portraits are still outstanding; the placeholder keeps the row
            // from rendering as an empty circle until a real one arrives.
            $this->attachImage($member, 'photo', $row['name'], 800, 800, 'ink');
        }
    }
    private function process(): void
    {
        $rows = [
            [
                'title' => ['en' => 'Understand the day', 'fr' => 'Comprendre la journee'],
                'summary' => [
                    'en' => 'We sit with the people who will use it before we design anything.',
                    'fr' => 'Nous passons du temps avec les utilisateurs avant toute conception.',
                ],
            ],
            [
                'title' => ['en' => 'Build in the open', 'fr' => 'Construire a decouvert'],
                'summary' => [
                    'en' => 'Short cycles, working software you can see, no long silences.',
                    'fr' => 'Cycles courts, logiciel visible, pas de longs silences.',
                ],
            ],
            [
                'title' => ['en' => 'Deploy and stay', 'fr' => 'Deployer et rester'],
                'summary' => [
                    'en' => 'We run what we build, so a problem at 7am is our problem too.',
                    'fr' => 'Nous exploitons ce que nous construisons, les incidents sont aussi les notres.',
                ],
            ],
        ];

        foreach ($rows as $order => $row) {
            ProcessStep::updateOrCreate(
                ['id' => $order + 1],
                $row + [
                    'body' => ['en' => '<p>Placeholder detail.</p>', 'fr' => '<p>Detail provisoire.</p>'],
                    'sort_order' => $order,
                ],
            );
        }
    }

    /**
     * The timeline from the company's own site: founded 2022 in Bamenda.
     */
    private function milestones(): void
    {
        $rows = [
            [
                'year' => '2022',
                'title' => ['en' => 'The spark', 'fr' => 'L etincelle'],
                'body' => [
                    'en' => 'I-NNOVA was born from a simple but powerful idea: Cameroon needs homegrown tech solutions, and the talent to build them.',
                    'fr' => 'I-NNOVA est nee d une idee simple et puissante: le Cameroun a besoin de solutions technologiques locales, et des talents pour les construire.',
                ],
            ],
            [
                'year' => '2023',
                'title' => ['en' => 'Foundations', 'fr' => 'Les fondations'],
                'body' => [
                    'en' => 'The first products took shape and the Kickstarter programme was created.',
                    'fr' => 'Les premiers produits prennent forme et le programme Kickstarter est cree.',
                ],
            ],
            [
                'year' => '2024',
                'title' => ['en' => 'The portfolio widens', 'fr' => 'Le portefeuille s elargit'],
                'body' => [
                    'en' => 'Ride sharing, hotel management and school management joined the line.',
                    'fr' => 'Covoiturage, gestion hoteliere et gestion scolaire rejoignent la gamme.',
                ],
            ],
            [
                'year' => '2025',
                'title' => ['en' => 'Scale', 'fr' => 'Le passage a l echelle'],
                'body' => [
                    'en' => 'Over 500 students reached through Kickstarter, and more than 100 businesses served.',
                    'fr' => 'Plus de 500 etudiants touches par Kickstarter, et plus de 100 entreprises servies.',
                ],
            ],
        ];

        foreach ($rows as $order => $row) {
            Milestone::updateOrCreate(
                ['year' => $row['year']],
                $row + ['sort_order' => $order],
            );
        }
    }
}

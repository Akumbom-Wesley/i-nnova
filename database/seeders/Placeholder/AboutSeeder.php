<?php

namespace Database\Seeders\Placeholder;

use App\Models\CompanyValue;
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
    }

    private function values(): void
    {
        $rows = [
            [
                'title' => ['en' => 'Innovative Solutions', 'fr' => 'Solutions Innovantes'],
                'body' => [
                    'en' => '<p>We build smart solutions that solve real world problems rather than demonstrate technology.</p>',
                    'fr' => '<p>Nous creons des solutions intelligentes qui resolvent de vrais problemes.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Community Impact', 'fr' => 'Impact Communautaire'],
                'body' => [
                    'en' => '<p>Transforming communities, empowering innovators. The work is measured by what it changes locally.</p>',
                    'fr' => '<p>Transformer les communautes, autonomiser les innovateurs.</p>',
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
                    'en' => '<p>We do not just build software. We build the builders.</p>',
                    'fr' => '<p>Nous ne construisons pas que des logiciels. Nous formons ceux qui les construisent.</p>',
                ],
            ],
        ];

        foreach ($rows as $order => $row) {
            $value = CompanyValue::updateOrCreate(
                ['id' => $order + 1],
                $row + ['sort_order' => $order],
            );

            $this->attachImage($value, 'image', $row['title']['en'], 1200, 800);
        }
    }

    private function team(): void
    {
        $rows = [
            ['name' => 'Placeholder Lead', 'role' => ['en' => 'Founder', 'fr' => 'Fondateur'], 'department' => ['en' => 'Leadership', 'fr' => 'Direction'], 'featured' => true],
            ['name' => 'Placeholder Engineer', 'role' => ['en' => 'Engineer', 'fr' => 'Ingenieur'], 'department' => ['en' => 'Engineering', 'fr' => 'Ingenierie'], 'featured' => true],
            ['name' => 'Placeholder Designer', 'role' => ['en' => 'Designer', 'fr' => 'Designer'], 'department' => ['en' => 'Design', 'fr' => 'Design'], 'featured' => true],
            ['name' => 'Placeholder Analyst', 'role' => ['en' => 'Analyst', 'fr' => 'Analyste'], 'department' => ['en' => 'Engineering', 'fr' => 'Ingenierie'], 'featured' => false],
        ];

        foreach ($rows as $order => $row) {
            $member = TeamMember::updateOrCreate(
                ['slug' => str($row['name'])->slug()->value()],
                [
                    'name' => $row['name'],
                    'role' => $row['role'],
                    'department' => $row['department'],
                    'credentials' => ['en' => 'Placeholder credentials', 'fr' => 'References provisoires'],
                    'bio' => ['en' => '<p>Placeholder bio.</p>', 'fr' => '<p>Biographie provisoire.</p>'],
                    'socials' => ['linkedin' => 'https://www.linkedin.com/'],
                    'sort_order' => $order,
                    'is_featured' => $row['featured'],
                ],
            );

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
}

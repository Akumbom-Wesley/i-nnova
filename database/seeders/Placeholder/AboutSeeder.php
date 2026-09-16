<?php

namespace Database\Seeders\Placeholder;

use App\Models\CompanyValue;
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
    }

    private function values(): void
    {
        $rows = [
            [
                'title' => ['en' => 'Collaborative', 'fr' => 'Collaboratif'],
                'body' => [
                    'en' => '<p>Open, team driven and inclusive. We encourage shared thinking and open dialogue.</p>',
                    'fr' => '<p>Ouvert, porte par l equipe et inclusif. Nous encourageons la reflexion partagee.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Structured', 'fr' => 'Structure'],
                'body' => [
                    'en' => '<p>Organised and proactive. Clear, concise and intentional in how we work.</p>',
                    'fr' => '<p>Organise et proactif. Clair, concis et intentionnel dans notre travail.</p>',
                ],
            ],
            [
                'title' => ['en' => 'Accountable', 'fr' => 'Responsable'],
                'body' => [
                    'en' => '<p>Driven and accountable. Confident, but grounded in what we have actually shipped.</p>',
                    'fr' => '<p>Engage et responsable. Confiant, mais ancre dans ce que nous avons livre.</p>',
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
}

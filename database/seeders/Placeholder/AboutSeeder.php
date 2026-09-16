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
}

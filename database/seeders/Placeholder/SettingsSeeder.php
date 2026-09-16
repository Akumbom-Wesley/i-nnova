<?php

namespace Database\Seeders\Placeholder;

use App\Models\SiteSetting;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use AttachesPlaceholderImages;

    public function run(): void
    {
        $settings = SiteSetting::instance();

        $settings->fill([
            'hero_heading' => [
                'en' => 'Practical software for institutions that cannot afford downtime.',
                'fr' => 'Des logiciels concrets pour des institutions qui ne peuvent pas s arreter.',
            ],
            'hero_subheading' => [
                'en' => 'We design, build and run the systems universities, schools and businesses depend on every day, and we train the engineers who build them.',
                'fr' => 'Nous concevons et exploitons les systemes dont dependent chaque jour universites, ecoles et entreprises, et nous formons les ingenieurs qui les construisent.',
            ],
            'hero_cta_label' => ['en' => 'See our work', 'fr' => 'Voir nos realisations'],
            'hero_cta_url' => '/work',
            'seo_title' => [
                'en' => 'I-NNOVA | Software for institutions, built in Bamenda',
                'fr' => 'I-NNOVA | Logiciels pour institutions, concus a Bamenda',
            ],
            'seo_description' => [
                'en' => 'I-NNOVA builds and operates software for universities, schools, hotels and retail businesses in Cameroon, and trains the engineers who build it.',
                'fr' => 'I-NNOVA concoit et exploite des logiciels pour universites, ecoles, hotels et commerces au Cameroun, et forme les ingenieurs qui les construisent.',
            ],
        ]);

        // Contact details stay empty on purpose. A placeholder phone number or
        // a guessed email domain is worse than a field the front end can hide,
        // so Sprint 5 fills these in with real, verified values.
        $settings->save();

        $this->attachImage($settings, 'og_image', 'I-NNOVA', 1200, 630);
    }
}

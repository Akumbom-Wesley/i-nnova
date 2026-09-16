<?php

namespace Database\Seeders\Placeholder;

use App\Models\SiteSetting;
use Database\Seeders\Concerns\AttachesPlaceholderImages;
use Illuminate\Database\Seeder;

/**
 * Contact details here are the real ones taken from the company roll-up, not
 * stand-ins. Sprint 5 confirms them rather than inventing them.
 */
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
                'en' => 'Driven by STEM to solve real world problems. We design, build and run the systems schools, hospitals, hotels and businesses depend on every day, and we train the engineers who build them.',
                'fr' => 'Portes par les STEM pour resoudre de vrais problemes. Nous concevons et exploitons les systemes dont dependent chaque jour ecoles, hopitaux, hotels et entreprises, et nous formons les ingenieurs qui les construisent.',
            ],
            'hero_cta_label' => ['en' => 'See our work', 'fr' => 'Voir nos realisations'],
            'hero_cta_url' => '/work',

            'about_heading' => [
                'en' => 'We do not just build software. We build the builders.',
                'fr' => 'Nous ne construisons pas que des logiciels. Nous formons ceux qui les construisent.',
            ],
            'about_story' => [
                'en' => '<p>I-NNOVA is a technology company committed to building smart, innovative solutions that solve real world problems and transform communities.</p><p>We work from Bamenda, in the North West Region of Cameroon, with institutions that cannot afford a system to go down: schools, hospitals, hotels and retailers. Placeholder copy beyond this point, replaced in Sprint 5.</p>',
                'fr' => '<p>I-NNOVA est une entreprise technologique engagee a creer des solutions innovantes qui resolvent de vrais problemes et transforment les communautes.</p><p>Nous travaillons depuis Bamenda, dans la Region du Nord-Ouest du Cameroun, avec des institutions qui ne peuvent pas se permettre une panne. Texte provisoire au-dela de ce point.</p>',
            ],

            'contact_email' => 'contact@i-nnovacmr.com',
            'contact_phone' => '+237 671 008 494',
            'whatsapp_number' => '+237671008494',
            'address' => [
                'en' => "City Chemist, Belgocam Building\nBamenda, North West Region\nCameroon",
                'fr' => "City Chemist, Immeuble Belgocam\nBamenda, Region du Nord-Ouest\nCameroun",
            ],

            'seo_title' => [
                'en' => 'I-NNOVA | Software for institutions, built in Bamenda',
                'fr' => 'I-NNOVA | Logiciels pour institutions, concus a Bamenda',
            ],
            'seo_description' => [
                'en' => 'I-NNOVA builds smart, innovative solutions that solve real world problems and transform communities. Software for schools, hospitals, hotels and retail across Cameroon.',
                'fr' => 'I-NNOVA cree des solutions innovantes qui resolvent de vrais problemes et transforment les communautes. Logiciels pour ecoles, hopitaux, hotels et commerces au Cameroun.',
            ],
        ]);

        $settings->save();

        $this->attachImage($settings, 'og_image', 'I-NNOVA', 1200, 630);
    }
}

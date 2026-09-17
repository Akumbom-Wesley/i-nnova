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

            // The main site sells the programme and hands off here.
            'kickstarter_url' => 'https://innovakickstarter.com',

            'socials' => [
                'facebook' => 'https://facebook.com/innovacm',
                'x' => 'https://twitter.com/innovacm',
                'linkedin' => 'https://linkedin.com/company/innovacm',
                'instagram' => 'https://instagram.com/innovacm',
                'github' => 'https://github.com/innovacm',
            ],

            'about_heading' => [
                'en' => 'A technology company committed to building smart, innovative solutions that solve real world problems and transform communities.',
                'fr' => 'Une entreprise technologique engagee a creer des solutions innovantes qui resolvent de vrais problemes et transforment les communautes.',
            ],

            // The word that cycles at the end of the About headline.
            'about_rotating_words' => [
                'en' => ['innovators', 'builders', 'businesses', 'institutions', 'careers'],
                'fr' => ['innovateurs', 'batisseurs', 'entreprises', 'institutions', 'carrieres'],
            ],
            // Mission and vision, in the company's own words.
            'mission' => [
                'en' => 'To develop innovative, affordable and scalable technology tailored for Cameroon, while building a world-class tech talent pipeline through hands-on education and mentorship.',
                'fr' => 'Developper des technologies innovantes, abordables et evolutives adaptees au Cameroun, tout en formant une filiere de talents tech de niveau mondial par la pratique et le mentorat.',
            ],
            'vision' => [
                'en' => 'To become Africa\'s leading dual-mission tech company, recognised both for the quality of our software and the calibre of the developers we cultivate, from Bamenda to the world.',
                'fr' => 'Devenir l entreprise tech africaine de reference a double mission, reconnue pour la qualite de nos logiciels comme pour le calibre des developpeurs que nous formons, de Bamenda au monde.',
            ],

            'about_story' => [
                'en' => '<p>I-NNOVA was founded in Bamenda in 2022, from a simple but powerful idea: Cameroon needs homegrown technology, and the talent to build it.</p><p>We work with institutions that cannot afford a system to go down, and we run an accelerator that turns people with no prior programming experience into working engineers. Both halves feed each other: the products give the programme real work, and the programme gives the products the people who build them.</p>',
                'fr' => '<p>I-NNOVA a ete fondee a Bamenda en 2022, a partir d une idee simple et puissante: le Cameroun a besoin de technologies locales, et des talents pour les construire.</p><p>Nous travaillons avec des institutions qui ne peuvent pas se permettre une panne, et nous animons un accelerateur qui transforme des debutants en ingenieurs. Les deux moities se nourrissent: les produits donnent au programme du vrai travail, et le programme donne aux produits ceux qui les construisent.</p>',
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

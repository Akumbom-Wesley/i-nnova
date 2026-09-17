<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

/**
 * Where a photograph appears. The subject lives in the title and caption;
 * this only decides which band on which page renders it.
 */
enum GalleryPlacement: string implements HasDescription, HasLabel
{
    case Hero = 'hero';
    case Home = 'home';
    case About = 'about';
    case Kickstarter = 'kickstarter';
    case KickstarterFeature = 'kickstarter_feature';

    public function getLabel(): string
    {
        return match ($this) {
            self::Hero => 'Home page hero slideshow',
            self::Home => 'Home page strip',
            self::About => 'About: life at I-NNOVA',
            self::Kickstarter => 'Kickstarter: gallery',
            self::KickstarterFeature => 'Kickstarter: the three beside the heading',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Hero => 'Slides behind the headline. Three to five reads best.',
            self::Home => 'The photo band on the home page.',
            self::About => 'The office, the location and the team at work.',
            self::Kickstarter => 'Photographs and video of the programme. Shows everything placed here.',
            self::KickstarterFeature => 'The cluster beside the page heading. Three reads best.',
        };
    }
}

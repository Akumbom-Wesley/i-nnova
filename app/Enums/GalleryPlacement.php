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

    public function getLabel(): string
    {
        return match ($this) {
            self::Hero => 'Home page hero slideshow',
            self::Home => 'Home page strip',
            self::About => 'About: life at I-NNOVA',
            self::Kickstarter => 'Kickstarter: the programme in action',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Hero => 'Slides behind the headline. Three to five reads best.',
            self::Home => 'The photo band on the home page.',
            self::About => 'The office, the location and the team at work.',
            self::Kickstarter => 'Internships and cohorts in progress.',
        };
    }
}

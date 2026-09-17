<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

/**
 * Where a stat's number comes from. A counted stat cannot go stale, which
 * matters most for the ones that read as claims: how many products are live,
 * how many businesses are served, how long the company has been going.
 */
enum StatSource: string implements HasDescription, HasLabel
{
    case Manual = 'manual';
    case ProductsLive = 'products_live';
    case ProductsTotal = 'products_total';
    case BusinessesServed = 'businesses_served';
    case YearsBuilding = 'years_building';
    case SectorsServed = 'sectors_served';
    case TeamMembers = 'team_members';
    case AcceleratorTracks = 'accelerator_tracks';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manual => 'Typed by hand',
            self::ProductsLive => 'Products live',
            self::ProductsTotal => 'Products, including unreleased',
            self::BusinessesServed => 'Businesses served',
            self::YearsBuilding => 'Years building',
            self::SectorsServed => 'Sectors served',
            self::TeamMembers => 'Team members',
            self::AcceleratorTracks => 'Accelerator tracks running',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Manual => 'Use the value typed below. For anything the site cannot count.',
            self::ProductsLive => 'Counted from products marked live.',
            self::ProductsTotal => 'Counted from every product.',
            self::BusinessesServed => 'Counted from case studies plus verified clients, with no double counting.',
            self::YearsBuilding => 'Counted from the founding year in Site Settings.',
            self::SectorsServed => 'Counted from sectors that have a product or a case study.',
            self::TeamMembers => 'Counted from the team list.',
            self::AcceleratorTracks => 'Counted from tracks marked as running.',
        };
    }

    public function isCounted(): bool
    {
        return $this !== self::Manual;
    }
}

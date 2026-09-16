<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * The old site presented unlaunched products as shipped, which is on the
 * "do not repeat" list. This split is what keeps that honest.
 */
enum ProductStatus: string implements HasColor, HasLabel
{
    case Live = 'live';
    case ComingSoon = 'coming_soon';

    public function getLabel(): string
    {
        return match ($this) {
            self::Live => 'Live',
            self::ComingSoon => 'Coming soon',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Live => 'success',
            self::ComingSoon => 'warning',
        };
    }
}

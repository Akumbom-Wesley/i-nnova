<?php

namespace App\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

/**
 * The two arrangements the brand guide allows for a co-branded pairing
 * (Section 4, Partners): the partner logo stacked under ours, or set beside
 * it. Anything else is off guide.
 */
enum PartnerLockup: string implements HasDescription, HasLabel
{
    case Horizontal = 'horizontal';
    case Vertical = 'vertical';

    public function getLabel(): string
    {
        return match ($this) {
            self::Horizontal => 'Horizontal',
            self::Vertical => 'Vertical',
        };
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Horizontal => 'Partner logo set beside ours, divided by a rule.',
            self::Vertical => 'Partner logo stacked under ours, divided by a rule.',
        };
    }
}

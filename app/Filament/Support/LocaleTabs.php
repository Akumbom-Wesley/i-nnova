<?php

namespace App\Filament\Support;

use Closure;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Renders one tab per content locale for Spatie-translatable fields.
 *
 * The official filament/spatie-laravel-translatable-plugin is abandoned, so
 * rather than depend on it we bind straight to the translation array. Spatie
 * casts every translatable attribute to an array and serialises it as
 * ['en' => ..., 'fr' => ...], so a field named "name.en" both fills and saves
 * correctly with no extra wiring.
 *
 * Usage:
 *
 *     LocaleTabs::make(fn (string $locale) => [
 *         TextInput::make("name.{$locale}")->label('Name')->required(),
 *     ])
 */
class LocaleTabs
{
    /**
     * $columns pairs short fields up inside a tab. It defaults to one, which
     * is right for a tab that is mostly prose, and is worth raising where the
     * tab is a row of short inputs each otherwise taking a whole line.
     */
    public static function make(Closure $components, string $label = 'Content', int $columns = 1): Tabs
    {
        $tabs = [];

        foreach (config('site.locales') as $locale => $name) {
            $tabs[] = Tab::make($name)->schema($components($locale))->columns($columns);
        }

        return Tabs::make($label)
            ->tabs($tabs)
            ->persistTabInQueryString()
            ->columnSpanFull();
    }

    /**
     * Fields on the default locale are the ones worth requiring. Requiring a
     * translation would block an editor from saving an English draft before
     * the French copy exists.
     */
    public static function isDefault(string $locale): bool
    {
        return $locale === config('site.default_locale');
    }
}

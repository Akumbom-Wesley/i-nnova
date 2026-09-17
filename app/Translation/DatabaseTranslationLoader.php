<?php

namespace App\Translation;

use App\Models\SiteText;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Translation\FileLoader;
use Throwable;

/**
 * Lets the admin override any fixed string without touching a view.
 *
 * Laravel resolves __('Some string') through the JSON loader, so decorating
 * that one path is enough to make every string on the site editable. Nothing
 * in the templates changes, and the language files stay as the defaults: a row
 * with no value, or no row at all, falls through to the file.
 */
class DatabaseTranslationLoader extends FileLoader
{
    /**
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array<string, string>
     */
    public function load($locale, $group, $namespace = null): array
    {
        $lines = parent::load($locale, $group, $namespace);

        // Only the JSON namespace carries the site's fixed strings.
        if ($group !== '*' || $namespace !== '*') {
            return $lines;
        }

        return array_merge($lines, $this->overrides($locale));
    }

    /**
     * @return array<string, string>
     */
    private function overrides(string $locale): array
    {
        try {
            // The database is not always there: migrations, a fresh clone, a
            // console command run before install. Falling back to the files
            // keeps the site rendering rather than failing on a missing table.
            if (! Schema::hasTable('site_texts')) {
                return [];
            }

            return Cache::rememberForever(
                SiteText::CACHE_PREFIX . $locale,
                fn (): array => SiteText::overridesFor($locale),
            );
        } catch (Throwable) {
            return [];
        }
    }
}

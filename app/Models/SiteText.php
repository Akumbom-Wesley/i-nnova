<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

/**
 * One fixed string on the site, overriding the language file for its key.
 */
class SiteText extends Model
{
    use HasFactory;
    use HasTranslations;

    public const CACHE_PREFIX = 'site-texts.';

    public array $translatable = ['value'];

    protected $guarded = [];

    protected static function booted(): void
    {
        // The loader caches per locale, so any write has to clear it or the
        // change would not show until the cache expired.
        static::saved(fn () => static::forgetCache());
        static::deleted(fn () => static::forgetCache());
    }

    public static function forgetCache(): void
    {
        foreach (array_keys(config('site.locales', [])) as $locale) {
            Cache::forget(self::CACHE_PREFIX . $locale);
        }

        // The translator keeps what it has loaded in memory for the life of
        // the process, so clearing the cache store is not enough on its own.
        // It matters for a long running worker, and for anything that saves
        // and then renders in the same request.
        $translator = app('translator');

        if (method_exists($translator, 'setLoaded')) {
            $translator->setLoaded([]);
        }
    }

    /**
     * Key to translated string, for one locale. Empty values are dropped so a
     * blank row falls through to the language file rather than blanking the
     * string on the site.
     *
     * @return array<string, string>
     */
    public static function overridesFor(string $locale): array
    {
        return static::query()
            ->get(['key', 'value'])
            ->mapWithKeys(fn (self $text): array => [
                $text->key => (string) $text->getTranslation('value', $locale, false),
            ])
            ->filter(fn (string $value): bool => $value !== '')
            ->all();
    }

    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    /**
     * The language file default, so the admin can show what a row started as.
     */
    public function defaultFor(string $locale): ?string
    {
        static $files = [];

        if (! array_key_exists($locale, $files)) {
            $path = lang_path($locale . '.json');
            $files[$locale] = is_file($path)
                ? (json_decode((string) file_get_contents($path), true) ?: [])
                : [];
        }

        return $files[$locale][$this->key] ?? null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * Singleton. Use SiteSetting::instance() everywhere rather than querying it,
 * so there is never a second row to disagree with the first.
 */
class SiteSetting extends Model implements HasMedia
{
    use HasTranslations;
    use InteractsWithMedia;

    protected static ?self $resolved = null;

    public array $translatable = [
        'hero_heading',
        'hero_subheading',
        'hero_cta_label',
        'about_heading',
        'about_rotating_words',
        'about_story',
        'mission',
        'vision',
        'address',
        'seo_title',
        'seo_description',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'socials' => 'array',
            'map_is_visible' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('og_image')->singleFile();
    }

    /**
     * Resolved once per request. The header, the footer and the page body all
     * ask for it, and firstOrCreate would otherwise run a write on the first
     * call of every request.
     */
    public static function instance(): self
    {
        return static::$resolved ??= static::firstOrCreate([]);
    }

    /**
     * Tests and the settings page need to drop the memo after a save.
     */
    public static function forgetInstance(): void
    {
        static::$resolved = null;
    }

    /**
     * Street level. Used when the admin leaves the zoom empty.
     */
    public const DEFAULT_MAP_ZOOM = 15;

    public function mapZoom(): int
    {
        return (int) ($this->map_zoom ?: self::DEFAULT_MAP_ZOOM);
    }

    public function hasMap(): bool
    {
        return $this->map_is_visible
            && filled($this->map_latitude)
            && filled($this->map_longitude);
    }

    /**
     * OpenStreetMap rather than Google: no key to manage, no consent banner to
     * add, and nothing loaded until the reader scrolls to it.
     */
    public function mapEmbedUrl(): ?string
    {
        if (! $this->hasMap()) {
            return null;
        }

        // A small box around the point. Roughly a street at zoom 15, a
        // neighbourhood lower, a building higher.
        $span = 0.02 / max(1, 2 ** ($this->mapZoom() - 15));

        return 'https://www.openstreetmap.org/export/embed.html?bbox='
            . implode(',', [
                round((float) $this->map_longitude - $span, 6),
                round((float) $this->map_latitude - $span, 6),
                round((float) $this->map_longitude + $span, 6),
                round((float) $this->map_latitude + $span, 6),
            ])
            . '&layer=mapnik&marker=' . $this->map_latitude . ',' . $this->map_longitude;
    }

    /**
     * Opens whichever maps application the reader actually uses.
     */
    public function mapDirectionsUrl(): ?string
    {
        if (! $this->hasMap()) {
            return null;
        }

        return 'https://www.openstreetmap.org/?mlat=' . $this->map_latitude
            . '&mlon=' . $this->map_longitude
            . '#map=' . $this->mapZoom() . '/' . $this->map_latitude . '/' . $this->map_longitude;
    }
}

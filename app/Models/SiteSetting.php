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

    public array $translatable = [
        'hero_heading',
        'hero_subheading',
        'hero_cta_label',
        'address',
        'seo_title',
        'seo_description',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'socials' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        // Sprint 4 needs an OG image that actually resolves; the old site's 404d.
        $this->addMediaCollection('og_image')->singleFile();
    }

    public static function instance(): self
    {
        return static::firstOrCreate([]);
    }
}

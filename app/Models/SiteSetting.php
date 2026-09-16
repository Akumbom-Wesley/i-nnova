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
}

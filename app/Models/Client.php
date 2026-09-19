<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * An institution running our software.
 *
 * This is also what used to be a case study. A client with nothing written
 * about it is a logo on the wall; one with a summary and a written story gets
 * a page of its own under /work. The difference is how much has been filled
 * in, not which model it is.
 */
class Client extends Model implements HasMedia
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;
    use InteractsWithMedia;

    public array $translatable = ['summary', 'challenge', 'solution', 'results', 'quote', 'quote_role'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /**
     * The slug is derived, not demanded.
     *
     * Only clients with a story told about them have a page, so most rows
     * never need a slug at all. Making every caller invent one, including a
     * seeder adding a logo to the wall, would be friction for nothing. The
     * suffix is only reached on a genuine collision between two institutions
     * with the same name.
     *
     * This is a convenience, not a guarantee, which is why the column is
     * nullable: seeders run inside WithoutModelEvents and this hook does not
     * fire for them. Anything that actually needs an address sets one.
     */
    protected static function booted(): void
    {
        static::saving(function (self $client): void {
            if (filled($client->slug)) {
                return;
            }

            $base = Str::slug($client->name) ?: 'client';
            $slug = $base;
            $suffix = 2;

            while (static::query()->where('slug', $slug)->whereKeyNot($client->getKey())->exists()) {
                $slug = $base . '-' . $suffix++;
            }

            $client->slug = $slug;
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('images');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Contain, not crop: a logo cropped to fill is a logo with its edges
        // cut off.
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 400, 200)
            ->nonQueued();

        $this->addMediaConversion('wide')
            ->fit(Fit::Max, 1600, 1000)
            ->quality(90)
            ->nonQueued();
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * A pivot, because an institution can run more than one of our products
     * and often does.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    /**
     * The front end must only ever render verified clients. Unverified logos
     * are on the "do not repeat" list.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    /**
     * Enough written about it to be worth a page of its own. Without a summary
     * there is nothing to put on /work but a name.
     *
     * The check reaches inside the JSON rather than testing the column for
     * NULL. A translatable field cleared in the admin is stored as
     * {"en": null}, which is a perfectly non-null column, so a plain
     * whereNotNull would keep publishing a page with an empty heading. Both
     * MySQL and SQLite resolve the -> path through json_extract.
     */
    public function scopeTold(Builder $query): Builder
    {
        $locale = config('site.default_locale');

        return $query->verified()
            ->whereNotNull("summary->{$locale}")
            ->where("summary->{$locale}", '!=', '');
    }

    public function isTold(): bool
    {
        // The default locale specifically, and without falling back, so this
        // agrees with the scope above whichever language the visitor is
        // reading. A page that exists in the listing and 404s when opened
        // would be worse than either outcome on its own.
        return $this->is_verified
            && filled($this->getTranslation('summary', config('site.default_locale'), false));
    }
}

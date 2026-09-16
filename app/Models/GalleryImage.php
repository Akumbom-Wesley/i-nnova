<?php

namespace App\Models;

use App\Enums\GalleryPlacement;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

/**
 * A photograph of the company at work: the internship running, the location,
 * the team building something.
 *
 * Every row can hold either an uploaded file or a remote URL. An upload always
 * wins, so replacing a stand-in is a matter of dropping a file on the record
 * in the admin: nothing in the templates changes.
 */
class GalleryImage extends Model implements HasMedia
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;
    use InteractsWithMedia;

    public array $translatable = ['title', 'caption', 'alt'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'placement' => GalleryPlacement::class,
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 800, 600)
            ->nonQueued();

        $this->addMediaConversion('wide')
            ->fit(Fit::Max, 1600, 1200)
            ->withResponsiveImages()
            ->nonQueued();
    }

    public function scopePlacedOn(Builder $query, GalleryPlacement $placement): Builder
    {
        return $query->where('placement', $placement)->where('is_active', true);
    }

    /**
     * True when this row is still a stand-in rather than a real photograph.
     * The admin surfaces it so nobody has to remember which are which.
     */
    public function isPlaceholder(): bool
    {
        return ! $this->hasMedia('image') && filled($this->external_url);
    }

    public function displayUrl(string $conversion = 'thumb'): ?string
    {
        if ($this->hasMedia('image')) {
            return $this->getFirstMediaUrl('image', $conversion) ?: $this->getFirstMediaUrl('image');
        }

        return $this->external_url;
    }

    /**
     * A srcset is only possible for uploads. A remote stand-in returns null
     * and the template falls back to a plain src.
     */
    public function srcset(): ?string
    {
        if (! $this->hasMedia('image')) {
            return null;
        }

        return $this->getFirstMedia('image')?->getSrcset('wide') ?: null;
    }

    public function altText(): string
    {
        return (string) ($this->alt ?: $this->title ?: '');
    }
}

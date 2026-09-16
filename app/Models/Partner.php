<?php

namespace App\Models;

use App\Enums\PartnerLockup;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Partner extends Model implements HasMedia
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;
    use InteractsWithMedia;

    public array $translatable = ['relationship'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'lockup' => PartnerLockup::class,
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        // Contain rather than crop: a partner logo must never be cut into.
        $this->addMediaConversion('wall')
            ->fit(Fit::Contain, 480, 240)
            ->quality(92)
            ->nonQueued();
    }

    /**
     * The front end must only ever render verified partners. A logo for a
     * relationship that has not been confirmed is on the "do not repeat" list.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function logoUrl(string $conversion = 'wall'): ?string
    {
        return $this->getFirstMediaUrl('logo', $conversion) ?: ($this->getFirstMediaUrl('logo') ?: null);
    }
}

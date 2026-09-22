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
        //
        // Sized for the wall at three times its rendered height. The mark is
        // 112px tall on a large screen, and a phone with a 3x display asks for
        // every one of these pixels; at the previous 480x240 the logos were
        // visibly soft once the row was made bigger.
        $this->addMediaConversion('wall')
            ->fit(Fit::Contain, 900, 400)
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

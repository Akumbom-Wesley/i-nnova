<?php

namespace App\Models;

use App\Enums\GalleryPlacement;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

    /**
     * How many photographs a page shows before the link to the full
     * gallery. Three reads as a deliberate selection; more starts to look
     * like the gallery itself and makes the link pointless.
     */
    public const FEATURED_LIMIT = 3;

    public array $translatable = ['title', 'caption', 'alt'];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'placement' => GalleryPlacement::class,
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();

        // An uploaded video, for footage not hosted on YouTube or Vimeo.
        $this->addMediaCollection('video')->singleFile();
    }

    /**
     * Quality is set explicitly rather than left to the default, because these
     * are the company's own photographs and re-encoding them softly would be a
     * visible loss. Fit::Max never enlarges, so a photograph already smaller
     * than the cap keeps its own dimensions and is only re-encoded, at 95,
     * which is indistinguishable from the source at these sizes.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 1000, 750)
            ->quality(90)
            ->nonQueued();

        $this->addMediaConversion('wide')
            ->fit(Fit::Max, 2000, 1500)
            ->quality(95)
            ->withResponsiveImages()
            ->nonQueued();
    }

    /**
     * The untouched upload. Used where the photograph is the subject rather
     * than a tile, so nothing stands between the file and the reader.
     */
    public function originalUrl(): ?string
    {
        return $this->hasMedia('image')
            ? $this->getFirstMediaUrl('image')
            : $this->external_url;
    }

    public function scopePlacedOn(Builder $query, GalleryPlacement $placement): Builder
    {
        return $query->where('placement', $placement)->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Video is either a link to YouTube or Vimeo or an uploaded file, so
     * "is this a video" is two conditions rather than a column. Kept here so
     * the filter on the gallery page and isVideo() can never disagree.
     */
    public function scopeVideos(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query
            ->where('video_url', '!=', '')
            ->orWhereHas('media', fn (Builder $media) => $media->where('collection_name', 'video')));
    }

    public function scopePhotographs(Builder $query): Builder
    {
        return $query
            ->where(fn (Builder $query) => $query->whereNull('video_url')->orWhere('video_url', ''))
            ->whereDoesntHave('media', fn (Builder $media) => $media->where('collection_name', 'video'));
    }

    /**
     * Newest first. The gallery is a stream rather than an arrangement: the
     * photographs from the cohort running now belong at the top, without
     * anyone having to drag them there.
     */
    public function scopeRecentFirst(Builder $query): Builder
    {
        return $query->orderByDesc('updated_at')->orderByDesc('id');
    }

    /**
     * The short selection a page shows before sending people to the full
     * gallery.
     *
     * Falls back to the first few in the editor's own order when nothing has
     * been marked, so uploading photographs is enough to get a section that
     * looks right, and ticking Featured is how you override that rather than
     * a step you have to remember. A page that went blank because nobody
     * ticked a box would be a worse failure than showing the wrong three.
     *
     * @return Collection<int, static>
     */
    public static function selectionFor(GalleryPlacement $placement, int $limit = self::FEATURED_LIMIT): Collection
    {
        $base = fn (): Builder => static::query()->placedOn($placement)->ordered()->with('media');

        $featured = $base()->featured()->limit($limit)->get();

        return $featured->isNotEmpty() ? $featured : $base()->limit($limit)->get();
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

    /**
     * True when this row is a video rather than a still.
     */
    public function isVideo(): bool
    {
        return filled($this->video_url) || $this->hasMedia('video');
    }

    /**
     * The file to play, for a video uploaded rather than linked.
     */
    public function videoFileUrl(): ?string
    {
        return $this->hasMedia('video') ? $this->getFirstMediaUrl('video') : null;
    }

    /**
     * A YouTube or Vimeo link turned into something an iframe can load.
     * Anything else is returned untouched, and the template links to it
     * rather than trying to embed something it does not understand.
     */
    public function videoEmbedUrl(): ?string
    {
        $url = $this->video_url;

        if (blank($url)) {
            return null;
        }

        if (preg_match('~youtube\.com/watch\?v=([\w-]+)~', $url, $m)
            || preg_match('~youtu\.be/([\w-]+)~', $url, $m)
            || preg_match('~youtube\.com/embed/([\w-]+)~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }
}

<?php

namespace App\Models;

use App\Enums\StatSource;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Stat extends Model
{
    use HasFactory;
    use HasSortOrder;
    use HasTranslations;

    public const CONTEXT_SITE = 'site';

    public const CONTEXT_KICKSTARTER = 'kickstarter';

    public array $translatable = ['label', 'caption'];

    protected $guarded = [];

    /**
     * A counted stat runs queries, and Blade evaluates the attribute more
     * than once per render, so the result is held for the instance.
     */
    private ?string $displayValue = null;

    protected function casts(): array
    {
        return [
            'source' => StatSource::class,
        ];
    }

    public function scopeContext(Builder $query, string $context): Builder
    {
        return $query->where('context', $context);
    }

    /**
     * What the front end shows. A counted stat is worked out from the records
     * that actually exist, so a number that reads as a claim cannot quietly
     * fall out of date as the site grows.
     */
    public function displayValue(): string
    {
        if ($this->displayValue !== null) {
            return $this->displayValue;
        }

        $source = $this->source ?? StatSource::Manual;

        $number = $source->isCounted()
            ? (string) $this->count($source)
            : (string) $this->value;

        // The suffix applies either way, so a typed 500 can still read
        // as 500+ without the plus being buried in the number itself.
        return $this->displayValue = $number . (string) $this->suffix;
    }

    private function count(StatSource $source): int
    {
        return match ($source) {
            StatSource::ProductsLive => Product::query()->live()->count(),
            StatSource::ProductsTotal => Product::query()->count(),
            StatSource::BusinessesServed => $this->businessesServed(),
            StatSource::YearsBuilding => $this->yearsBuilding(),
            StatSource::SectorsServed => Sector::query()
                ->where(fn (Builder $query) => $query->has('products')->orHas('clients'))
                ->count(),
            StatSource::TeamMembers => TeamMember::query()->count(),
            StatSource::AcceleratorTracks => KickstarterTrack::query()->where('is_active', true)->count(),
            StatSource::Manual => 0,
        };
    }

    /**
     * Verified clients.
     *
     * This used to merge case study institutions with client names and
     * lowercase both to avoid counting the same organisation twice. With one
     * model there is only one row per institution, so the deduplication has
     * nothing left to do.
     */
    private function businessesServed(): int
    {
        return Client::query()->verified()->count();
    }

    private function yearsBuilding(): int
    {
        $founded = SiteSetting::instance()->founded_year;

        if (blank($founded)) {
            return 0;
        }

        return max(0, (int) now()->format('Y') - (int) $founded);
    }
}

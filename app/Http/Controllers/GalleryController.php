<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\GalleryImage;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * The full Kickstarter gallery.
 *
 * The Kickstarter page carries a short selection; this is everything, newest
 * first and paged, so the set can grow to hundreds of photographs without the
 * page growing with it.
 */
class GalleryController extends Controller
{
    /**
     * Enough to fill a few screens of the grid without loading a cohort's
     * worth of photography into one response. A multiple of twelve, so the
     * last row is full at one, two, three and four columns.
     */
    private const PER_PAGE = 24;

    public function __invoke(Request $request): View
    {
        // Anything else means everything, so a mangled or guessed query
        // string shows the gallery rather than an error.
        $type = in_array($request->query('type'), ['photo', 'video'], true)
            ? $request->query('type')
            : null;

        return view('pages.gallery', [
            'settings' => SiteSetting::instance(),
            'type' => $type,
            'counts' => [
                'all' => $this->base()->count(),
                'photo' => $this->base()->photographs()->count(),
                'video' => $this->base()->videos()->count(),
            ],
            'images' => $this->base()
                ->when($type === 'photo', fn (Builder $query) => $query->photographs())
                ->when($type === 'video', fn (Builder $query) => $query->videos())
                ->recentFirst()
                ->with('media')
                ->paginate(self::PER_PAGE)
                // Keeps both ?page and ?type on the locale-prefixed URL.
                ->withQueryString(),
        ]);
    }

    private function base(): Builder
    {
        return GalleryImage::query()->placedOn(GalleryPlacement::Kickstarter);
    }
}

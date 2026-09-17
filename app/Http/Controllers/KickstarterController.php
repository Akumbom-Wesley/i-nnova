<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\AlumniOutcome;
use App\Models\GalleryImage;
use App\Models\KickstarterMentor;
use App\Models\KickstarterTrack;
use App\Models\SiteSetting;
use App\Models\Stat;
use Illuminate\Contracts\View\View;

class KickstarterController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.kickstarter', [
            'settings' => SiteSetting::instance(),
            'tracks' => KickstarterTrack::query()->where('is_active', true)->ordered()->with('media')->get(),
            'mentors' => KickstarterMentor::query()->ordered()->with('media')->get(),
            'photos' => GalleryImage::query()->placedOn(GalleryPlacement::Kickstarter)->ordered()->with('media')->get(),
            'feature' => GalleryImage::query()->placedOn(GalleryPlacement::KickstarterFeature)->ordered()->with('media')->get(),
            'alumni' => AlumniOutcome::query()->ordered()->with('track', 'media')->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_KICKSTARTER)->ordered()->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AlumniOutcome;
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
            'tracks' => KickstarterTrack::query()->where('is_active', true)->ordered()->get(),
            'mentors' => KickstarterMentor::query()->ordered()->get(),
            'alumni' => AlumniOutcome::query()->ordered()->with('track')->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_KICKSTARTER)->ordered()->get(),
        ]);
    }
}

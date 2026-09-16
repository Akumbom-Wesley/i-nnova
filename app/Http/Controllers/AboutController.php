<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\CompanyValue;
use App\Models\GalleryImage;
use App\Models\Milestone;
use App\Models\ProcessStep;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\TeamMember;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.about', [
            'settings' => SiteSetting::instance(),
            'values' => CompanyValue::query()->ordered()->get(),
            'steps' => ProcessStep::query()->ordered()->get(),
            'milestones' => Milestone::query()->ordered()->get(),
            'photos' => GalleryImage::query()->placedOn(GalleryPlacement::About)->ordered()->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_SITE)->ordered()->get(),
            // The full team, grouped by department, with anyone unassigned last.
            'departments' => TeamMember::query()->ordered()->get()->groupBy(
                fn (TeamMember $member): string => $member->department ?: 'Team',
            ),
        ]);
    }
}

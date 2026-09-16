<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\Client;
use App\Models\CompanyValue;
use App\Models\GalleryImage;
use App\Models\Milestone;
use App\Models\Partner;
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
            'partners' => Partner::query()->verified()->ordered()->get(),
            'clients' => Client::query()->verified()->ordered()->get(),
            'photos' => GalleryImage::query()->placedOn(GalleryPlacement::About)->ordered()->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_SITE)->ordered()->get(),
            // One flat list. Department is still on the model and still
            // editable, it just no longer splits the page into groups.
            'team' => TeamMember::query()->ordered()->get(),
        ]);
    }
}

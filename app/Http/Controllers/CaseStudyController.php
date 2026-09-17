<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Sector;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CaseStudyController extends Controller
{
    public function index(Request $request): View
    {
        $sector = $request->string('sector')->trim()->value();

        $caseStudies = CaseStudy::query()
            ->when($sector !== '', fn ($query) => $query->whereRelation('sector', 'slug', $sector))
            ->ordered()
            ->with('sector', 'product', 'media')
            ->get();

        return view('pages.work.index', [
            'settings' => SiteSetting::instance(),
            'caseStudies' => $caseStudies,
            // Only sectors that actually have a case study behind them, so a
            // filter can never lead to an empty page.
            'sectors' => Sector::query()->has('caseStudies')->ordered()->get(),
            'activeSector' => $sector,
        ]);
    }

    public function show(CaseStudy $caseStudy): View
    {
        $caseStudy->load('sector', 'product');

        return view('pages.work.show', [
            'settings' => SiteSetting::instance(),
            'caseStudy' => $caseStudy,
            'more' => CaseStudy::query()
                ->whereKeyNot($caseStudy->getKey())
                ->ordered()
                ->limit(2)
                ->with('sector', 'media')
                ->get(),
        ]);
    }
}

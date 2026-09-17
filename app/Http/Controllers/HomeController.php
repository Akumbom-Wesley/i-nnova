<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\CaseStudy;
use App\Models\Client;
use App\Models\CompanyValue;
use App\Models\GalleryImage;
use App\Models\Partner;
use App\Models\KickstarterTrack;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Stat;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.home', [
            'settings' => SiteSetting::instance(),
            'products' => Product::query()->live()->ordered()->with('sector')->get(),
            'comingSoon' => Product::query()->comingSoon()->ordered()->get(),
            'caseStudies' => CaseStudy::query()->where('is_featured', true)->ordered()->with('product', 'sector')->get(),
            'values' => CompanyValue::query()->ordered()->get(),
            'team' => TeamMember::query()->where('is_featured', true)->ordered()->get(),
            'tracks' => KickstarterTrack::query()->where('is_active', true)->ordered()->get(),
            'testimonials' => Testimonial::query()->where('is_featured', true)->ordered()->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_SITE)->ordered()->get(),
            // Verified only. An unconfirmed logo must never reach a page.
            'clients' => Client::query()->verified()->ordered()->get(),
            'photos' => GalleryImage::query()->placedOn(GalleryPlacement::Home)->ordered()->get(),
            'slides' => GalleryImage::query()->placedOn(GalleryPlacement::Hero)->ordered()->get(),
            // Verified only. An unconfirmed partnership must never reach a page.
            'partners' => Partner::query()->verified()->ordered()->get(),
        ]);
    }
}

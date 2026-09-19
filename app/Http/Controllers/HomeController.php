<?php

namespace App\Http\Controllers;

use App\Enums\GalleryPlacement;
use App\Models\Client;
use App\Models\CompanyValue;
use App\Models\GalleryImage;
use App\Models\KickstarterTrack;
use App\Models\Partner;
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
            'products' => Product::query()->live()->ordered()->with('sector', 'media')->get(),
            'comingSoon' => Product::query()->comingSoon()->ordered()->get(),
            // The deployments band: clients we lead with, and what they run.
            'deployments' => Client::query()->verified()->where('is_featured', true)->ordered()->with('products', 'sector', 'media')->get(),
            'values' => CompanyValue::query()->ordered()->with('media')->get(),
            'team' => TeamMember::query()->ordered()->limit(TeamMember::LEADERSHIP_COUNT)->with('media')->get(),
            'tracks' => KickstarterTrack::query()->where('is_active', true)->ordered()->with('media')->get(),
            'testimonials' => Testimonial::query()->where('is_featured', true)->ordered()->with('media')->get(),
            'stats' => Stat::query()->context(Stat::CONTEXT_SITE)->ordered()->get(),
            // Verified only. An unconfirmed logo must never reach a page.
            'clients' => Client::query()->verified()->ordered()->with('media')->get(),
            'photos' => GalleryImage::query()->placedOn(GalleryPlacement::Home)->ordered()->with('media')->get(),
            'slides' => GalleryImage::query()->placedOn(GalleryPlacement::Hero)->ordered()->with('media')->get(),
            // Verified only. An unconfirmed partnership must never reach a page.
            'partners' => Partner::query()->verified()->ordered()->with('media')->get(),
        ]);
    }
}

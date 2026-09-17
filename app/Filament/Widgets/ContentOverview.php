<?php

namespace App\Filament\Widgets;

use App\Models\CaseStudy;
use App\Models\GalleryImage;
use App\Models\KickstarterTrack;
use App\Models\Lead;
use App\Models\Product;
use App\Models\TeamMember;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * What is actually published, at a glance. Every figure links to the list it
 * came from, so the dashboard is a way in rather than a readout.
 */
class ContentOverview extends StatsOverviewWidget
{
    // Rendered with the page rather than fetched afterwards. The dashboard
    // is the first thing seen on signing in, and a row of empty cards that
    // fill a moment later reads as something being broken.
    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $live = Product::query()->live()->count();
        $comingSoon = Product::query()->comingSoon()->count();
        $unread = Lead::query()->unread()->count();

        return [
            Stat::make('Products live', (string) $live)
                ->description($comingSoon > 0 ? "{$comingSoon} still in development" : 'Nothing unreleased')
                ->descriptionIcon('heroicon-o-cube')
                ->color($live > 0 ? 'success' : 'gray')
                ->url(route('filament.admin.resources.products.index')),

            Stat::make('Deployments', (string) CaseStudy::query()->count())
                ->description('Case studies published')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('info')
                ->url(route('filament.admin.resources.case-studies.index')),

            Stat::make('Team', (string) TeamMember::query()->count())
                ->description(KickstarterTrack::query()->where('is_active', true)->count() . ' accelerator tracks running')
                ->descriptionIcon('heroicon-o-users')
                ->color('info')
                ->url(route('filament.admin.resources.team-members.index')),

            Stat::make('Unread enquiries', (string) $unread)
                ->description($unread > 0 ? 'Waiting for a reply' : 'Nothing waiting')
                ->descriptionIcon($unread > 0 ? 'heroicon-o-envelope' : 'heroicon-o-check-circle')
                ->color($unread > 0 ? 'warning' : 'success')
                ->url(route('filament.admin.resources.leads.index')),

            Stat::make('Photographs', (string) GalleryImage::query()->where('is_active', true)->count())
                ->description(GalleryImage::query()->whereDoesntHave('media')->whereNotNull('external_url')->count() . ' still stand-ins')
                ->descriptionIcon('heroicon-o-photo')
                ->color('info')
                ->url(route('filament.admin.resources.gallery-images.index')),
        ];
    }
}

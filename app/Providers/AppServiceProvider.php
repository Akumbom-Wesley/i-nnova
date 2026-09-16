<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Site chrome needs contact details and socials on every page, so the
        // header and footer get them without every controller passing them.
        View::composer(['partials.header', 'partials.footer'], function ($view): void {
            $view->with('settings', SiteSetting::instance());
        });
    }
}

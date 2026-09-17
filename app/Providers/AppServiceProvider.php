<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Translation\DatabaseTranslationLoader;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Decorating the loader rather than the translator means every
        // existing __() call picks up admin overrides with no view changes.
        $this->app->extend('translation.loader', function ($loader, $app) {
            return new DatabaseTranslationLoader($app['files'], $app['path.lang']);
        });
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

<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Translation\DatabaseTranslationLoader;
use Filament\Tables\Table;
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
        $this->configureAdminTables();

        // Site chrome needs contact details and socials on every page, so the
        // header and footer get them without every controller passing them.
        View::composer(['partials.header', 'partials.footer'], function ($view): void {
            $view->with('settings', SiteSetting::instance());
        });
    }

    /**
     * Pagination for every table in the admin, set in one place.
     *
     * Filament paginates by default but stops while a table is being
     * reordered, which is exactly when a long list hurts: dragging a
     * photograph into place would otherwise mean rendering every row in the
     * table first. Paging stays on, and the options start at 12 rather than
     * 5, because these lists are content the editor is reading, not records
     * being audited one at a time.
     */
    private function configureAdminTables(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table
                ->paginationPageOptions([12, 25, 50, 100])
                ->paginatedWhileReordering();
        });
    }
}

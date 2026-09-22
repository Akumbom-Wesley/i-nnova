<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Translation\DatabaseTranslationLoader;
use Closure;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();
        $this->configureMediaUploads();

        // Site chrome needs contact details and socials on every page, so the
        // header and footer get them without every controller passing them.
        View::composer(['partials.header', 'partials.footer'], function ($view): void {
            $view->with('settings', SiteSetting::instance());
        });
    }

    /**
     * What one address may post to the contact form.
     *
     * Two limits rather than one, because they stop different things. The per
     * minute limit stops a burst; a single daily limit would let that burst
     * through and only bite hours later. The daily limit stops a slow drip
     * that stays under the per minute limit forever, which is what a patient
     * script does once it notices it is being throttled.
     *
     * Both are generous for a person. Nobody sends four enquiries in a minute,
     * and if they send twenty in a day they are not writing them by hand.
     */
    private function configureRateLimiting(): void
    {
        RateLimiter::for('contact', fn (Request $request) => [
            Limit::perMinute(4)->by($request->ip()),
            Limit::perDay(20)->by($request->ip()),
        ]);
    }

    /**
     * No SVG, on any upload field, anywhere in the admin.
     *
     * An SVG is a document, not a picture: it can carry script, and that
     * script runs in this site's origin when the file is opened from
     * /storage. Rich text is filtered on the way out now, so an upload would
     * be the way round that, and it would be a stored XSS that survives
     * because the file looks like an image.
     *
     * Filament's image() allows it: it validates mimetypes:image/*, and
     * image/svg+xml matches. Set here rather than on each of the sixteen
     * upload fields, so a field added later is covered without anyone having
     * to remember.
     */
    private function configureMediaUploads(): void
    {
        SpatieMediaLibraryFileUpload::configureUsing(function (SpatieMediaLibraryFileUpload $upload): void {
            $upload->rule(static function (): Closure {
                return static function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    // Extension as well as reported type. The type is taken
                    // from the file that arrived, and the extension is what
                    // the web server will serve it as.
                    $isSvg = in_array((string) $value->getMimeType(), ['image/svg+xml', 'image/svg'], true)
                        || strtolower((string) $value->getClientOriginalExtension()) === 'svg';

                    if ($isSvg) {
                        $fail('SVG files are not accepted here, because they can carry scripts. Please upload a PNG, JPG or WebP.');
                    }
                };
            });
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

<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KickstarterController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Machine endpoints stay outside the locale prefix: one sitemap covers every
// language, and robots.txt has only one address by convention.
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/**
 * Every public page lives under its locale, so a page and its translation are
 * separate addresses. SetLocale registers the segment as a URL default, which
 * is why route() calls in the views need no locale argument.
 */
Route::prefix('{locale}')
    ->whereIn('locale', array_keys(config('site.locales')))
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

        Route::get('/work', [ClientController::class, 'index'])->name('work.index');
        Route::get('/work/{client:slug}', [ClientController::class, 'show'])->name('work.show');

        Route::get('/about', AboutController::class)->name('about');
        Route::get('/kickstarter', KickstarterController::class)->name('kickstarter');
        Route::get('/kickstarter/gallery', GalleryController::class)->name('kickstarter.gallery');

        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::post('/contact', [ContactController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('contact.store');
    });

// Anything without a locale goes to the default one rather than 404ing.
Route::get('/{path?}', function (?string $path = null) {
    return redirect('/' . config('site.default_locale') . ($path ? '/' . $path : ''), 302);
})->where('path', '.*')->name('locale.fallback');

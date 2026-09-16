<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Models\Product;
use Illuminate\Http\Response;

/**
 * Generated on request rather than written to disk, so it can never drift from
 * what is actually published.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $locales = array_keys(config('site.locales'));

        $routes = [
            ['name' => 'home', 'params' => [], 'priority' => '1.0', 'frequency' => 'weekly'],
            ['name' => 'products.index', 'params' => [], 'priority' => '0.9', 'frequency' => 'weekly'],
            ['name' => 'work.index', 'params' => [], 'priority' => '0.9', 'frequency' => 'weekly'],
            ['name' => 'kickstarter', 'params' => [], 'priority' => '0.8', 'frequency' => 'monthly'],
            ['name' => 'about', 'params' => [], 'priority' => '0.7', 'frequency' => 'monthly'],
            ['name' => 'contact', 'params' => [], 'priority' => '0.6', 'frequency' => 'yearly'],
        ];

        foreach (Product::query()->live()->ordered()->get() as $product) {
            $routes[] = ['name' => 'products.show', 'params' => ['product' => $product->slug], 'priority' => '0.8', 'frequency' => 'monthly'];
        }

        foreach (CaseStudy::query()->ordered()->get() as $caseStudy) {
            $routes[] = ['name' => 'work.show', 'params' => ['caseStudy' => $caseStudy->slug], 'priority' => '0.7', 'frequency' => 'monthly'];
        }

        return response()
            ->view('sitemap', ['routes' => $routes, 'locales' => $locales])
            ->header('Content-Type', 'application/xml');
    }
}

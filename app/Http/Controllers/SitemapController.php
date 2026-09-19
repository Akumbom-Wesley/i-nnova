<?php

namespace App\Http\Controllers;

use App\Models\Client;
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
            ['name' => 'kickstarter.gallery', 'params' => [], 'priority' => '0.6', 'frequency' => 'monthly'],
            ['name' => 'about', 'params' => [], 'priority' => '0.7', 'frequency' => 'monthly'],
            ['name' => 'contact', 'params' => [], 'priority' => '0.6', 'frequency' => 'yearly'],
        ];

        foreach (Product::query()->live()->ordered()->get() as $product) {
            $routes[] = ['name' => 'products.show', 'params' => ['product' => $product->slug], 'priority' => '0.8', 'frequency' => 'monthly'];
        }

        // Only clients with a story told about them have a page to list.
        foreach (Client::query()->told()->ordered()->get() as $client) {
            $routes[] = ['name' => 'work.show', 'params' => ['client' => $client->slug], 'priority' => '0.7', 'frequency' => 'monthly'];
        }

        return response()
            ->view('sitemap', ['routes' => $routes, 'locales' => $locales])
            ->header('Content-Type', 'application/xml');
    }
}

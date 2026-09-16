<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('pages.products.index', [
            'settings' => SiteSetting::instance(),
            'live' => Product::query()->live()->ordered()->with('sector')->get(),
            'comingSoon' => Product::query()->comingSoon()->ordered()->with('sector')->get(),
        ]);
    }

    public function show(Product $product): View
    {
        $product->load('sector', 'caseStudies', 'testimonials');

        return view('pages.products.show', [
            'settings' => SiteSetting::instance(),
            'product' => $product,
            'related' => Product::query()
                ->live()
                ->whereKeyNot($product->getKey())
                ->ordered()
                ->limit(3)
                ->get(),
        ]);
    }
}

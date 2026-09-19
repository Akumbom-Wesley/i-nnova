<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Sector;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The work: who runs our software and what it did for them.
 *
 * Only clients with something written about them appear. A client that is
 * just a logo belongs on the wall on the home page, not on a page of its own
 * with nothing on it.
 */
class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $sector = $request->string('sector')->trim()->value();

        return view('pages.work.index', [
            'settings' => SiteSetting::instance(),
            'clients' => Client::query()
                ->told()
                ->when($sector !== '', fn ($query) => $query->whereRelation('sector', 'slug', $sector))
                ->ordered()
                ->with('sector', 'products', 'media')
                ->get(),
            // Only sectors with something behind them, so a filter can never
            // lead to an empty page.
            'sectors' => Sector::query()->whereHas('clients', fn ($query) => $query->told())->ordered()->get(),
            'activeSector' => $sector,
        ]);
    }

    public function show(Client $client): View
    {
        abort_unless($client->isTold(), 404);

        $client->load('sector', 'products');

        return view('pages.work.show', [
            'settings' => SiteSetting::instance(),
            'client' => $client,
            'more' => Client::query()
                ->told()
                ->whereKeyNot($client->getKey())
                ->ordered()
                ->limit(2)
                ->with('sector', 'media')
                ->get(),
        ]);
    }
}

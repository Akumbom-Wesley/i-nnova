<?php

namespace App\Http\Controllers;

use App\Enums\LeadStatus;
use App\Http\Requests\StoreLeadRequest;
use App\Models\Lead;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('pages.contact', [
            'settings' => SiteSetting::instance(),
        ]);
    }

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        Lead::create([
            ...$request->safe()->except('website'),
            'status' => LeadStatus::New,
            // Recorded so a reply goes out in the language they wrote in.
            'locale' => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('contact')
            ->with('status', 'Thank you. Your message has reached us and we will reply shortly.');
    }
}

@props(['clients'])

{{--
    Only verified clients reach this. The model scope enforces it too, but the
    guard is repeated here so a caller passing an unfiltered collection cannot
    put an unconfirmed logo on a page.
--}}
@php $shown = $clients->where('is_verified', true); @endphp

@if ($shown->isNotEmpty())
    <ul class="grid grid-cols-2 items-center gap-x-8 gap-y-10 sm:grid-cols-3 lg:grid-cols-5">
        @foreach ($shown as $index => $client)
            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms">
                @php $logo = $client->getFirstMediaUrl('logo', 'thumb') ?: $client->getFirstMediaUrl('logo'); @endphp

                <x-dynamic-component
                    :component="$client->website_url ? 'ui.logo-wall-link' : 'ui.logo-wall-item'"
                    :client="$client"
                    :logo="$logo"
                />
            </li>
        @endforeach
    </ul>
@endif

@props(['clients'])

@php $shown = $clients->where('is_verified', true); @endphp

@if ($shown->isNotEmpty())
    <ul {{ $attributes->class('flex flex-wrap items-center justify-center gap-x-12 gap-y-10 sm:gap-x-16') }}>
        @foreach ($shown as $index => $client)
            @php $logo = $client->getFirstMediaUrl('logo', 'thumb') ?: $client->getFirstMediaUrl('logo'); @endphp

            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms" class="shrink-0">
                @if ($client->website_url)
                    <a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer"
                       class="group block rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                        <x-ui.client-mark :client="$client" :logo="$logo" />
                        <span class="sr-only">{{ $client->name }}{{ __(' (opens their website)') }}</span>
                    </a>
                @else
                    <x-ui.client-mark :client="$client" :logo="$logo" />
                @endif
            </li>
        @endforeach
    </ul>
@endif

@props([
    'type' => null,
    'counts' => [],
])

{{--
    Photographs, video, or both.

    Links rather than a script, so a filtered gallery is an address that can be
    shared and a back button that works. Counts sit in the labels, so nobody
    presses a filter to find out it holds nothing.
--}}
@php
    $options = [
        ['value' => null, 'label' => __('Everything'), 'count' => $counts['all'] ?? 0],
        ['value' => 'photo', 'label' => __('Photographs'), 'count' => $counts['photo'] ?? 0],
        ['value' => 'video', 'label' => __('Video'), 'count' => $counts['video'] ?? 0],
    ];
@endphp

<div {{ $attributes->class('flex flex-wrap items-center gap-2') }}
     role="group"
     aria-label="{{ __('Filter the gallery') }}">
    @foreach ($options as $option)
        @continue($option['value'] !== null && $option['count'] === 0)

        @php
            $isCurrent = $type === $option['value'];
            // Dropping ?page as well as ?type: page four of everything is
            // rarely page four of the video.
            $url = $option['value']
                ? route('kickstarter.gallery', ['type' => $option['value']])
                : route('kickstarter.gallery');
        @endphp

        <a href="{{ $url }}"
           @class([
               'inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition-colors duration-250 ease-[var(--ease-brand)]',
               'border-primary-band bg-primary-band text-white' => $isCurrent,
               'border-hairline bg-paper text-content hover:border-accent/45 hover:text-primary' => ! $isCurrent,
           ])
           @if ($isCurrent) aria-current="true" @endif>
            {{ $option['label'] }}

            <span @class([
                'rounded-full px-2 py-0.5 text-xs tabular-nums',
                'bg-white/20' => $isCurrent,
                'bg-paper-dim text-muted' => ! $isCurrent,
            ])>{{ $option['count'] }}</span>
        </a>
    @endforeach
</div>

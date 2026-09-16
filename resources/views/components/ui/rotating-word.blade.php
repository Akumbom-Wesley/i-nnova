@props([
    'words' => [],
    'interval' => 2600,
])

@php
    $words = array_values(array_filter((array) $words));
@endphp

@if ($words === [])
    {{-- Nothing to cycle, nothing to render. --}}
@elseif (count($words) === 1)
    <span {{ $attributes }}>{{ $words[0] }}</span>
@else
    {{--
        The word cycles, but every word is in the markup from the start, so a
        reader without JavaScript, or with reduced motion on, sees a complete
        sentence rather than a gap. The cycling copies are hidden from
        assistive tech; the first one is the one that is read.
    --}}
    <span
        x-data="rotatingWord({{ count($words) }}, {{ (int) $interval }})"
        {{ $attributes->class('relative inline-grid align-bottom') }}
    >
        @foreach ($words as $index => $word)
            <span
                class="word-cycle col-start-1 row-start-1 whitespace-nowrap"
                @if ($index === 0) data-active @else aria-hidden="true" @endif
                x-bind:data-active="active === {{ $index }} ? '' : null"
            >{{ $word }}</span>
        @endforeach

        {{-- A cursor, so the line reads as something being typed. --}}
        <span class="word-caret pointer-events-none absolute -right-3 bottom-1 h-[0.72em] w-0.5 bg-accent"
              aria-hidden="true"></span>
    </span>
@endif

@props([
    'image',
    'conversion' => 'thumb',
    'width' => 800,
    'height' => 600,
    'sizes' => '(min-width: 64rem) 33vw, (min-width: 40rem) 50vw, 100vw',
    'eager' => false,
])

@php
    // "original" serves the untouched upload. The hero uses it because the
    // photograph is the subject there, not a tile, and these sources are
    // already modest in size.
    $src = $conversion === 'original'
        ? $image->originalUrl()
        : $image->displayUrl($conversion);

    $srcset = $image->srcset();
@endphp

@if ($src)
    <img
        src="{{ $src }}"
        @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
        alt="{{ $image->altText() }}"
        width="{{ $width }}"
        height="{{ $height }}"
        @if ($eager) fetchpriority="high" @else loading="lazy" @endif
        decoding="async"
        {{ $attributes->class('h-full w-full object-cover') }}
    >
@endif

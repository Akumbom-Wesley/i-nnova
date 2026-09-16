@props(['class' => ''])

@php
    // The gradient needs a document-unique id: two grids on one page sharing an
    // id would make the second one reference the first one's definition.
    $fadeId = 'grid-fade-' . Str::random(8);
@endphp

{{--
    A dot mesh with a scan sweeping across it. Background texture for a light
    band, so white space still has something happening in it.
--}}
<svg viewBox="0 0 480 240" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" aria-hidden="true" focusable="false">
    <defs>
        <linearGradient id="{{ $fadeId }}" x1="0" y1="0" x2="1" y2="0">
            <stop offset="0%" stop-color="currentColor" stop-opacity="0"/>
            <stop offset="50%" stop-color="currentColor" stop-opacity="0.9"/>
            <stop offset="100%" stop-color="currentColor" stop-opacity="0"/>
        </linearGradient>
    </defs>

    <g fill="currentColor">
        @for ($row = 0; $row < 6; $row++)
            @for ($col = 0; $col < 12; $col++)
                <circle
                    cx="{{ 20 + $col * 40 }}"
                    cy="{{ 20 + $row * 40 }}"
                    r="2.5"
                    class="grid-dot"
                    style="--dot-delay: {{ number_format((($row + $col) % 7) * 0.28, 2) }}s"
                />
            @endfor
        @endfor
    </g>

    {{-- The sweep. --}}
    <rect x="-60" y="0" width="60" height="240" fill="url(#{{ $fadeId }})" class="grid-scan"/>
</svg>

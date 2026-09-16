@props(['class' => ''])

{{--
    A node graph: institutions connected to a hub. Edges carry traffic in both
    directions, which is the shape of a deployed system rather than a diagram.
--}}
<svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" aria-hidden="true" focusable="false">

    {{-- Edges. --}}
    <g stroke="currentColor" stroke-width="1.75" opacity="0.4">
        <path class="circuit-trace" style="--trace-length: 150" d="M200 150L74 62"/>
        <path class="circuit-trace" style="--trace-length: 150; --reveal-delay: 90ms" d="M200 150L336 70"/>
        <path class="circuit-trace" style="--trace-length: 130; --reveal-delay: 180ms" d="M200 150L46 176"/>
        <path class="circuit-trace" style="--trace-length: 140; --reveal-delay: 270ms" d="M200 150L358 186"/>
        <path class="circuit-trace" style="--trace-length: 130; --reveal-delay: 360ms" d="M200 150L120 262"/>
        <path class="circuit-trace" style="--trace-length: 140; --reveal-delay: 450ms" d="M200 150L288 266"/>
    </g>

    {{-- Traffic. --}}
    <g stroke="currentColor" stroke-width="3.5" stroke-linecap="round">
        <path class="circuit-packet" style="--path-length: 150; --packet-duration: 4s" d="M200 150L74 62"/>
        <path class="circuit-packet" style="--path-length: 150; --packet-duration: 4.6s; --packet-delay: 1.3s" d="M336 70L200 150"/>
        <path class="circuit-packet" style="--path-length: 140; --packet-duration: 5.2s; --packet-delay: 0.7s" d="M200 150L358 186"/>
        <path class="circuit-packet" style="--path-length: 130; --packet-duration: 4.4s; --packet-delay: 2.1s" d="M120 262L200 150"/>
    </g>

    {{-- Outer nodes. --}}
    <g fill="currentColor">
        @foreach ([[74, 62, 0], [336, 70, 0.5], [46, 176, 1], [358, 186, 1.5], [120, 262, 2], [288, 266, 2.5]] as [$x, $y, $delay])
            <circle cx="{{ $x }}" cy="{{ $y }}" r="7" class="circuit-node" style="--node-delay: {{ $delay }}s"/>
        @endforeach
    </g>

    {{-- The hub. --}}
    <circle cx="200" cy="150" r="26" stroke="currentColor" stroke-width="2.5" opacity="0.5"/>
    <circle cx="200" cy="150" r="13" fill="var(--color-accent)" class="circuit-core"/>
</svg>

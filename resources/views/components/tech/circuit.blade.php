@props(['class' => ''])

{{--
    The tech motif: a board with a processor at its centre, traces running out
    to solder pads, packets moving down those traces, a signal readout and a
    terminal cursor.

    Decorative, so it is hidden from assistive tech. The traces draw themselves
    as the section reveals; everything else loops. All of it stops under
    prefers-reduced-motion and the drawing still reads as a board.
--}}
<svg viewBox="0 0 420 320" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" aria-hidden="true" focusable="false">

    {{-- Traces. Right angles and 45 degree corners, as a board is actually routed. --}}
    <g stroke="currentColor" stroke-width="2" stroke-linecap="square" stroke-linejoin="round" opacity="0.5">
        <path class="circuit-trace" style="--trace-length: 210" d="M12 96h58l22-22h72"/>
        <path class="circuit-trace" style="--trace-length: 190; --reveal-delay: 120ms" d="M12 150h42l26 26h84"/>
        <path class="circuit-trace" style="--trace-length: 200; --reveal-delay: 240ms" d="M12 246h96l24-24h32"/>
        <path class="circuit-trace" style="--trace-length: 230; --reveal-delay: 360ms" d="M408 104h-64l-22 22h-58"/>
        <path class="circuit-trace" style="--trace-length: 210; --reveal-delay: 480ms" d="M408 168h-48l-26 26h-66"/>
        <path class="circuit-trace" style="--trace-length: 190; --reveal-delay: 600ms" d="M408 244h-88l-22-22h-30"/>
    </g>

    {{-- Packets running down those same routes. --}}
    <g stroke="currentColor" stroke-width="3.5" stroke-linecap="round" opacity="0.95">
        <path class="circuit-packet" style="--path-length: 210; --packet-duration: 4.4s" d="M12 96h58l22-22h72"/>
        <path class="circuit-packet" style="--path-length: 190; --packet-duration: 5.2s; --packet-delay: 1.1s" d="M12 150h42l26 26h84"/>
        <path class="circuit-packet" style="--path-length: 230; --packet-duration: 4.8s; --packet-delay: 2.3s" d="M408 104h-64l-22 22h-58"/>
        <path class="circuit-packet" style="--path-length: 210; --packet-duration: 5.6s; --packet-delay: 0.6s" d="M408 168h-48l-26 26h-66"/>
    </g>

    {{-- Solder pads at the trace ends. --}}
    <g fill="currentColor">
        <circle cx="12" cy="96" r="4" class="circuit-node"/>
        <circle cx="12" cy="150" r="4" class="circuit-node" style="--node-delay: 0.6s"/>
        <circle cx="12" cy="246" r="4" class="circuit-node" style="--node-delay: 1.2s"/>
        <circle cx="408" cy="104" r="4" class="circuit-node" style="--node-delay: 1.8s"/>
        <circle cx="408" cy="168" r="4" class="circuit-node" style="--node-delay: 2.4s"/>
        <circle cx="408" cy="244" r="4" class="circuit-node" style="--node-delay: 3s"/>
    </g>

    {{-- The processor. --}}
    <g transform="translate(150 120)">
        {{-- Pins. --}}
        <g stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity="0.7">
            <path d="M-14 16h14M-14 40h14M-14 64h14"/>
            <path d="M120 16h14M120 40h14M120 64h14"/>
            <path d="M22-14v14M58-14v14M94-14v14"/>
            <path d="M22 80v14M58 80v14M94 80v14"/>
        </g>

        <rect x="0" y="0" width="120" height="80" rx="10"
              stroke="currentColor" stroke-width="3" fill="var(--color-paper)" fill-opacity="0.04"/>

        {{-- The die, lighting as it works. --}}
        <rect x="26" y="20" width="68" height="40" rx="5" fill="var(--color-accent)" class="circuit-core"/>

        {{-- Terminal prompt on the die. --}}
        <g fill="var(--color-paper)">
            <path d="M38 33l7 7-7 7" stroke="var(--color-paper)" stroke-width="3"
                  stroke-linecap="round" stroke-linejoin="round" fill="none"/>
            <rect x="52" y="44" width="16" height="4" rx="1" class="circuit-cursor"/>
        </g>
    </g>

    {{-- Signal readout. --}}
    <g fill="currentColor" opacity="0.65">
        <rect x="150" y="242" width="8" height="34" rx="2" class="circuit-bar"/>
        <rect x="166" y="242" width="8" height="34" rx="2" class="circuit-bar" style="--bar-delay: 0.3s"/>
        <rect x="182" y="242" width="8" height="34" rx="2" class="circuit-bar" style="--bar-delay: 0.6s"/>
        <rect x="198" y="242" width="8" height="34" rx="2" class="circuit-bar" style="--bar-delay: 0.9s"/>
        <rect x="214" y="242" width="8" height="34" rx="2" class="circuit-bar" style="--bar-delay: 1.2s"/>
        <rect x="230" y="242" width="8" height="34" rx="2" class="circuit-bar" style="--bar-delay: 1.5s"/>
    </g>

    {{-- Code brackets, the other thing this company actually makes. --}}
    <g stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.5">
        <path class="circuit-trace" style="--trace-length: 70; --reveal-delay: 700ms" d="M286 244l-14 16 14 16"/>
        <path class="circuit-trace" style="--trace-length: 70; --reveal-delay: 780ms" d="M306 244l14 16-14 16"/>
    </g>
</svg>

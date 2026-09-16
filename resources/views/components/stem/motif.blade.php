@props(['class' => ''])

{{--
    The STEM motif, assembled from the vocabulary in the "Driven by STEM"
    artwork: a flask (Science), circuit traces (Technology), a gear
    (Engineering) and an orbiting atom (Mathematics).

    Decorative, so it is hidden from assistive tech. Every animation stops
    under prefers-reduced-motion and the drawing still reads.
--}}
<svg viewBox="0 0 420 320" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" aria-hidden="true" focusable="false">

    {{-- Technology: traces that draw themselves as the section reveals. --}}
    <g stroke="currentColor" stroke-width="2" stroke-linecap="round" opacity="0.55">
        <path class="stem-trace" style="--trace-length: 220" d="M18 250h56l28-28h60"/>
        <path class="stem-trace" style="--trace-length: 190; --reveal-delay: 160ms" d="M18 196h34l30 30"/>
        <path class="stem-trace" style="--trace-length: 240; --reveal-delay: 320ms" d="M402 96h-58l-26 26h-54"/>
        <path class="stem-trace" style="--trace-length: 200; --reveal-delay: 460ms" d="M402 148h-40l-26-26"/>
    </g>

    {{-- Maths: nodes pulsing on the network. --}}
    <g fill="currentColor">
        <circle cx="162" cy="222" r="3.5" class="stem-node"/>
        <circle cx="82" cy="226" r="3.5" class="stem-node" style="--node-delay: 0.7s"/>
        <circle cx="264" cy="122" r="3.5" class="stem-node" style="--node-delay: 1.4s"/>
        <circle cx="336" cy="122" r="3.5" class="stem-node" style="--node-delay: 2.1s"/>
    </g>

    {{-- Speed streaks, as under the wordmark. --}}
    <g stroke="currentColor" stroke-width="3" stroke-linecap="round" opacity="0.4">
        <line x1="286" y1="250" x2="330" y2="250" class="stem-streak"/>
        <line x1="300" y1="264" x2="356" y2="264" class="stem-streak" style="--streak-delay: 0.4s"/>
        <line x1="316" y1="278" x2="344" y2="278" class="stem-streak" style="--streak-delay: 0.8s"/>
    </g>

    {{-- Engineering: the gear. --}}
    <g class="stem-gear" opacity="0.9">
        <path fill="currentColor" d="M104 92h12l3-14 12 5-3 14 9 9 14-3 5 12-14 3v12l14 3-5 12-14-3-9 9 3 14-12 5-3-14h-12l-3 14-12-5 3-14-9-9-14 3-5-12 14-3v-12l-14-3 5-12 14 3 9-9-3-14 12-5 3 14Z" transform="translate(-24 -22) scale(0.86)"/>
        <circle cx="86" cy="98" r="15" fill="var(--color-paper)"/>
    </g>

    {{-- Science: the flask, with rising bubbles. --}}
    <g transform="translate(222 176)">
        <path d="M22 4v34L4 84a8 8 0 0 0 7 11h50a8 8 0 0 0 7-11L50 38V4"
              stroke="currentColor" stroke-width="5" stroke-linejoin="round"/>
        <path d="M16 4h40" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>
        <path d="M13 66h46l7 16a6 6 0 0 1-5 9H11a6 6 0 0 1-5-9l7-16Z" fill="var(--color-accent)"/>
        <g fill="var(--color-paper)">
            <circle cx="26" cy="78" r="3.5" class="stem-bubble"/>
            <circle cx="40" cy="82" r="2.5" class="stem-bubble" style="--bubble-delay: 0.9s"/>
            <circle cx="50" cy="76" r="3" class="stem-bubble" style="--bubble-delay: 1.8s"/>
        </g>
    </g>

    {{-- Science and Maths: the atom. --}}
    <g transform="translate(330 196)" stroke="currentColor" stroke-width="4">
        <g class="stem-orbit">
            <ellipse cx="0" cy="0" rx="46" ry="18"/>
        </g>
        <g class="stem-orbit stem-orbit--reverse">
            <ellipse cx="0" cy="0" rx="46" ry="18" transform="rotate(60)"/>
        </g>
        <g class="stem-orbit">
            <ellipse cx="0" cy="0" rx="46" ry="18" transform="rotate(120)"/>
        </g>
        <circle cx="0" cy="0" r="9" fill="var(--color-accent)" stroke="none"/>
    </g>
</svg>

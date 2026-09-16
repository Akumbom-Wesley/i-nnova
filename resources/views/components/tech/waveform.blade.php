@props(['class' => ''])

{{--
    A signal trace that draws itself, then drifts. Used as a horizon line under
    a closing section rather than as a full illustration.
--}}
<svg viewBox="0 0 900 120" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" preserveAspectRatio="none" aria-hidden="true" focusable="false">
    <g class="wave-drift">
        <path class="wave-draw" style="--wave-length: 1100"
              d="M0 76c60 0 60-42 120-42s60 42 120 42 60-56 120-56 60 56 120 56 60-34 120-34 60 34 120 34 60-48 120-48"
              stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>

        <path class="wave-draw" style="--wave-length: 1100; --reveal-delay: 240ms"
              d="M0 96c60 0 60-26 120-26s60 26 120 26 60-34 120-34 60 34 120 34 60-20 120-20 60 20 120 20 60-30 120-30"
              stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" opacity="0.8"/>
    </g>
</svg>

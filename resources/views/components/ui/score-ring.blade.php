@props([
    'value' => 0,
    'max' => 100,
    'label' => null,
    'caption' => null,
    'size' => 180,
])

@php
    // The Career Capital Score is a ring gauge in the company artwork, so it is
    // drawn as one here rather than flattened to a number.
    $radius = 70;
    $length = 2 * M_PI * $radius;
    $fraction = $max > 0 ? max(0, min(1, $value / $max)) : 0;
    $offset = $length * (1 - $fraction);
@endphp

<div {{ $attributes->class('flex flex-col items-center text-center') }}>
    <div class="relative" style="width: {{ $size }}px; height: {{ $size }}px">
        <svg viewBox="0 0 180 180" class="h-full w-full -rotate-90" aria-hidden="true">
            <circle cx="90" cy="90" r="{{ $radius }}" fill="none"
                    stroke="currentColor" stroke-width="12" opacity="0.15"/>

            <circle cx="90" cy="90" r="{{ $radius }}" fill="none"
                    stroke="var(--color-accent)" stroke-width="12" stroke-linecap="round"
                    class="ring-value"
                    style="--ring-length: {{ round($length, 2) }}; --ring-offset: {{ round($offset, 2) }}"/>
        </svg>

        <div class="absolute inset-0 flex flex-col items-center justify-center">
            <span class="font-display text-h2 tabular-nums">{{ $value }}</span>
            <span class="text-xs opacity-60">{{ __('out of :max', ['max' => $max]) }}</span>
        </div>
    </div>

    @if ($label)
        <p class="mt-5 font-semibold">{{ $label }}</p>
    @endif

    @if ($caption)
        <p class="mt-1 max-w-xs text-sm opacity-60">{{ $caption }}</p>
    @endif
</div>

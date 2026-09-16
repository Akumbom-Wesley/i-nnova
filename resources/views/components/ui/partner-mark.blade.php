@props(['partner', 'logo'])

<div class="flex h-14 items-center justify-center">
    @if ($logo)
        {{-- Held back to a single tone so a wall of mixed logos reads as one
             row, and brought up on hover. --}}
        <img src="{{ $logo }}" alt="{{ $partner->name }}" width="240" height="120" loading="lazy" decoding="async"
             class="max-h-14 w-auto object-contain opacity-70 brightness-0 invert transition-all duration-500 ease-[var(--ease-brand)] hover:opacity-100 hover:brightness-100 hover:invert-0">
    @else
        <span class="font-display text-lg text-white/70 transition-colors duration-300 hover:text-white">
            {{ $partner->name }}
        </span>
    @endif
</div>

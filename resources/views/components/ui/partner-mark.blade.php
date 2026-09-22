@props(['partner', 'logo'])

{{--
    Sized off the row height rather than the artwork, so a set of logos drawn
    at different dimensions still lines up. The width cap stops one very wide
    wordmark taking the row on its own.
--}}
<div class="flex h-20 items-center justify-center sm:h-24 lg:h-28">
    @if ($logo)
        {{-- Held back to a single tone so a wall of mixed logos reads as one
             row, and brought up to full colour on hover. --}}
        <img src="{{ $logo }}" alt="{{ $partner->name }}" width="450" height="200" loading="lazy" decoding="async"
             class="max-h-20 w-auto max-w-[11rem] object-contain opacity-70 brightness-0 invert transition-all duration-500 ease-[var(--ease-brand)] hover:opacity-100 hover:brightness-100 hover:invert-0 sm:max-h-24 sm:max-w-[13rem] lg:max-h-28 lg:max-w-[15rem]">
    @else
        {{-- No artwork yet. The name set at the same weight as a mark keeps
             the row even rather than leaving a hole in it. --}}
        <span class="font-display text-xl text-white/70 transition-colors duration-300 hover:text-white sm:text-2xl">
            {{ $partner->name }}
        </span>
    @endif
</div>

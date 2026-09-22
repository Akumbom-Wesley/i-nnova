@props(['partner', 'logo'])

{{--
    Sized off the row height rather than the artwork, so a set of logos drawn
    at different dimensions still lines up. The width cap stops one very wide
    wordmark taking the row on its own.
--}}
<div class="flex h-24 items-center justify-center sm:h-28 lg:h-32">
    @if ($logo)
        {{--
            Shown as supplied. An earlier version held every mark to a single
            tone and brought the real thing back on hover, which looks tidy
            with monochrome artwork and turns a logo that has its own
            background into a grey rectangle until the reader happens to point
            at it. A logo nobody can see is not doing its job.
        --}}
        <img src="{{ $logo }}" alt="{{ $partner->name }}" width="450" height="200" loading="lazy" decoding="async"
             class="max-h-24 w-auto max-w-[13rem] object-contain transition-transform duration-500 ease-[var(--ease-brand)] hover:scale-105 sm:max-h-28 sm:max-w-[15rem] lg:max-h-32 lg:max-w-[17rem]">
    @else
        {{-- No artwork yet. The name set at the same weight as a mark keeps
             the row even rather than leaving a hole in it. --}}
        <span class="font-display text-xl text-content/70 transition-colors duration-300 hover:text-content sm:text-2xl">
            {{ $partner->name }}
        </span>
    @endif
</div>

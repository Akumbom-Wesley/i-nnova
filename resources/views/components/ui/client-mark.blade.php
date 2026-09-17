@props(['client', 'logo'])

<div class="flex h-14 items-center justify-center">
    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $client->name }}" width="240" height="120" loading="lazy" decoding="async"
             class="max-h-14 w-auto object-contain opacity-70 brightness-0 invert transition-all duration-500 ease-[var(--ease-brand)] group-hover:opacity-100 group-hover:brightness-100 group-hover:invert-0">
    @else
        <span class="font-display text-lg text-white/70 transition-colors duration-300 group-hover:text-white">
            {{ $client->name }}
        </span>
    @endif
</div>

@props(['client', 'logo'])

<div class="flex h-16 items-center justify-center">
    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $client->name }}" loading="lazy"
             class="max-h-16 w-auto object-contain opacity-60 grayscale transition-all duration-500 ease-[var(--ease-brand)] hover:opacity-100 hover:grayscale-0">
    @else
        <span class="text-center text-sm font-semibold text-muted">{{ $client->name }}</span>
    @endif
</div>

@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'tone' => 'light',
    'align' => 'left',
])

@php
    $isDark = $tone === 'dark';
    $centred = $align === 'center';
@endphp

<x-ui.reveal class="{{ $centred ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl' }}">
    @if ($eyebrow)
        <p class="text-eyebrow font-semibold uppercase {{ $isDark ? 'text-accent' : 'text-accent-text' }}">
            {{ $eyebrow }}
        </p>
    @endif

    @if ($title)
        <h2 class="mt-4 font-display text-h2 {{ $isDark ? 'text-white' : 'text-ink' }}">
            {{ $title }}
        </h2>
    @endif

    {{-- The orange rule draws itself in as the header reveals. --}}
    <div class="rule-draw mt-5 h-0.5 w-16 bg-accent {{ $centred ? 'mx-auto' : '' }}" aria-hidden="true"></div>

    @if ($lead)
        <p class="text-lead mt-6 {{ $isDark ? 'text-white/70' : 'text-muted' }}">
            {{ $lead }}
        </p>
    @endif

    {{ $slot }}
</x-ui.reveal>

@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'motif' => 'circuit',
    'back' => null,
    'backLabel' => 'Back',
])

{{-- The standing header for every inner page, so they all open the same way. --}}
<header class="relative overflow-hidden border-b border-ink/10 bg-paper">
    <div class="pointer-events-none absolute inset-x-0 -top-40 h-96 bg-gradient-to-b from-primary-soft to-transparent"
         aria-hidden="true"></div>

    @if ($motif)
        <x-dynamic-component
            :component="'tech.' . $motif"
            class="pointer-events-none absolute -right-20 top-0 hidden w-[28rem] text-primary/[0.11] lg:block"
        />
    @endif

    <div class="relative mx-auto max-w-6xl px-4 pt-(--spacing-band-sm) pb-(--spacing-band) sm:px-6">
        @if ($back)
            <x-ui.reveal from="none">
                <a href="{{ $back }}"
                   class="group inline-flex items-center gap-2 text-sm font-semibold text-primary">
                    <svg class="h-4 w-4 transition-transform duration-300 ease-[var(--ease-brand)] group-hover:-translate-x-1"
                         fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6 6-6-6 6-6"/>
                    </svg>
                    {{ $backLabel }}
                </a>
            </x-ui.reveal>
        @endif

        @if ($eyebrow)
            <x-ui.reveal from="none" :delay="$back ? 60 : 0">
                <p class="{{ $back ? 'mt-8' : '' }} text-eyebrow font-semibold uppercase text-accent-text">
                    {{ $eyebrow }}
                </p>
            </x-ui.reveal>
        @endif

        <x-ui.reveal :delay="80">
            <h1 class="mt-5 max-w-4xl font-display text-h1 text-ink">{{ $title }}</h1>
        </x-ui.reveal>

        <x-ui.reveal :delay="140">
            <div class="rule-draw mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
        </x-ui.reveal>

        @if ($lead)
            <x-ui.reveal :delay="200">
                <p class="text-lead mt-7 max-w-2xl text-muted">{{ $lead }}</p>
            </x-ui.reveal>
        @endif

        {{ $slot }}
    </div>
</header>

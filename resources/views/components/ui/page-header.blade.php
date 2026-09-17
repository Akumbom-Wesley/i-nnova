@props([
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'motif' => 'circuit',
    'back' => null,
    'backLabel' => 'Back',
    'tone' => 'dark',
    'media' => null,
])

@php
    // Dark is the default: a white band under a white header left the inner
    // pages opening on nothing. The gradient runs blue into navy, so white
    // text measures at worst 6.86:1 against the lightest point of it.
    $isDark = $tone === 'dark';
@endphp

{{-- The standing header for every inner page, so they all open the same way. --}}
<header @class([
    'relative overflow-hidden',
    'page-header-ground text-white' => $isDark,
    'border-b border-ink/10 bg-paper' => ! $isDark,
])>
    @unless ($isDark)
        <div class="pointer-events-none absolute inset-x-0 -top-40 h-96 bg-gradient-to-b from-primary-soft to-transparent"
             aria-hidden="true"></div>
    @endunless

    @if ($motif)
        <x-dynamic-component
            :component="'tech.' . $motif"
            :class="'pointer-events-none absolute -right-20 top-0 hidden w-[28rem] lg:block ' . ($isDark ? 'text-white/[0.13]' : 'text-primary/[0.11]')"
        />
    @endif

    <div class="relative mx-auto max-w-6xl px-4 pt-(--spacing-band-sm) pb-(--spacing-band) sm:px-6">
        <div @class(['grid items-center gap-14 lg:grid-cols-[1.15fr_1fr]' => filled($media)])>
            <div>
                @if ($back)
                    <x-ui.reveal from="none">
                        <a href="{{ $back }}"
                           class="group inline-flex items-center gap-2 text-sm font-semibold {{ $isDark ? 'text-white/80 hover:text-white' : 'text-primary' }}">
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
                        <p class="{{ $back ? 'mt-8' : '' }} text-eyebrow font-semibold uppercase {{ $isDark ? 'text-accent' : 'text-accent-text' }}">
                            {{ $eyebrow }}
                        </p>
                    </x-ui.reveal>
                @endif

                <x-ui.reveal :delay="80">
                    <h1 class="mt-5 max-w-4xl font-display text-h1 {{ $isDark ? 'text-white' : 'text-ink' }}">{{ $title }}</h1>
                </x-ui.reveal>

                <x-ui.reveal :delay="140">
                    <div class="rule-draw mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
                </x-ui.reveal>

                @if ($lead)
                    <x-ui.reveal :delay="200">
                        <p class="text-lead mt-7 max-w-2xl {{ $isDark ? 'text-white/75' : 'text-muted' }}">{{ $lead }}</p>
                    </x-ui.reveal>
                @endif

                {{ $slot }}
            </div>

            @if (filled($media))
                <x-ui.reveal from="right" :delay="220" class="hidden lg:block">
                    {{ $media }}
                </x-ui.reveal>
            @endif
        </div>
    </div>
</header>

@props([
    'href' => null,
    'image' => null,
    'imageAlt' => '',
    'eyebrow' => null,
    'title' => null,
    'body' => null,
    'badge' => null,
    'badgeTone' => 'accent',
])

@php
    $tag = $href ? 'a' : 'div';

    $badgeTones = [
        'accent' => 'bg-accent-soft text-accent-text',
        'primary' => 'bg-primary-soft text-primary',
        'muted' => 'bg-paper-dim text-muted',
    ];
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class('card-lift group flex h-full flex-col overflow-hidden rounded-2xl border border-ink/10 bg-paper hover:border-accent/40 hover:shadow-xl hover:shadow-ink/5') }}
>
    @if ($image)
        <div class="aspect-[16/10] overflow-hidden bg-paper-dim">
            <img src="{{ $image }}" alt="{{ $imageAlt }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.04]">
        </div>
    @endif

    <div class="flex flex-1 flex-col p-7">
        @if ($eyebrow || $badge)
            <div class="flex flex-wrap items-center gap-3">
                @if ($eyebrow)
                    <span class="text-eyebrow font-semibold uppercase text-muted">{{ $eyebrow }}</span>
                @endif

                @if ($badge)
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $badgeTones[$badgeTone] ?? $badgeTones['accent'] }}">
                        {{ $badge }}
                    </span>
                @endif
            </div>
        @endif

        @if ($title)
            <h3 class="mt-3 font-display text-h3 text-ink">{{ $title }}</h3>
        @endif

        @if ($body)
            <p class="mt-3 flex-1 leading-relaxed text-muted">{{ $body }}</p>
        @endif

        {{ $slot }}

        @if ($href)
            <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary" aria-hidden="true">
                Read more
                <svg class="h-4 w-4 transition-transform duration-300 ease-[var(--ease-brand)] group-hover:translate-x-1"
                     fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                </svg>
            </span>
        @endif
    </div>
</{{ $tag }}>

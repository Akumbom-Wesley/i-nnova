@props([
    'quote' => null,
    'name' => null,
    'role' => null,
    'organisation' => null,
    'photo' => null,
    'tone' => 'light',
])

@php $isDark = $tone === 'dark'; @endphp

<figure {{ $attributes->class([
    'card-lift card-trace flex h-full flex-col overflow-hidden rounded-2xl border p-8',
    'border-white/15 bg-white/5' => $isDark,
    'border-ink/10 bg-paper hover:border-accent/40 hover:shadow-lg hover:shadow-ink/5' => ! $isDark,
]) }}>
    <svg class="h-7 w-7 shrink-0 {{ $isDark ? 'text-accent' : 'text-accent' }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M9.5 6C6.5 7.6 4.6 10.5 4.6 13.7c0 2.6 1.6 4.3 3.8 4.3 2 0 3.5-1.5 3.5-3.4 0-1.9-1.3-3.3-3.1-3.3-.4 0-.8.1-1 .2.4-1.6 1.9-3.2 3.7-4.2L9.5 6Zm8.4 0c-3 1.6-4.9 4.5-4.9 7.7 0 2.6 1.6 4.3 3.8 4.3 2 0 3.5-1.5 3.5-3.4 0-1.9-1.3-3.3-3.1-3.3-.4 0-.8.1-1 .2.4-1.6 1.9-3.2 3.7-4.2L17.9 6Z"/>
    </svg>

    <blockquote class="mt-6 flex-1 text-lg leading-relaxed {{ $isDark ? 'text-white/90' : 'text-ink' }}">
        {{ $quote ?? $slot }}
    </blockquote>

    <figcaption class="mt-8 flex items-center gap-4 border-t pt-6 {{ $isDark ? 'border-white/15' : 'border-ink/10' }}">
        @if ($photo)
            <img src="{{ $photo }}" alt="" width="48" height="48" loading="lazy" decoding="async" class="h-12 w-12 shrink-0 rounded-full object-cover">
        @endif

        <div class="min-w-0">
            <p class="truncate font-semibold {{ $isDark ? 'text-white' : 'text-ink' }}">{{ $name }}</p>

            @if ($role || $organisation)
                <p class="truncate text-sm {{ $isDark ? 'text-white/60' : 'text-muted' }}">
                    {{ collect([$role, $organisation])->filter()->implode(', ') }}
                </p>
            @endif
        </div>
    </figcaption>
</figure>

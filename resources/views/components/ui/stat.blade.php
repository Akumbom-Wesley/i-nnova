@props([
    'value' => null,
    'label' => null,
    'caption' => null,
    'tone' => 'light',
])

@php $isDark = $tone === 'dark'; @endphp

<div {{ $attributes->class('group') }}>
    <p class="font-display text-h1 tabular-nums {{ $isDark ? 'text-white' : 'text-primary' }}">
        {{ $value }}
    </p>

    <div class="rule-draw mt-3 h-0.5 w-10 bg-accent" aria-hidden="true"></div>

    <p class="mt-4 text-sm font-semibold {{ $isDark ? 'text-white/85' : 'text-ink' }}">
        {{ $label }}
    </p>

    @if ($caption)
        <p class="mt-1 text-sm {{ $isDark ? 'text-white/55' : 'text-muted' }}">{{ $caption }}</p>
    @endif
</div>

@props(['tone' => 'accent'])

@php
    $tones = [
        'accent' => 'bg-accent-soft text-accent-text',
        'primary' => 'bg-primary-soft text-primary',
        'muted' => 'bg-paper-dim text-muted',
        'dark' => 'bg-white/10 text-white',
    ];
@endphp

<span {{ $attributes->class('inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ' . ($tones[$tone] ?? $tones['accent'])) }}>
    {{ $slot }}
</span>

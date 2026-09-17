@props([
    'href' => null,
    'variant' => 'accent',
    'size' => 'base',
])

@php
    // White on accent is 3.50:1, which clears AA for large text and UI but not
    // body copy, so every accent label is at least 16px semibold.
    $variants = [
        'accent' => 'bg-accent text-white hover:bg-accent-dark focus-visible:outline-accent-dark',
        'primary' => 'bg-primary text-white hover:bg-primary-dark focus-visible:outline-primary-dark',
        'outline' => 'border border-content/20 text-content hover:border-content/40 hover:bg-paper-dim focus-visible:outline-primary',
        'ghost-light' => 'border border-white/30 text-white hover:border-white/60 hover:bg-white/10 focus-visible:outline-white',
    ];

    $sizes = [
        'base' => 'px-6 py-3 text-base',
        'lg' => 'px-7 py-3.5 text-lg',
    ];

    $classes = implode(' ', [
        'group inline-flex items-center justify-center gap-2 rounded-full font-semibold',
        'transition-[background-color,border-color,transform,box-shadow] duration-300 ease-[var(--ease-brand)]',
        'hover:-translate-y-0.5 active:translate-y-0',
        'focus-visible:outline-2 focus-visible:outline-offset-2',
        $variants[$variant] ?? $variants['accent'],
        $sizes[$size] ?? $sizes['base'],
    ]);
@endphp

<{{ $href ? 'a' : 'button' }}
    @if ($href) href="{{ $href }}" @else type="{{ $attributes->get('type', 'button') }}" @endif
    {{ $attributes->class($classes)->except('type') }}
>
    {{ $slot }}

    <svg class="h-4 w-4 transition-transform duration-300 ease-[var(--ease-brand)] group-hover:translate-x-1"
         fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
    </svg>
</{{ $href ? 'a' : 'button' }}>

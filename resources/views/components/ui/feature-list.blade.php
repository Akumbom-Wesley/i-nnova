@props([
    'items' => [],
    'tone' => 'light',
    'columns' => 1,
])

@php $isDark = $tone === 'dark'; @endphp

<ul {{ $attributes->class([
    'grid gap-x-8 gap-y-3',
    'sm:grid-cols-2' => $columns === 2,
]) }}>
    @foreach ($items as $index => $item)
        <li class="flex items-start gap-3" data-reveal style="--reveal-delay: {{ $index * 70 }}ms">
            <svg class="mt-0.5 h-5 w-5 shrink-0 text-accent" fill="none" stroke="currentColor"
                 stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
            </svg>

            <span class="{{ $isDark ? 'text-white/80' : 'text-muted' }}">{{ $item }}</span>
        </li>
    @endforeach
</ul>

@props([
    'images',
    'columns' => 3,
])

{{--
    A photo grid. Every tile reveals on scroll and lifts on hover, and the
    caption sits over the image rather than under it, so the grid stays even
    whether or not an editor has written one.
--}}
@if ($images->isNotEmpty())
    <ul {{ $attributes->class([
        'grid gap-4 sm:grid-cols-2',
        'lg:grid-cols-3' => $columns === 3,
        'lg:grid-cols-4' => $columns === 4,
    ]) }}>
        @foreach ($images as $index => $image)
            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms"
                @class([
                    'card-lift group relative overflow-hidden rounded-2xl bg-paper-dim',
                    // The first tile runs wide on a large screen, so the grid
                    // reads as a composition rather than a contact sheet.
                    'sm:col-span-2 lg:row-span-2' => $index === 0 && $images->count() > 2,
                ])>
                <div @class([
                    'overflow-hidden',
                    'aspect-[4/3]' => ! ($index === 0 && $images->count() > 2),
                    'aspect-[4/3] lg:aspect-square' => $index === 0 && $images->count() > 2,
                ])>
                    <x-ui.photo
                        :image="$image"
                        {{-- The featured tile renders roughly twice as wide as the
                             rest, so it takes the larger conversion. --}}
                        :conversion="$index === 0 ? 'wide' : 'thumb'"
                        :width="$index === 0 ? 1200 : 800"
                        :height="$index === 0 ? 900 : 600"
                        class="transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.04]"
                    />
                </div>

                @if ($image->caption)
                    <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/85 to-transparent p-5 pt-12">
                        <span class="text-sm font-medium text-white">{{ $image->caption }}</span>
                    </figcaption>
                @endif
            </li>
        @endforeach
    </ul>
@endif

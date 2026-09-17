@props([
    'images',
    'columns' => 3,
])

{{--
    A media grid. Every tile reveals on scroll, and a tile is a photograph, an
    embedded video or an uploaded video file depending on what the record
    carries. Captions sit over the tile rather than under it, so the grid stays
    even whether or not an editor has written one.
--}}
@if ($images->isNotEmpty())
    <ul {{ $attributes->class([
        'grid gap-4 sm:grid-cols-2',
        'lg:grid-cols-3' => $columns === 3,
        'lg:grid-cols-4' => $columns === 4,
    ]) }}>
        @foreach ($images as $index => $image)
            @php
                $isFeatured = $index === 0 && $images->count() > 2;
                $isVideo = $image->isVideo();
            @endphp

            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms"
                @class([
                    'group relative overflow-hidden rounded-2xl bg-paper-dim',
                    // A video tile does not lift: the player is something to
                    // use, not a card to hover over.
                    'card-lift' => ! $isVideo,
                    // The first tile runs wide on a large screen, so the grid
                    // reads as a composition rather than a contact sheet.
                    'sm:col-span-2 lg:row-span-2' => $isFeatured,
                ])>
                <div @class([
                    'overflow-hidden',
                    'aspect-[4/3]' => ! $isFeatured,
                    'aspect-[4/3] lg:aspect-square' => $isFeatured && ! $isVideo,
                    // Video keeps its own shape rather than being cropped square.
                    'aspect-video' => $isFeatured && $isVideo,
                ])>
                    <x-ui.media-tile
                        :image="$image"
                        {{-- The featured tile renders roughly twice as wide as the
                             rest, so it takes the larger conversion. --}}
                        :conversion="$isFeatured ? 'wide' : 'thumb'"
                        :width="$isFeatured ? 1200 : 800"
                        :height="$isFeatured ? 900 : 600"
                        :class="$isVideo ? '' : 'transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.04]'"
                    />
                </div>

                @if ($image->caption && ! $isVideo)
                    <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink/85 to-transparent p-5 pt-12">
                        <span class="text-sm font-medium text-white">{{ $image->caption }}</span>
                    </figcaption>
                @endif

                @if ($image->caption && $isVideo)
                    <figcaption class="px-5 py-4">
                        <span class="text-sm font-medium text-muted">{{ $image->caption }}</span>
                    </figcaption>
                @endif
            </li>
        @endforeach
    </ul>
@endif

@props(['images'])

{{--
    The full gallery grid.

    A mosaic rather than a contact sheet: every fourth tile runs double width
    and height, so the eye has somewhere to rest instead of scanning identical
    squares. Photographs open in a lightbox; video plays where it sits.

    No captions on the tiles. There can be hundreds of these, and a strip of
    text over each one turns a gallery into a list. The words are still there
    for anyone who opens a photograph, and the alt text is untouched.

    Every photograph is a real link to its own file, so the grid works before
    Alpine has started and keeps working with JavaScript off. The lightbox
    only intercepts the click.
--}}
<div x-data="lightbox" @keydown.escape.window="isOpen && hide()">
    <ul class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        @foreach ($images as $image)
            @php
                $isVideo = $image->isVideo();
                // Video keeps a single cell: a player stretched over four
                // cells is a lot of page for something you have to press.
                $isWide = ! $isVideo && $loop->index % 4 === 0;
                $full = $image->displayUrl('wide') ?: $image->displayUrl();
            @endphp

            <li data-reveal="scale"
                {{-- Capped, or the last tile on a full page would wait a
                     second and a half to appear. --}}
                style="--reveal-delay: {{ min($loop->index, 8) * 60 }}ms"
                @class([
                    'group relative overflow-hidden rounded-2xl bg-paper-dim',
                    'col-span-2 row-span-2' => $isWide,
                ])>
                @if ($isVideo)
                    <div class="aspect-[4/3] h-full w-full">
                        <x-ui.media-tile :image="$image" conversion="thumb" :width="800" :height="600" />
                    </div>

                @else
                    <a href="{{ $full }}"
                       data-lightbox-item
                       data-full="{{ $full }}"
                       data-alt="{{ $image->altText() }}"
                       data-caption="{{ $image->caption }}"
                       @click.prevent="show($el)"
                       @class([
                           'block h-full w-full cursor-zoom-in',
                           'aspect-[4/3]' => ! $isWide,
                       ])>
                        <x-ui.photo
                            :image="$image"
                            :conversion="$isWide ? 'wide' : 'thumb'"
                            :width="$isWide ? 1200 : 800"
                            :height="$isWide ? 900 : 600"
                            class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.04]"
                        />

                        <span class="pointer-events-none absolute inset-0 bg-ink/0 transition-colors duration-500 ease-[var(--ease-brand)] group-hover:bg-ink/15"
                              aria-hidden="true"></span>
                    </a>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- The lightbox. One instance for the whole grid, showing whichever
         photograph was pressed. --}}
    <div x-show="isOpen"
         x-cloak
         class="lightbox"
         role="dialog"
         aria-modal="true"
         :aria-label="alt || @js(__('Photograph'))"
         @keydown.tab.prevent="trapTab($event)"
         @keydown.left="move(-1)"
         @keydown.right="move(1)"
         x-transition.opacity.duration.200ms>
        <div class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <p class="text-sm font-semibold tabular-nums text-white/70"
               x-text="hasMany ? (index + 1) + ' / ' + items.length : ''"></p>

            <button type="button"
                    x-ref="close"
                    @click="hide()"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition-colors duration-200 hover:bg-white/20">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" aria-hidden="true">
                    <path d="M5 5l10 10M15 5L5 15" />
                </svg>
                <span class="sr-only">{{ __('Close') }}</span>
            </button>
        </div>

        {{-- Clicking the ground around the photograph closes it, which is
             what people expect of an overlay. The figure itself does not. --}}
        <div class="flex min-h-0 flex-1 items-center justify-center px-4 pb-4 sm:px-6" @click.self="hide()">
            <button type="button"
                    x-ref="previous"
                    x-show="hasMany"
                    @click="move(-1)"
                    class="mr-1 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/10 text-white transition-colors duration-200 hover:bg-white/20 sm:mr-3 sm:h-12 sm:w-12">
                <x-icon.chevron-left class="h-6 w-6" />
                <span class="sr-only">{{ __('Previous photograph') }}</span>
            </button>

            <figure class="lightbox-figure flex min-h-0 flex-1 flex-col items-center justify-center gap-4">
                <img :src="source" :alt="alt" class="rounded-xl">

                <figcaption class="max-w-2xl text-center text-sm text-white/70" x-show="caption" x-text="caption"></figcaption>
            </figure>

            <button type="button"
                    x-ref="next"
                    x-show="hasMany"
                    @click="move(1)"
                    class="ml-1 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-white/10 text-white transition-colors duration-200 hover:bg-white/20 sm:ml-3 sm:h-12 sm:w-12">
                <x-icon.chevron-right class="h-6 w-6" />
                <span class="sr-only">{{ __('Next photograph') }}</span>
            </button>
        </div>

    </div>
</div>

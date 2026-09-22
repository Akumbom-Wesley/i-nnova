@props(['partners'])

{{--
    Partners are marks only: no link, no caption, nothing competing with the
    logo.

    Verified only. The scope enforces it too, but the guard is repeated here so
    a caller passing an unfiltered collection cannot put an unconfirmed
    partnership on a page.
--}}
@php $shown = $partners->where('is_verified', true); @endphp

@if ($shown->isNotEmpty())
    {{--
        Whether the row scrolls is measured, not assumed. How many partners
        there are is a CMS decision, and a row of three sliding past would look
        broken while a row of twelve cut off at the edge would lose some
        entirely. So it centres while it fits and starts moving when it stops
        fitting, rechecked when the window is resized.

        The second copy exists only to make the loop seamless, which is why it
        is hidden from assistive tech: it is the same partners again.
    --}}
    <div
        x-data="{
            scrolling: false,
            duration: 40,
            measure() {
                const row = this.$refs.list.scrollWidth;
                this.scrolling = row > this.$el.clientWidth;
                // A fixed duration would crawl for two logos and race for
                // twenty. Roughly 60 pixels a second reads at about the pace
                // of an eye moving along a shelf.
                this.duration = Math.max(18, Math.round(row / 60));
            },
        }"
        x-init="measure(); $nextTick(() => measure())"
        x-on:resize.window.debounce.150ms="measure()"
        {{-- Logos load lazily, so the row is often narrower at init than it
             ends up. Measuring again once everything has arrived is what
             stops a full row sitting still because it was measured empty. --}}
        x-on:load.window="measure()"
        :class="scrolling && 'partner-marquee--scrolling'"
        {{ $attributes->class('partner-marquee relative overflow-hidden') }}
    >
        <div
            class="partner-track"
            :class="scrolling ? 'partner-track--scrolling' : 'mx-auto'"
            :style="scrolling ? `--marquee-duration: ${duration}s` : ''"
        >
            <ul x-ref="list" class="partner-list">
                @foreach ($shown as $index => $partner)
                    <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms" class="shrink-0">
                        <x-ui.partner-mark :partner="$partner" :logo="$partner->logoUrl()" />
                    </li>
                @endforeach
            </ul>

            <ul class="partner-list" aria-hidden="true" x-show="scrolling" x-cloak>
                @foreach ($shown as $partner)
                    <li class="shrink-0">
                        <x-ui.partner-mark :partner="$partner" :logo="$partner->logoUrl()" />
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

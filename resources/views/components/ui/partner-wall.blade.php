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
        A single row that wraps rather than a grid. A grid would hold every
        mark to one cell width, which reads as a table of boxes; a row lets a
        wide wordmark be wide and a square mark be square, which is how a logo
        wall is supposed to look. The gaps are large because the marks are, and
        two logos closer together than their own height read as one lockup.
    --}}
    <ul {{ $attributes->class('flex flex-wrap items-center justify-center gap-x-12 gap-y-12 sm:gap-x-16 lg:gap-x-20') }}>
        @foreach ($shown as $index => $partner)
            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms" class="shrink-0">
                <x-ui.partner-mark :partner="$partner" :logo="$partner->logoUrl()" />
            </li>
        @endforeach
    </ul>
@endif

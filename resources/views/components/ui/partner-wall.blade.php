@props(['partners'])

{{--
    Partners are marks only: no link, no caption, nothing competing with the
    logo. Clients are the ones a visitor can follow through to.

    Verified only. The scope enforces it too, but the guard is repeated here so
    a caller passing an unfiltered collection cannot put an unconfirmed
    partnership on a page.
--}}
@php $shown = $partners->where('is_verified', true); @endphp

@if ($shown->isNotEmpty())
    <ul {{ $attributes->class('flex flex-wrap items-center justify-center gap-x-12 gap-y-10 sm:gap-x-16') }}>
        @foreach ($shown as $index => $partner)
            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms" class="shrink-0">
                <x-ui.partner-mark :partner="$partner" :logo="$partner->logoUrl()" />
            </li>
        @endforeach
    </ul>
@endif

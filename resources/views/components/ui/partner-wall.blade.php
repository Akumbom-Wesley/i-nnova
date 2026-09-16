@props(['partners'])

{{--
    Verified only. The scope enforces it too, but the guard is repeated here so
    a caller passing an unfiltered collection cannot put an unconfirmed
    partnership on a page.
--}}
@php $shown = $partners->where('is_verified', true); @endphp

@if ($shown->isNotEmpty())
    <ul {{ $attributes->class('flex flex-wrap items-center justify-center gap-x-12 gap-y-10 sm:gap-x-16') }}>
        @foreach ($shown as $index => $partner)
            <li data-reveal="scale" style="--reveal-delay: {{ $index * 80 }}ms" class="shrink-0">
                @php $logo = $partner->logoUrl(); @endphp

                <x-dynamic-component
                    :component="$partner->website_url ? 'ui.partner-mark-link' : 'ui.partner-mark'"
                    :partner="$partner"
                    :logo="$logo"
                />
            </li>
        @endforeach
    </ul>
@endif

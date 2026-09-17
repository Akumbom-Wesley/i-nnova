@props([
    'partner',
    'tone' => 'dark',
])

@php
    $isDark = $tone === 'dark';
    $isVertical = $partner->lockup === \App\Enums\PartnerLockup::Vertical;
    $logo = $partner->logoUrl();
@endphp

{{--
    A co-branded pairing, per the brand guide Section 4.

    The guide is explicit that our stand-alone mark is never paired with a
    partner: the pairing always uses the logo carrying the trademark name. So
    this renders logo.png, the full lockup, and never logo-mark.png.
--}}
<div {{ $attributes->class([
    'flex items-center gap-8',
    'flex-col' => $isVertical,
    'flex-row' => ! $isVertical,
]) }}>
    <img src="{{ asset('images/logo.png') }}" alt="I-NNOVA" width="120" height="120"
         loading="lazy" decoding="async"
         class="h-16 w-auto shrink-0 object-contain {{ $isVertical ? '' : 'sm:h-20' }}">

    {{-- The dividing rule the guide shows between the two marks. --}}
    <span aria-hidden="true"
          @class([
              'shrink-0',
              'h-px w-16' => $isVertical,
              'h-16 w-px sm:h-20' => ! $isVertical,
              'bg-white/25' => $isDark,
              'bg-ink/20' => ! $isDark,
          ])></span>

    @if ($logo)
        <img src="{{ $logo }}" alt="{{ $partner->name }}" width="240" height="120"
             loading="lazy" decoding="async"
             class="h-16 w-auto shrink-0 object-contain {{ $isVertical ? '' : 'sm:h-20' }}">
    @else
        <span class="font-display text-h3 {{ $isDark ? 'text-white' : 'text-content' }}">{{ $partner->name }}</span>
    @endif
</div>

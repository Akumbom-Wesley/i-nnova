@props(['partners'])

@php
    // Verified only. The previous site published logos for relationships that
    // did not exist, and that decision is not being reopened by accident.
    $shown = $partners->where('is_verified', true);
@endphp

@if ($shown->isNotEmpty())
    {{--
        A light ground, because the marks are shown as they were supplied and
        most real logos are drawn to sit on white. On the dark band the ones
        carrying their own background read as pale rectangles.

        Padded tighter than a full band: this section is a heading and a row of
        logos, and the standard band spacing left it floating well clear of the
        section above it.
    --}}
    <section class="relative overflow-hidden bg-paper-dim py-(--spacing-band-sm)" aria-labelledby="partners">
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="none" class="text-center">
                <h2 id="partners" class="font-display text-h2 text-content">{{ __('Partners') }}</h2>

                <div class="rule-draw mx-auto mt-4 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
            </x-ui.reveal>
        </div>

        {{-- Outside the container on purpose. A row that slides should run to
             both edges of the screen, not stop short inside a margin. --}}
        <x-ui.partner-wall :partners="$shown" class="mt-8 sm:mt-10" />
    </section>
@endif

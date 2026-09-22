@props(['partners'])

@php
    // Verified only. The previous site published logos for relationships that
    // did not exist, and that decision is not being reopened by accident.
    $shown = $partners->where('is_verified', true);
@endphp

@if ($shown->isNotEmpty())
    <section class="band-sheen relative overflow-hidden bg-ink py-(--spacing-band)" aria-labelledby="partners">
        <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-24 w-full text-white/[0.10]" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            {{--
                One word and the marks, nothing else. A logo wall that carries
                an eyebrow, a heading and a sub-heading spends most of its
                height on text about the logos rather than on the logos.
            --}}
            <x-ui.reveal from="none" class="text-center">
                <h2 id="partners" class="font-display text-h2 text-white">{{ __('Partners') }}</h2>

                <div class="rule-draw mx-auto mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
            </x-ui.reveal>

            <x-ui.partner-wall :partners="$shown" class="mt-14 sm:mt-20" />
        </div>
    </section>
@endif

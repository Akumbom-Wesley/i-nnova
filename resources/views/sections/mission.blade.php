@props(['settings'])

@if (filled($settings->mission) || filled($settings->vision))
    <section class="relative overflow-hidden bg-primary py-(--spacing-band) text-white">
        <x-tech.network class="pointer-events-none absolute -right-16 top-1/2 hidden w-[26rem] -translate-y-1/2 text-white/[0.12] lg:block" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid gap-12 lg:grid-cols-2">
                @if (filled($settings->mission))
                    <x-ui.reveal from="left">
                        <p class="text-eyebrow font-semibold uppercase text-accent">{{ __('Mission') }}</p>
                        <div class="rule-draw mt-5 h-0.5 w-12 bg-accent" aria-hidden="true"></div>
                        <p class="text-lead mt-7 text-white/85">{{ $settings->mission }}</p>
                    </x-ui.reveal>
                @endif

                @if (filled($settings->vision))
                    <x-ui.reveal from="right" :delay="120">
                        <p class="text-eyebrow font-semibold uppercase text-accent">{{ __('Vision') }}</p>
                        <div class="rule-draw mt-5 h-0.5 w-12 bg-accent" aria-hidden="true"></div>
                        <p class="text-lead mt-7 text-white/85">{{ $settings->vision }}</p>
                    </x-ui.reveal>
                @endif
            </div>
        </div>
    </section>
@endif

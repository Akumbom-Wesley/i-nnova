@props(['partners'])

@php
    $verified = $partners->where('is_verified', true);
    $featured = $verified->where('is_featured', true);
    $wall = $verified->where('is_featured', false);
@endphp

@if ($verified->isNotEmpty())
    {{--
        Arranged as a single quiet row on a dark ground, in the manner of the
        reference: a heading, logos held to one tone, generous space between
        them and no captions competing with the marks.
    --}}
    <section class="relative overflow-hidden bg-ink py-(--spacing-band)" aria-labelledby="partners">
        <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-24 w-full text-white/[0.10]" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="none" class="text-center">
                <p class="text-eyebrow font-semibold uppercase text-accent">{{ __('Partners') }}</p>

                <h2 id="partners" class="mt-5 font-display text-h2 text-white">
                    {{ __('Organisations we build alongside') }}
                </h2>

                <div class="rule-draw mx-auto mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
            </x-ui.reveal>

            @if ($wall->isNotEmpty())
                <x-ui.partner-wall :partners="$wall" class="mt-14" />
            @endif

            @if ($featured->isNotEmpty())
                {{-- A featured partnership gets the co-branded lockup set out
                     in the brand guide rather than a place in the row. --}}
                <div class="mt-16 flex flex-wrap items-center justify-center gap-x-16 gap-y-12 border-t border-white/10 pt-16">
                    @foreach ($featured as $index => $partner)
                        <x-ui.reveal from="scale" :delay="$index * 110" class="text-center">
                            <x-brand.partner-lockup :partner="$partner" tone="dark" />

                            @if ($partner->relationship)
                                <p class="mt-6 text-sm text-white/55">{{ $partner->relationship }}</p>
                            @endif
                        </x-ui.reveal>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

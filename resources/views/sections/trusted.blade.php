@props(['partners', 'clients'])

@php
    // Verified only, on both sides. The previous site published logos for
    // relationships that did not exist.
    $shownPartners = $partners->where('is_verified', true);
    $shownClients = $clients->where('is_verified', true);
@endphp

@if ($shownPartners->isNotEmpty() || $shownClients->isNotEmpty())
    <section class="band-sheen relative overflow-hidden bg-ink py-(--spacing-band)" aria-labelledby="trusted">
        <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-24 w-full text-white/[0.10]" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="none" class="text-center">
                <p class="text-eyebrow font-semibold uppercase text-accent">{{ __('Trusted by') }}</p>

                <h2 id="trusted" class="mt-5 font-display text-h2 text-white">
                    {{ __('Trusted partners and clients') }}
                </h2>

                <div class="rule-draw mx-auto mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
            </x-ui.reveal>

            @if ($shownPartners->isNotEmpty())
                <x-ui.reveal from="none" class="mt-16">
                    <h3 class="text-eyebrow text-center font-semibold uppercase text-white/45">
                        {{ __('Partners') }}
                    </h3>
                </x-ui.reveal>

                {{-- Partners are marks only. Nothing to click, nothing under
                     them competing with the logo. --}}
                <x-ui.partner-wall :partners="$shownPartners" class="mt-10" />
            @endif

            @if ($shownClients->isNotEmpty())
                <x-ui.reveal from="none" class="{{ $shownPartners->isNotEmpty() ? 'mt-16 border-t border-white/10 pt-16' : 'mt-16' }}">
                    <h3 class="text-eyebrow text-center font-semibold uppercase text-white/45">
                        {{ __('Clients') }}
                    </h3>
                </x-ui.reveal>

                {{-- Clients link out to their own sites: a visitor who wants to
                     check the deployment is real should be able to. --}}
                <x-ui.client-wall :clients="$shownClients" class="mt-10" />
            @endif
        </div>
    </section>
@endif

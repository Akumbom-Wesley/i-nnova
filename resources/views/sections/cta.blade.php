@props(['settings'])

<section class="bg-paper py-(--spacing-band-lg)">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <x-ui.reveal from="scale"
                     class="relative overflow-hidden rounded-3xl border border-ink/10 bg-ink px-8 py-16 text-center text-white sm:px-16">
            <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-32 w-full text-white/[0.14]" />

            <div class="relative mx-auto max-w-2xl">
                <h2 class="font-display text-h1">
                    {{ __('Tell us what your institution is wrestling with.') }}
                </h2>

                <div class="rule-draw mx-auto mt-7 h-0.5 w-16 bg-accent" aria-hidden="true"></div>

                <p class="text-lead mt-7 text-white/70">
                    {{ __('A short conversation is usually enough to tell whether we are the right fit.') }}
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <x-ui.button :href="route('contact')" size="lg">
                        {{ __('Start a conversation') }}
                    </x-ui.button>

                    @if ($settings->contact_phone)
                        <x-ui.button :href="'tel:' . preg_replace('/\s+/', '', $settings->contact_phone)"
                                     variant="ghost-light" size="lg">
                            {{ $settings->contact_phone }}
                        </x-ui.button>
                    @endif
                </div>
            </div>
        </x-ui.reveal>
    </div>
</section>

@props(['settings'])

<section class="bg-paper py-(--spacing-band-lg)">
    <div class="mx-auto max-w-6xl px-4 sm:px-6">
        <x-ui.reveal from="scale"
                     class="relative overflow-hidden rounded-3xl border border-ink/10 bg-ink px-8 py-16 text-center text-white sm:px-16">
            <x-tech.circuit class="pointer-events-none absolute -left-32 -top-20 w-[34rem] text-white/[0.07]" />

            <div class="relative mx-auto max-w-2xl">
                <h2 class="font-display text-h1">
                    Tell us what your institution is wrestling with.
                </h2>

                <div class="rule-draw mx-auto mt-7 h-0.5 w-16 bg-accent" aria-hidden="true"></div>

                <p class="text-lead mt-7 text-white/70">
                    A short conversation is usually enough to tell whether we are the right fit.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    <x-ui.button :href="url('/contact')" size="lg">
                        Start a conversation
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

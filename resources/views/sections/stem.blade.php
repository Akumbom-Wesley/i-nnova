@props(['tracks'])

{{--
    The STEM band. This is where the positioning gets room to breathe and
    where the motif animates at full contrast rather than as a wash.
--}}
<section class="relative overflow-hidden bg-ink py-(--spacing-band-lg) text-white">
    <x-stem.motif class="pointer-events-none absolute -left-24 top-1/2 w-[38rem] -translate-y-1/2 text-white/[0.09]" />

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
            <div>
                <x-ui.reveal from="left">
                    <p class="text-eyebrow font-semibold uppercase text-accent">Driven by STEM</p>

                    <h2 class="mt-5 font-display text-h1">
                        Science, technology, engineering and maths, pointed at
                        <span class="text-accent">real world problems</span>.
                    </h2>

                    <div class="rule-draw mt-7 h-0.5 w-16 bg-accent" aria-hidden="true"></div>

                    <p class="text-lead mt-7 text-white/70">
                        The same discipline that builds the products trains the people.
                        Our accelerator runs four tracks, each one built around shipping
                        real work rather than finishing a syllabus.
                    </p>
                </x-ui.reveal>

                <x-ui.reveal from="left" :delay="160" class="mt-10">
                    <x-ui.button :href="url('/kickstarter')" size="lg">
                        Explore Kickstarter
                    </x-ui.button>
                </x-ui.reveal>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($tracks as $index => $track)
                    <x-ui.reveal from="right" :delay="$index * 110"
                                 class="card-lift rounded-2xl border border-white/15 bg-white/5 p-7 hover:border-accent/50 hover:bg-white/10">
                        <span class="font-display text-sm text-accent">0{{ $index + 1 }}</span>

                        <h3 class="mt-3 font-display text-h3 text-white">{{ $track->name }}</h3>

                        <p class="mt-3 text-sm leading-relaxed text-white/60">{{ $track->summary }}</p>
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </div>
</section>

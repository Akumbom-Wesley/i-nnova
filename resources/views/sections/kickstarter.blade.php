@props(['settings'])

<section class="relative overflow-hidden bg-primary py-(--spacing-band) text-white">
    <x-stem.motif class="pointer-events-none absolute -right-20 bottom-0 w-[30rem] text-white/[0.10]" />

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_1fr] lg:items-center">
            <div>
                <x-ui.reveal from="left">
                    <p class="text-eyebrow font-semibold uppercase text-accent">I-NNOVA Kickstarter</p>

                    <h2 class="mt-5 font-display text-h1">
                        Launch your tech career.<br>
                        <span class="text-accent">Build the future.</span>
                    </h2>

                    <p class="text-lead mt-7 max-w-xl text-white/75">
                        Real projects, industry standard tools, mentorship from working
                        engineers, and a Career Capital Score that shows where you actually stand.
                    </p>
                </x-ui.reveal>

                <x-ui.reveal from="left" :delay="140" class="mt-10 flex flex-wrap gap-4">
                    <x-ui.button :href="url('/kickstarter')" size="lg">
                        See the programme
                    </x-ui.button>

                    @if ($settings->kickstarter_url)
                        <x-ui.button :href="$settings->kickstarter_url" variant="ghost-light" size="lg">
                            Apply now
                        </x-ui.button>
                    @endif
                </x-ui.reveal>
            </div>

            {{-- Learn, build, launch. The three-beat from the roll-up. --}}
            <div class="space-y-4">
                @foreach ([
                    ['Learn', 'in-demand skills'],
                    ['Build', 'real projects'],
                    ['Launch', 'your future'],
                ] as $index => [$verb, $detail])
                    <x-ui.reveal from="right" :delay="$index * 130"
                                 class="card-lift flex items-center gap-5 rounded-2xl border border-white/15 bg-white/5 p-6 hover:border-accent/50 hover:bg-white/10">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-accent font-display text-lg text-white">
                            {{ $index + 1 }}
                        </span>

                        <p class="font-display text-h3">
                            {{ $verb }}
                            <span class="font-sans text-base font-normal text-white/60">{{ $detail }}</span>
                        </p>
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </div>
</section>

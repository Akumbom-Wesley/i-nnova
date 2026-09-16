@props(['settings'])

<section class="band-sheen relative overflow-hidden bg-primary py-(--spacing-band) text-white">
    <x-tech.network class="pointer-events-none absolute -right-16 bottom-0 w-[26rem] text-white/[0.12]" />

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
        <div class="grid gap-14 lg:grid-cols-[1.15fr_1fr] lg:items-center">
            <div>
                <x-ui.reveal from="left">
                    <p class="text-eyebrow font-semibold uppercase text-accent">I-NNOVA Kickstarter</p>

                    <h2 class="mt-5 font-display text-h1">
                        Launch your tech career.<br>
                        <span class="text-accent">Build the future.</span>
                    </h2>

                    <p class="text-lead mt-7 max-w-xl text-white/75">
                        {{ __('Hands-on learning on real projects, career focused and community driven, with a Career Capital Score that shows where you actually stand.') }}
                    </p>
                </x-ui.reveal>

                <x-ui.reveal from="left" :delay="120" class="mt-9">
                    <p class="text-eyebrow font-semibold uppercase text-white/45">What you get</p>

                    <x-ui.feature-list
                        class="mt-5"
                        tone="dark"
                        :columns="2"
                        :items="[
                            __('Hands-on projects'),
                            __('Mentorship and guidance'),
                            __('Certificates and recognition'),
                            __('Career growth opportunities'),
                        ]"
                    />
                </x-ui.reveal>

                <x-ui.reveal from="left" :delay="200" class="mt-10 flex flex-wrap gap-4">
                    <x-ui.button :href="route('kickstarter')" size="lg">
                        {{ __('See the programme') }}
                    </x-ui.button>

                    @if ($settings->kickstarter_url)
                        <x-ui.button :href="$settings->kickstarter_url" variant="ghost-light" size="lg">
                            {{ __('Apply now') }}
                        </x-ui.button>
                    @endif
                </x-ui.reveal>
            </div>

            <div class="space-y-8">
                {{-- The Career Capital Score, drawn as the ring gauge it is. --}}
                <x-ui.reveal from="scale"
                             class="rounded-2xl border border-white/15 bg-white/5 p-8">
                    <x-ui.score-ring
                        :value="27"
                        :max="100"
                        label="{{ __('Career Capital Score') }}"
                        caption="Measured across technical skills, interview performance, portfolio, collaboration and learning."
                        class="text-white"
                    />
                </x-ui.reveal>

                {{-- Learn, build, launch. The three-beat from the company artwork. --}}
                <div class="space-y-3">
                    @foreach ([
                        [__('Learn'), __('in-demand skills')],
                        [__('Build'), __('real projects')],
                        [__('Launch'), __('your future')],
                    ] as $index => [$verb, $detail])
                        <x-ui.reveal from="right" :delay="$index * 110"
                                     class="card-lift flex items-center gap-5 rounded-2xl border border-white/15 bg-white/5 p-5 hover:border-accent/50 hover:bg-white/10">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent font-display text-white">
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
    </div>
</section>

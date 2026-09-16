@extends('layouts.app')

@section('title', 'Kickstarter | I-NNOVA')
@section('description', 'Learn in-demand skills, build real projects, launch your tech career.')

@section('content')
    <x-ui.page-header
        eyebrow="I-NNOVA Kickstarter"
        title="Launch your tech career. Build the future."
        lead="Real projects, industry standard tools and workflows, mentorship from working engineers, and a Career Capital Score that shows where you actually stand."
        motif="network"
    >
        <x-ui.reveal :delay="260" class="mt-10 flex flex-wrap gap-4">
            @if ($settings->kickstarter_url)
                <x-ui.button :href="$settings->kickstarter_url" size="lg">Apply now</x-ui.button>
            @endif

            <x-ui.button href="#tracks" variant="outline" size="lg">See the tracks</x-ui.button>
        </x-ui.reveal>
    </x-ui.page-header>

    @if ($stats->isNotEmpty())
        <section class="border-b border-ink/10 bg-paper-dim py-(--spacing-band-sm)">
            <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:grid-cols-3 sm:px-6">
                @foreach ($stats as $index => $stat)
                    <x-ui.reveal :delay="$index * 110">
                        <x-ui.stat :value="$stat->value" :label="$stat->label" :caption="$stat->caption" />
                    </x-ui.reveal>
                @endforeach
            </div>
        </section>
    @endif

    @if ($tracks->isNotEmpty())
        <section id="tracks" class="scroll-mt-24 bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="Accelerator tracks"
                    title="Four ways in"
                    lead="Each track is built around shipping real work rather than finishing a syllabus."
                />

                <div class="mt-16 grid gap-6 sm:grid-cols-2">
                    @foreach ($tracks as $index => $track)
                        <x-ui.reveal :delay="$index * 90"
                                     class="card-lift h-full rounded-2xl border border-ink/10 bg-paper p-8 hover:border-accent/40 hover:shadow-lg hover:shadow-ink/5">
                            <span class="font-display text-sm text-accent-text">0{{ $index + 1 }}</span>

                            <h3 class="mt-3 font-display text-h3 text-ink">{{ $track->name }}</h3>

                            @if ($track->duration)
                                <p class="mt-2 text-sm font-semibold text-primary">{{ $track->duration }}</p>
                            @endif

                            <div class="rule-draw mt-5 h-0.5 w-10 bg-accent" aria-hidden="true"></div>

                            <p class="mt-5 leading-relaxed text-muted">{{ $track->summary }}</p>
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Career Capital Score, the measure the programme actually reports on. --}}
    <section class="relative overflow-hidden bg-primary py-(--spacing-band) text-white">
        <x-tech.circuit class="pointer-events-none absolute -left-24 top-0 hidden w-[30rem] text-white/[0.10] lg:block" />

        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <div class="grid gap-14 lg:grid-cols-[1fr_1.1fr] lg:items-center">
                <x-ui.reveal from="scale" class="rounded-2xl border border-white/15 bg-white/5 p-10">
                    <x-ui.score-ring
                        :value="27"
                        :max="100"
                        label="Career Capital Score"
                        caption="Rookie level. It climbs as you complete activities."
                        class="text-white"
                    />
                </x-ui.reveal>

                <div>
                    <x-ui.reveal from="right">
                        <p class="text-eyebrow font-semibold uppercase text-accent">Career Capital Score</p>

                        <h2 class="mt-5 font-display text-h1">Progress you can point at.</h2>

                        <p class="text-lead mt-7 text-white/75">
                            Five measures, scored out of 100, so what you have built is visible
                            to an employer rather than asserted in a CV.
                        </p>
                    </x-ui.reveal>

                    <x-ui.reveal from="right" :delay="120" class="mt-8">
                        <x-ui.feature-list
                            tone="dark"
                            :columns="2"
                            :items="[
                                'Technical skills',
                                'Interview performance',
                                'Portfolio',
                                'Collaboration',
                                'Learning',
                            ]"
                        />
                    </x-ui.reveal>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto grid max-w-6xl gap-16 px-4 sm:px-6 lg:grid-cols-2">
            <div>
                <x-ui.section-header eyebrow="Why join" title="What the programme gives you" />

                <x-ui.reveal :delay="120" class="mt-10">
                    <x-ui.feature-list :items="[
                        'Real projects that build your portfolio',
                        'Industry standard tools and workflows',
                        'Mentorship from working engineers',
                        'Career coaching and resume support',
                        'Internship and job opportunities',
                        'A community of builders and innovators',
                    ]" />
                </x-ui.reveal>
            </div>

            <div>
                <x-ui.section-header eyebrow="What you get" title="Learn, build, launch" />

                <div class="mt-10 space-y-4">
                    @foreach ([
                        ['Learn', 'in-demand skills'],
                        ['Build', 'real projects'],
                        ['Launch', 'your future'],
                    ] as $index => [$verb, $detail])
                        <x-ui.reveal :delay="$index * 110"
                                     class="card-lift flex items-center gap-5 rounded-2xl border border-ink/10 bg-paper-dim p-6 hover:border-accent/40">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent font-display text-white">
                                {{ $index + 1 }}
                            </span>

                            <p class="font-display text-h3 text-ink">
                                {{ $verb }}
                                <span class="font-sans text-base font-normal text-muted">{{ $detail }}</span>
                            </p>
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if ($mentors->isNotEmpty())
        <section class="border-y border-ink/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="Mentors"
                    title="Taught by people doing the work"
                />

                <div class="mt-16 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($mentors as $index => $mentor)
                        <x-ui.reveal :delay="$index * 90" from="scale" class="group text-center">
                            <div class="relative mx-auto aspect-square w-36 overflow-hidden rounded-full bg-paper">
                                @if ($photo = $mentor->getFirstMediaUrl('photo', 'thumb'))
                                    <img src="{{ $photo }}" alt="{{ $mentor->name }}" loading="lazy"
                                         class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-105">
                                @endif

                                <span class="pointer-events-none absolute inset-0 rounded-full ring-0 ring-accent transition-all duration-300 ease-[var(--ease-brand)] group-hover:ring-4"
                                      aria-hidden="true"></span>
                            </div>

                            <h3 class="mt-6 font-display text-lg text-ink">{{ $mentor->name }}</h3>
                            <p class="mt-1 text-sm text-muted">{{ $mentor->title }}</p>

                            @if ($mentor->credentials)
                                <p class="mt-1 text-xs text-muted/80">{{ $mentor->credentials }}</p>
                            @endif
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($alumni->isNotEmpty())
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="Alumni outcomes"
                    title="Where people went next"
                    lead="The measure of the programme is not what it teaches but what it leads to."
                />

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($alumni as $index => $person)
                        <x-ui.reveal :delay="$index * 90"
                                     class="card-lift h-full rounded-2xl border border-ink/10 bg-paper p-7 hover:border-accent/40 hover:shadow-lg hover:shadow-ink/5">
                            <div class="flex items-center gap-4">
                                @if ($photo = $person->getFirstMediaUrl('photo', 'thumb'))
                                    <img src="{{ $photo }}" alt="" loading="lazy"
                                         class="h-14 w-14 shrink-0 rounded-full object-cover">
                                @endif

                                <div class="min-w-0">
                                    <h3 class="truncate font-display text-lg text-ink">{{ $person->name }}</h3>
                                    <p class="truncate text-sm text-muted">{{ $person->role }}</p>
                                </div>
                            </div>

                            @if ($person->organisation)
                                <p class="mt-5 text-sm font-semibold text-primary">Now at {{ $person->organisation }}</p>
                            @endif

                            @if ($person->outcome)
                                <p class="mt-3 leading-relaxed text-muted">{{ $person->outcome }}</p>
                            @endif

                            <div class="mt-6 flex flex-wrap items-center gap-2">
                                @if ($person->track)
                                    <x-ui.badge tone="muted">{{ $person->track->name }}</x-ui.badge>
                                @endif

                                @if ($person->cohort_year)
                                    <x-ui.badge tone="muted">{{ $person->cohort_year }}</x-ui.badge>
                                @endif
                            </div>
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{--
        The handoff. The main site sells the programme; applications and the
        learning itself live on the Kickstarter platform.
    --}}
    <section class="bg-paper pb-(--spacing-band-lg)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="scale"
                         class="relative overflow-hidden rounded-3xl border border-ink/10 bg-ink px-8 py-16 text-center text-white sm:px-16">
                <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-32 w-full text-white/[0.14]" />

                <div class="relative mx-auto max-w-2xl">
                    <h2 class="font-display text-h1">Ready to kickstart your future?</h2>

                    <div class="rule-draw mx-auto mt-7 h-0.5 w-16 bg-accent" aria-hidden="true"></div>

                    <p class="text-lead mt-7 text-white/70">
                        Applications, tracks and your Career Capital Score all live on the
                        Kickstarter platform.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                        @if ($settings->kickstarter_url)
                            <x-ui.button :href="$settings->kickstarter_url" size="lg">Apply now</x-ui.button>
                        @endif

                        <x-ui.button :href="route('contact')" variant="ghost-light" size="lg">
                            Ask a question
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.reveal>
        </div>
    </section>
@endsection

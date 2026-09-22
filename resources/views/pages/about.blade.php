@extends('layouts.app')

@push('schema')
    <x-seo.people :people="$team" />
@endpush

@php
    $seoTitle = __('About') . ' | I-NNOVA';
    $seoDescription = __('A technology company in Bamenda building software that solves real world problems, and training the engineers who build it.');
@endphp

@section('content')
    @php
        $rotating = array_values(array_filter((array) ($settings->about_rotating_words ?? [])));
    @endphp

    <header class="relative overflow-hidden page-header-ground text-white">

        <x-tech.code class="pointer-events-none absolute -right-20 top-0 hidden w-[28rem] text-white/[0.13] lg:block" />

        <div class="relative mx-auto max-w-6xl px-4 pt-(--spacing-band-sm) pb-(--spacing-band) sm:px-6">
            <div class="grid items-center gap-14 lg:grid-cols-[1.15fr_1fr]">
                <div>
                    <x-ui.reveal from="none">
                        <p class="text-eyebrow font-semibold uppercase text-accent">{{ __('About') }}</p>
                    </x-ui.reveal>

                    <x-ui.reveal :delay="80">
                        <h1 class="mt-5 max-w-4xl font-display text-h1 text-white">
                            {{ __('Transforming communities,') }}<br>
                            {{ __('empowering') }}
                            @if ($rotating !== [])
                                <x-ui.rotating-word :words="$rotating" class="text-accent" />
                            @else
                                <span class="text-accent">{{ __('innovators') }}</span>
                            @endif
                        </h1>
                    </x-ui.reveal>

                    <x-ui.reveal :delay="140">
                        <div class="rule-draw mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>
                    </x-ui.reveal>

                    @if ($settings->about_heading)
                        <x-ui.reveal :delay="200">
                            <p class="text-lead mt-7 max-w-2xl text-white/75">{{ $settings->about_heading }}</p>
                        </x-ui.reveal>
                    @endif
                </div>

                {{-- A designed panel rather than photographs: the whole of the
                     company's photography lives on Kickstarter, and repeating
                     it here would say less than the disciplines do. --}}
                <x-ui.reveal from="right" :delay="220" class="hidden lg:block">
                    <x-tech.stem-panel />
                </x-ui.reveal>
            </div>
        </div>
    </header>

    @if (filled($settings->about_story))
        <section class="section-seam bg-paper py-(--spacing-band)">
            <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.seam />

                {{-- A quiet marker rather than a heading: the story speaks for
                     itself, it just needs something to start against.

                     On a phone the marker sits above the text and the rule runs
                     across rather than down, so the mark is still there and the
                     column it used to occupy is not left empty. --}}
                <div class="grid gap-6 lg:grid-cols-[auto_1fr] lg:gap-16">
                    <div class="flex items-center gap-4 lg:block" aria-hidden="true">
                        <span class="block font-display leading-none text-primary/15 text-[3.25rem] sm:text-[4.5rem] lg:text-[7rem] lg:text-primary/10">
                            &ldquo;
                        </span>

                        <span class="h-px flex-1 bg-gradient-to-r from-accent to-transparent lg:mt-4 lg:block lg:h-24 lg:w-px lg:flex-none lg:bg-gradient-to-b"></span>
                    </div>

                    <x-ui.reveal>
                        <x-ui.prose class="text-lead"><x-ui.rich-text :html="$settings->about_story" /></x-ui.prose>
                    </x-ui.reveal>
                </div>
            </div>
        </section>
    @endif

    @include('sections.mission', ['settings' => $settings])

    @if ($stats->isNotEmpty())
        <section class="border-y border-content/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:grid-cols-3 sm:px-6">
                @foreach ($stats as $index => $stat)
                    <x-ui.reveal :delay="$index * 110">
                        <x-ui.stat :value="$stat->displayValue()" :label="$stat->label" :caption="$stat->caption" />
                    </x-ui.reveal>
                @endforeach
            </div>
        </section>
    @endif

    @if ($values->isNotEmpty())
        <section class="section-seam section-seam--warm bg-paper py-(--spacing-band)">
            <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.seam />

                <x-ui.section-header eyebrow="{{ __('What we stand for') }}" title="{{ __('Values') }}" />

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($values as $index => $value)
                        <x-ui.reveal :delay="$index * 90"
                                     class="card-lift group relative h-full overflow-hidden rounded-2xl border border-content/10 bg-paper p-7 hover:border-accent/40 hover:shadow-lg hover:shadow-shade/5">
                            {{-- The number as a ghosted numeral behind the card, so
                                 the grid has depth without another colour in it. --}}
                            <span class="pointer-events-none absolute -right-3 -top-6 font-display text-[5.5rem] leading-none text-primary/[0.06] transition-colors duration-500 ease-[var(--ease-brand)] group-hover:text-accent/[0.10]"
                                  aria-hidden="true">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <span class="relative font-display text-sm text-accent-text">
                                {{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}
                            </span>

                            <h3 class="relative mt-3 font-display text-h3 text-content">{{ $value->title }}</h3>

                            <div class="rule-draw relative mt-4 h-0.5 w-10 bg-accent" aria-hidden="true"></div>

                            <div class="relative mt-4 leading-relaxed text-muted [&_p]:m-0"><x-ui.rich-text :html="$value->body" /></div>
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($steps->isNotEmpty())
        <section id="how-we-work" class="relative scroll-mt-24 overflow-hidden bg-ink py-(--spacing-band) text-white">
            <x-tech.code class="pointer-events-none absolute -right-16 top-1/2 hidden w-[26rem] -translate-y-1/2 text-white/[0.09] lg:block" />

            <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header eyebrow="{{ __('How we work') }}" title="{{ __('The way a project actually runs') }}" tone="dark" />

                <ol class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($steps as $index => $step)
                        <x-ui.reveal :as="'li'" :delay="$index * 90"
                                     class="card-lift rounded-2xl border border-white/15 bg-white/5 p-7 hover:border-accent/50 hover:bg-white/10">
                            <span class="font-display text-sm text-accent">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <h3 class="mt-3 font-display text-h3">{{ $step->title }}</h3>
                            <p class="mt-4 leading-relaxed text-white/65">{{ $step->summary }}</p>
                        </x-ui.reveal>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if ($team->isNotEmpty())
        <section id="team" class="scroll-mt-24 bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="{{ __('The people') }}"
                    title="{{ __('The team') }}"
                    lead="{{ __('Values and people carry the same weight here as the products do.') }}"
                />

                {{-- One flat grid. Departments were splitting three people into
                     three headed groups, which read as an org chart rather than
                     a team. --}}
                <ul class="mt-16 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($team as $index => $member)
                        <x-ui.reveal :as="'li'" :delay="$index * 80" from="scale" class="group text-center">
                            <div class="relative mx-auto aspect-square w-40 overflow-hidden rounded-full bg-paper-dim">
                                @if ($photo = $member->getFirstMediaUrl('photo', 'thumb'))
                                    <img src="{{ $photo }}" alt="{{ $member->name }}" width="160" height="160" loading="lazy" decoding="async"
                                         class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-105">
                                @endif

                                <span class="pointer-events-none absolute inset-0 rounded-full ring-0 ring-accent transition-all duration-300 ease-[var(--ease-brand)] group-hover:ring-4"
                                      aria-hidden="true"></span>
                            </div>

                            <h3 class="mt-6 font-display text-lg text-content">{{ $member->name }}</h3>
                            <p class="mt-1 text-sm text-muted">{{ $member->role }}</p>

                            @if ($member->credentials)
                                <p class="mt-1 text-xs text-muted/80">{{ $member->credentials }}</p>
                            @endif

                            @php $socials = array_filter($member->socials ?? []); @endphp

                            @if ($socials !== [])
                                <div class="mt-4 flex justify-center gap-2">
                                    @foreach ($socials as $network => $url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                           class="flex h-8 w-8 items-center justify-center rounded-full border border-content/15 transition-all duration-300 ease-[var(--ease-brand)] hover:-translate-y-0.5 hover:border-accent hover:bg-accent">
                                            <x-brand.social-icon :network="$network" class="h-3.5 w-3.5 text-muted transition-colors duration-300 hover:text-white" />
                                            <span class="sr-only">{{ $member->name }}{{ __(' on ') }}{{ $network }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </x-ui.reveal>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @include('sections.timeline', ['milestones' => $milestones])

    @include('sections.partners', ['partners' => $partners])

    {{-- The first three sit in the header cluster, so the gallery takes
         what is left rather than showing them twice. --}}
    @include('sections.gallery', [
        'images' => $photos,
        'eyebrow' => __('Inside I-NNOVA'),
        'title' => __('Life here'),
        'lead' => __('The building in Bamenda, and the team at work in it.'),
    ])

    @include('sections.map', ['settings' => $settings])

    @include('sections.cta', ['settings' => $settings])
@endsection

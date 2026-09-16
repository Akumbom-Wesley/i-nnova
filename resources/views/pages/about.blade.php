@extends('layouts.app')

@push('schema')
    <x-seo.people :people="$departments->flatten()" />
@endpush

@php
    $seoTitle = __('About') . ' | I-NNOVA';
    $seoDescription = __('A technology company in Bamenda building software that solves real world problems, and training the engineers who build it.');
@endphp

@section('content')
    <x-ui.page-header
        eyebrow="{{ __('About') }}"
        :title="$settings->about_heading ?: 'We do not just build software. We build the builders.'"
        lead="{{ __('Transforming communities, empowering innovators.') }}"
        motif="code"
    />

    @if (filled($settings->about_story))
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.reveal>
                    <x-ui.prose class="text-lead">{!! $settings->about_story !!}</x-ui.prose>
                </x-ui.reveal>
            </div>
        </section>
    @endif

    @include('sections.mission', ['settings' => $settings])

    @if ($stats->isNotEmpty())
        <section class="border-y border-ink/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto grid max-w-6xl gap-10 px-4 sm:grid-cols-3 sm:px-6">
                @foreach ($stats as $index => $stat)
                    <x-ui.reveal :delay="$index * 110">
                        <x-ui.stat :value="$stat->value" :label="$stat->label" :caption="$stat->caption" />
                    </x-ui.reveal>
                @endforeach
            </div>
        </section>
    @endif

    @if ($values->isNotEmpty())
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header eyebrow="{{ __('What we stand for') }}" title="{{ __('Values') }}" />

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($values as $index => $value)
                        <x-ui.reveal :delay="$index * 90"
                                     class="card-lift h-full rounded-2xl border border-ink/10 bg-paper p-7 hover:border-accent/40 hover:shadow-lg hover:shadow-ink/5">
                            <span class="font-display text-sm text-accent-text">0{{ $index + 1 }}</span>
                            <h3 class="mt-3 font-display text-h3 text-ink">{{ $value->title }}</h3>
                            <div class="rule-draw mt-4 h-0.5 w-10 bg-accent" aria-hidden="true"></div>
                            <div class="mt-4 leading-relaxed text-muted [&_p]:m-0">{!! $value->body !!}</div>
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($steps->isNotEmpty())
        <section class="relative overflow-hidden bg-ink py-(--spacing-band) text-white">
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

    @if ($departments->isNotEmpty())
        <section id="team" class="scroll-mt-24 bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="{{ __('The people') }}"
                    title="{{ __('The team') }}"
                    lead="{{ __('Values and people carry the same weight here as the products do.') }}"
                />

                <div class="mt-16 space-y-16">
                    @foreach ($departments as $department => $members)
                        <div>
                            <h3 class="text-eyebrow font-semibold uppercase text-muted">{{ $department }}</h3>

                            <div class="mt-8 grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
                                @foreach ($members as $index => $member)
                                    <x-ui.reveal :delay="$index * 80" from="scale" class="group text-center">
                                        <div class="relative mx-auto aspect-square w-36 overflow-hidden rounded-full bg-paper-dim">
                                            @if ($photo = $member->getFirstMediaUrl('photo', 'thumb'))
                                                <img src="{{ $photo }}" alt="{{ $member->name }}" width="144" height="144" loading="lazy" decoding="async"
                                                     class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-105">
                                            @endif

                                            <span class="pointer-events-none absolute inset-0 rounded-full ring-0 ring-accent transition-all duration-300 ease-[var(--ease-brand)] group-hover:ring-4"
                                                  aria-hidden="true"></span>
                                        </div>

                                        <h4 class="mt-6 font-display text-lg text-ink">{{ $member->name }}</h4>
                                        <p class="mt-1 text-sm text-muted">{{ $member->role }}</p>

                                        @if ($member->credentials)
                                            <p class="mt-1 text-xs text-muted/80">{{ $member->credentials }}</p>
                                        @endif

                                        @php $socials = array_filter($member->socials ?? []); @endphp

                                        @if ($socials !== [])
                                            <div class="mt-3 flex justify-center gap-4">
                                                @foreach ($socials as $network => $url)
                                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                                                       class="text-xs font-semibold uppercase tracking-wide text-primary hover:text-accent-text">
                                                        <span class="sr-only">{{ $member->name }} on </span>{{ $network }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </x-ui.reveal>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.timeline', ['milestones' => $milestones])

    @include('sections.gallery', [
        'images' => $photos,
        'eyebrow' => __('Inside I-NNOVA'),
        'title' => __('Life here'),
        'lead' => __('The building in Bamenda, and the team at work in it.'),
    ])

    @include('sections.cta', ['settings' => $settings])
@endsection

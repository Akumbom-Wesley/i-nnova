@extends('layouts.app')

@php
    $seoTitle = __('Work') . ' | I-NNOVA';
    $seoDescription = __('Institutions running I-NNOVA software every day.');
@endphp

@section('content')
    <x-ui.page-header
        eyebrow="{{ __('Deployments') }}"
        title="{{ __('Running in real institutions') }}"
        lead="{{ __('Not pilots and not demos. Systems in daily service, and the institutions behind them are named.') }}"
        motif="network"
    />

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            @if ($sectors->isNotEmpty())
                <x-ui.reveal from="none" class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('work.index') }}"
                       @class([
                           'rounded-full px-4 py-2 text-sm font-semibold transition-colors duration-200',
                           'bg-ink text-white' => $activeSector === '',
                           'border border-ink/20 text-ink hover:border-ink/40' => $activeSector !== '',
                       ])>
                        {{ __('All sectors') }}
                    </a>

                    @foreach ($sectors as $sector)
                        <a href="{{ route('work.index', ['sector' => $sector->slug]) }}"
                           @class([
                               'rounded-full px-4 py-2 text-sm font-semibold transition-colors duration-200',
                               'bg-ink text-white' => $activeSector === $sector->slug,
                               'border border-ink/20 text-ink hover:border-ink/40' => $activeSector !== $sector->slug,
                           ])>
                            {{ $sector->name }}
                        </a>
                    @endforeach
                </x-ui.reveal>
            @endif

            @if ($caseStudies->isEmpty())
                <x-ui.reveal class="mt-16 rounded-2xl border border-dashed border-ink/20 p-12 text-center">
                    <p class="text-lead text-muted">Nothing published in this sector yet.</p>

                    <x-ui.button :href="route('work.index')" variant="outline" class="mt-8">
                        {{ __('See all work') }}
                    </x-ui.button>
                </x-ui.reveal>
            @else
                <div class="mt-14 grid gap-6 md:grid-cols-2">
                    @foreach ($caseStudies as $index => $caseStudy)
                        <x-ui.reveal :delay="$index * 110" class="h-full">
                            <a href="{{ route('work.show', $caseStudy) }}"
                               class="card-lift group flex h-full flex-col rounded-2xl border border-ink/10 bg-paper p-8 hover:border-accent/40 hover:shadow-xl hover:shadow-ink/5">
                                <div class="flex items-start gap-6">
                                    @php $logo = $caseStudy->getFirstMediaUrl('logo', 'thumb') ?: $caseStudy->getFirstMediaUrl('logo'); @endphp

                                    @if ($logo)
                                        <img src="{{ $logo }}" alt="" width="80" height="80" loading="lazy"
                                             class="h-20 w-20 shrink-0 object-contain transition-transform duration-500 ease-[var(--ease-brand)] group-hover:scale-105">
                                    @endif

                                    <div class="min-w-0">
                                        @if ($caseStudy->sector)
                                            <span class="text-eyebrow font-semibold uppercase text-muted">{{ $caseStudy->sector->name }}</span>
                                        @endif

                                        <h2 class="mt-2 font-display text-h3 text-ink">{{ $caseStudy->institution }}</h2>

                                        @if ($caseStudy->product)
                                            <p class="mt-2 text-sm font-semibold text-primary">{{ $caseStudy->product->name }}</p>
                                        @endif
                                    </div>
                                </div>

                                <p class="mt-6 flex-1 leading-relaxed text-muted">{{ $caseStudy->summary }}</p>

                                <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary" aria-hidden="true">
                                    {{ __('Read the case study') }}
                                    <svg class="h-4 w-4 transition-transform duration-300 ease-[var(--ease-brand)] group-hover:translate-x-1"
                                         fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                    </svg>
                                </span>
                            </a>
                        </x-ui.reveal>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @include('sections.cta', ['settings' => $settings])
@endsection

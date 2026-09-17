@extends('layouts.app')

@php
    $seoTitle = __('Kickstarter gallery') . ' | I-NNOVA';
    $seoDescription = __('Photographs and video from the I-NNOVA Kickstarter programme: internships running, projects being built, and the people doing it.');
@endphp

@section('content')
    <x-ui.page-header
        eyebrow="{{ __('Gallery') }}"
        title="{{ __('The programme in pictures and video') }}"
        lead="{{ __('Internships running, projects being built, and the people doing it.') }}"
        motif="network"
        :back="route('kickstarter')"
        backLabel="{{ __('Back to Kickstarter') }}"
    />

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            @if ($counts['all'] > 0)
                <div class="mb-12 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                    <x-ui.gallery-filter :type="$type" :counts="$counts" />

                    <p class="text-sm text-muted">{{ __('Newest first') }}</p>
                </div>
            @endif

            @if ($images->isEmpty())
                {{-- Reachable from the menu only when there is something here,
                     but the address can still be typed or shared, and a filter
                     can be followed to a page that has emptied since. --}}
                <p class="mx-auto max-w-lg text-center text-lead text-muted">
                    @if ($counts['all'] > 0)
                        {{ __('Nothing here under that filter yet.') }}
                    @else
                        {{ __('There is nothing in the gallery yet. Come back once the next cohort is under way.') }}
                    @endif
                </p>
            @else
                <x-ui.gallery-grid :images="$images" />

                <x-ui.pagination :paginator="$images" label="{{ __('Gallery pages') }}" />
            @endif
        </div>
    </section>

    <section class="bg-paper pb-(--spacing-band-lg)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="scale"
                         class="relative overflow-hidden rounded-3xl border border-content/10 bg-ink px-8 py-14 text-center text-white sm:px-16">
                <x-tech.waveform class="pointer-events-none absolute inset-x-0 bottom-0 h-32 w-full text-white/[0.14]" />

                <div class="relative mx-auto max-w-2xl">
                    <h2 class="font-display text-h2">{{ __('Want to be in the next one?') }}</h2>

                    <div class="rule-draw mx-auto mt-6 h-0.5 w-16 bg-accent" aria-hidden="true"></div>

                    <p class="text-lead mt-6 text-white/70">
                        {{ __('The accelerator runs in cohorts. Applications and the tracks are on the Kickstarter platform.') }}
                    </p>

                    <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                        @if ($settings->kickstarter_url)
                            <x-ui.button :href="$settings->kickstarter_url" size="lg">{{ __('Apply now') }}</x-ui.button>
                        @endif

                        <x-ui.button :href="route('kickstarter')" variant="ghost-light" size="lg">
                            {{ __('About the programme') }}
                        </x-ui.button>
                    </div>
                </div>
            </x-ui.reveal>
        </div>
    </section>
@endsection

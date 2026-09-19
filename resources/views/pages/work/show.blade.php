@extends('layouts.app')

@php
    $seoTitle = $client->name . ' | ' . __('Work') . ' | I-NNOVA';
    $seoDescription = $client->summary ?? '';
    $seoImage = $client->getFirstMediaUrl('cover') ?: null;
    $seoType = 'article';
@endphp

@section('content')
    <x-ui.page-header
        :eyebrow="$client->sector?->name"
        :title="$client->name"
        :lead="$client->summary"
        :back="route('work.index')"
        back-label="{{ __('All work') }}"
        motif="network"
    >
        @php $logo = $client->getFirstMediaUrl('logo', 'thumb') ?: $client->getFirstMediaUrl('logo'); @endphp

        @if ($logo || $client->products->isNotEmpty())
            <x-ui.reveal :delay="260" class="mt-10 flex flex-wrap items-center gap-8">
                @if ($logo)
                    <img src="{{ $logo }}" alt="{{ $client->name }}" width="96" height="96"
                         class="h-24 w-24 object-contain">
                @endif

                @foreach ($client->products as $clientProduct)
                    <div>
                        {{-- Singular or plural, because an institution can run
                             one of ours or several. --}}
                        <p class="text-eyebrow font-semibold uppercase text-muted">
                            {{ $loop->first ? ($client->products->count() > 1 ? 'Products deployed' : 'Product deployed') : '' }}
                        </p>
                        <a href="{{ route('products.show', $clientProduct) }}"
                           class="link-underline mt-2 inline-block font-display text-h3 text-primary">
                            {{ $clientProduct->name }}
                        </a>
                    </div>
                @endforeach
            </x-ui.reveal>
        @endif
    </x-ui.page-header>

    @php $coverMedia = $client->getFirstMedia('cover'); @endphp

    @if ($coverMedia)
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="scale" class="-mt-10 overflow-hidden rounded-2xl border border-content/10 shadow-xl shadow-shade/5">
                <img src="{{ $coverMedia->getUrl('wide') ?: $coverMedia->getUrl() }}"
                     srcset="{{ $coverMedia->getSrcset('wide') }}"
                     sizes="(min-width: 72rem) 72rem, 100vw"
                     alt="{{ $client->name }}" width="1600" height="900"
                     fetchpriority="high" decoding="async" class="w-full object-cover">
            </x-ui.reveal>
        </div>
    @endif

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl space-y-16 px-4 sm:px-6">
            @foreach ([
                ['Challenge', $client->challenge],
                ['Solution', $client->solution],
                ['Results', $client->results],
            ] as $index => [$heading, $body])
                @if (filled($body))
                    <x-ui.reveal :delay="$index * 80" class="grid gap-8 lg:grid-cols-[14rem_1fr]">
                        <div>
                            <span class="font-display text-sm text-accent-text">0{{ $index + 1 }}</span>
                            <h2 class="mt-2 font-display text-h3 text-content">{{ $heading }}</h2>
                            <div class="rule-draw mt-4 h-0.5 w-10 bg-accent" aria-hidden="true"></div>
                        </div>

                        <x-ui.prose>{!! $body !!}</x-ui.prose>
                    </x-ui.reveal>
                @endif
            @endforeach
        </div>
    </section>

    @if (filled($client->quote))
        <section class="bg-ink py-(--spacing-band) text-white">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
                <x-ui.reveal from="scale">
                    <blockquote class="font-display text-h2">{{ $client->quote }}</blockquote>

                    @if ($client->quote_attribution)
                        <figcaption class="mt-8 text-white/60">
                            {{ collect([$client->quote_attribution, $client->quote_role, $client->name])->filter()->implode(', ') }}
                        </figcaption>
                    @endif
                </x-ui.reveal>
            </div>
        </section>
    @endif

    @php $gallery = $client->getMedia('images'); @endphp

    @if ($gallery->isNotEmpty())
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto grid max-w-6xl gap-5 px-4 sm:grid-cols-2 sm:px-6">
                @foreach ($gallery as $index => $image)
                    <x-ui.reveal :delay="$index * 90" from="scale" class="overflow-hidden rounded-xl border border-content/10">
                        <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}"
                             alt="{{ $client->name }}" width="600" height="400" loading="lazy" decoding="async" class="w-full object-cover">
                    </x-ui.reveal>
                @endforeach
            </div>
        </section>
    @endif

    @if ($more->isNotEmpty())
        <section class="border-t border-content/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header eyebrow="{{ __('More work') }}" title="{{ __('Other deployments') }}" />

                <div class="mt-14 grid gap-6 sm:grid-cols-2">
                    @foreach ($more as $index => $other)
                        <x-ui.reveal :delay="$index * 110" class="h-full">
                            <x-ui.card
                                :href="route('work.show', $other)"
                                :eyebrow="$other->sector?->name"
                                :title="$other->institution"
                                :body="$other->summary"
                            />
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.cta', ['settings' => $settings])
@endsection

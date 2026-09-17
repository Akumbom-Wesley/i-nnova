@extends('layouts.app')

@php
    $seoTitle = $product->name . ' | I-NNOVA';
    $seoDescription = $product->tagline ?? '';
    $seoImage = $product->getFirstMediaUrl('cover') ?: null;
    $seoType = 'product';
@endphp

@push('schema')
    <x-seo.product :product="$product" />
@endpush

@section('content')
    <x-ui.page-header
        :eyebrow="$product->sector?->name"
        :title="$product->name"
        :lead="$product->tagline"
        :back="route('products.index')"
        back-label="{{ __('All products') }}"
        motif="circuit"
    >
        <x-ui.reveal :delay="260" class="mt-8 flex flex-wrap items-center gap-4">
            @if ($product->isLive())
                <x-ui.badge tone="primary">Live</x-ui.badge>

                @if ($product->website_url)
                    <x-ui.button :href="$product->website_url" variant="outline">Visit the product</x-ui.button>
                @endif
            @else
                <x-ui.badge tone="accent">
                    {{ $product->launch_date ? 'Expected ' . $product->launch_date->format('F Y') : 'Coming soon' }}
                </x-ui.badge>
            @endif
        </x-ui.reveal>
    </x-ui.page-header>

    @php $coverMedia = $product->getFirstMedia('cover'); @endphp

    @if ($coverMedia)
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="scale" class="-mt-10 overflow-hidden rounded-2xl border border-content/10 shadow-xl shadow-shade/5">
                <img src="{{ $coverMedia->getUrl('wide') ?: $coverMedia->getUrl() }}"
                     srcset="{{ $coverMedia->getSrcset('wide') }}"
                     sizes="(min-width: 72rem) 72rem, 100vw"
                     alt="{{ $product->name }}" width="1600" height="900"
                     fetchpriority="high" decoding="async" class="w-full object-cover">
            </x-ui.reveal>
        </div>
    @endif

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto grid max-w-6xl gap-16 px-4 sm:px-6 lg:grid-cols-[1.4fr_1fr]">
            <div>
                @if ($product->isLive())
                    <x-ui.reveal>
                        <x-ui.prose>{!! $product->description !!}</x-ui.prose>
                    </x-ui.reveal>
                @else
                    {{--
                        A product that has not shipped gets limited information on
                        purpose. Presenting unlaunched work as though it were in
                        service is exactly what this rebuild is correcting.
                    --}}
                    <x-ui.reveal class="rounded-2xl border border-dashed border-content/20 bg-paper-dim p-8">
                        <h2 class="font-display text-h3 text-content">Still in development</h2>

                        <p class="mt-4 leading-relaxed text-muted">
                            This one is being built. We would rather say little than describe
                            something that is not running yet.
                            @if ($product->launch_date)
                                We expect it in {{ $product->launch_date->format('F Y') }}.
                            @endif
                        </p>

                        <x-ui.button :href="route('contact')" class="mt-8">
                            {{ __('Ask us about it') }}
                        </x-ui.button>
                    </x-ui.reveal>
                @endif

                @php $screenshots = $product->getMedia('screenshots'); @endphp

                @if ($product->isLive() && $screenshots->isNotEmpty())
                    <div class="mt-14 grid gap-5 sm:grid-cols-2">
                        @foreach ($screenshots as $index => $shot)
                            <x-ui.reveal :delay="$index * 90" from="scale"
                                         class="overflow-hidden rounded-xl border border-content/10">
                                <img src="{{ $shot->getUrl('thumb') ?: $shot->getUrl() }}"
                                     alt="{{ $product->name }} screenshot" width="720" height="450" loading="lazy" decoding="async"
                                     class="w-full object-cover">
                            </x-ui.reveal>
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="space-y-10">
                @php $features = $product->features ?? []; @endphp

                @if (is_array($features) && $features !== [])
                    <x-ui.reveal from="right" class="rounded-2xl border border-content/10 bg-paper-dim p-8">
                        <h2 class="text-eyebrow font-semibold uppercase text-muted">What it does</h2>
                        <x-ui.feature-list class="mt-6" :items="$features" />
                    </x-ui.reveal>
                @endif

                @if ($product->caseStudies->isNotEmpty())
                    <x-ui.reveal from="right" :delay="90">
                        <h2 class="text-eyebrow font-semibold uppercase text-muted">In service at</h2>

                        <ul class="mt-6 space-y-3">
                            @foreach ($product->caseStudies as $caseStudy)
                                <li>
                                    <a href="{{ route('work.show', $caseStudy) }}"
                                       class="link-underline font-semibold text-primary">
                                        {{ $caseStudy->institution }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </x-ui.reveal>
                @endif
            </aside>
        </div>
    </section>

    @if ($product->testimonials->isNotEmpty())
        <section class="border-y border-content/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-6 md:grid-cols-2">
                    @foreach ($product->testimonials as $index => $testimonial)
                        <x-ui.reveal :delay="$index * 110" class="h-full">
                            <x-ui.quote
                                :quote="$testimonial->quote"
                                :name="$testimonial->person_name"
                                :role="$testimonial->person_role"
                                :organisation="$testimonial->organisation"
                                :photo="$testimonial->getFirstMediaUrl('photo', 'thumb') ?: null"
                            />
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.cta', ['settings' => $settings])
@endsection

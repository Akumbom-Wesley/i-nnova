@extends('layouts.app')

@section('title', $caseStudy->institution . ' | Work | I-NNOVA')
@section('description', $caseStudy->summary ?? '')

@section('content')
    <x-ui.page-header
        :eyebrow="$caseStudy->sector?->name"
        :title="$caseStudy->institution"
        :lead="$caseStudy->summary"
        :back="route('work.index')"
        back-label="All work"
        motif="network"
    >
        @php $logo = $caseStudy->getFirstMediaUrl('logo', 'thumb') ?: $caseStudy->getFirstMediaUrl('logo'); @endphp

        @if ($logo || $caseStudy->product)
            <x-ui.reveal :delay="260" class="mt-10 flex flex-wrap items-center gap-8">
                @if ($logo)
                    <img src="{{ $logo }}" alt="{{ $caseStudy->institution }}" width="96" height="96"
                         class="h-24 w-24 object-contain">
                @endif

                @if ($caseStudy->product)
                    <div>
                        <p class="text-eyebrow font-semibold uppercase text-muted">Product deployed</p>
                        <a href="{{ route('products.show', $caseStudy->product) }}"
                           class="link-underline mt-2 inline-block font-display text-h3 text-primary">
                            {{ $caseStudy->product->name }}
                        </a>
                    </div>
                @endif
            </x-ui.reveal>
        @endif
    </x-ui.page-header>

    @if ($cover = $caseStudy->getFirstMediaUrl('cover'))
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="scale" class="-mt-10 overflow-hidden rounded-2xl border border-ink/10 shadow-xl shadow-ink/5">
                <img src="{{ $cover }}" alt="{{ $caseStudy->institution }}" class="w-full object-cover">
            </x-ui.reveal>
        </div>
    @endif

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl space-y-16 px-4 sm:px-6">
            @foreach ([
                ['Challenge', $caseStudy->challenge],
                ['Solution', $caseStudy->solution],
                ['Results', $caseStudy->results],
            ] as $index => [$heading, $body])
                @if (filled($body))
                    <x-ui.reveal :delay="$index * 80" class="grid gap-8 lg:grid-cols-[14rem_1fr]">
                        <div>
                            <span class="font-display text-sm text-accent-text">0{{ $index + 1 }}</span>
                            <h2 class="mt-2 font-display text-h3 text-ink">{{ $heading }}</h2>
                            <div class="rule-draw mt-4 h-0.5 w-10 bg-accent" aria-hidden="true"></div>
                        </div>

                        <x-ui.prose>{!! $body !!}</x-ui.prose>
                    </x-ui.reveal>
                @endif
            @endforeach
        </div>
    </section>

    @if (filled($caseStudy->quote))
        <section class="bg-ink py-(--spacing-band) text-white">
            <div class="mx-auto max-w-4xl px-4 text-center sm:px-6">
                <x-ui.reveal from="scale">
                    <blockquote class="font-display text-h2">{{ $caseStudy->quote }}</blockquote>

                    @if ($caseStudy->quote_attribution)
                        <figcaption class="mt-8 text-white/60">
                            {{ collect([$caseStudy->quote_attribution, $caseStudy->quote_role, $caseStudy->institution])->filter()->implode(', ') }}
                        </figcaption>
                    @endif
                </x-ui.reveal>
            </div>
        </section>
    @endif

    @php $gallery = $caseStudy->getMedia('images'); @endphp

    @if ($gallery->isNotEmpty())
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto grid max-w-6xl gap-5 px-4 sm:grid-cols-2 sm:px-6">
                @foreach ($gallery as $index => $image)
                    <x-ui.reveal :delay="$index * 90" from="scale" class="overflow-hidden rounded-xl border border-ink/10">
                        <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}"
                             alt="{{ $caseStudy->institution }}" loading="lazy" class="w-full object-cover">
                    </x-ui.reveal>
                @endforeach
            </div>
        </section>
    @endif

    @if ($more->isNotEmpty())
        <section class="border-t border-ink/10 bg-paper-dim py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header eyebrow="More work" title="Other deployments" />

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

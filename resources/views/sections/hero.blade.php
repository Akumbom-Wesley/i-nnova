@props(['settings', 'stats', 'slides'])

@php
    // A stored internal path such as /work has no locale in it, so it is
    // prefixed here. An absolute URL is left alone: it points off site.
    $heroCta = match (true) {
        blank($settings->hero_cta_url) => route('work.index'),
        str_starts_with($settings->hero_cta_url, '/') => url(app()->getLocale() . $settings->hero_cta_url),
        default => $settings->hero_cta_url,
    };

    $hasSlides = $slides->isNotEmpty();
@endphp

<section
    class="relative overflow-hidden {{ $hasSlides ? 'bg-ink text-white' : 'bg-paper' }}"
    @if ($hasSlides) x-data="heroSlider({{ $slides->count() }})" @mouseenter="stop()" @mouseleave="start()" @endif
>
    @if ($hasSlides)
        {{--
            Decorative: the headline carries the meaning, so the slides are
            hidden from assistive tech and only the controls are exposed.
        --}}
        <div class="absolute inset-0" aria-hidden="true">
            @foreach ($slides as $index => $slide)
                <div class="hero-slide absolute inset-0 overflow-hidden"
                     x-bind:data-active="active === {{ $index }} ? '' : null"
                     @if ($index === 0) data-active @endif>
                    <x-ui.photo
                        :image="$slide"
                        conversion="original"
                        :width="1920"
                        :height="1080"
                        sizes="100vw"
                        :eager="$index === 0"
                        class="h-full w-full object-cover"
                    />
                </div>
            @endforeach

            {{-- The scrim. Guarantees the headline stays legible whatever the
                 photograph underneath happens to be. --}}
            <div class="hero-scrim absolute inset-0"></div>
        </div>
    @else
        {{-- With no photography loaded the hero falls back to the calm white
             treatment rather than a black rectangle. --}}
        <div class="pointer-events-none absolute inset-x-0 -top-40 h-[32rem] bg-gradient-to-b from-primary-soft to-transparent"
             aria-hidden="true"></div>

        <x-tech.circuit class="pointer-events-none absolute -right-16 top-10 hidden w-[34rem] text-primary/[0.13] lg:block" />
    @endif

    <div class="relative mx-auto max-w-6xl px-4 pt-(--spacing-band) pb-(--spacing-band-lg) sm:px-6">
        <div class="max-w-3xl">
            <x-ui.reveal from="none">
                <p class="text-eyebrow font-semibold uppercase {{ $hasSlides ? 'text-accent' : 'text-accent-text' }}">
                    {{ __('Bamenda, Cameroon') }}
                </p>
            </x-ui.reveal>

            <x-ui.reveal :delay="80">
                <h1 class="mt-6 font-display text-display {{ $hasSlides ? 'text-white' : 'text-content' }}">
                    {{ $settings->hero_heading ?: __('Practical software for institutions that cannot afford downtime.') }}
                </h1>
            </x-ui.reveal>

            <x-ui.reveal :delay="160" class="mt-8 flex items-center gap-4">
                <span class="h-px w-10 bg-accent" aria-hidden="true"></span>
                <p class="font-display text-h3 {{ $hasSlides ? 'text-white' : 'text-primary' }}">
                    {!! __('Driven by :stem to solve real world problems', ['stem' => '<span class="text-accent">STEM</span>']) !!}
                </p>
            </x-ui.reveal>

            <x-ui.reveal :delay="240">
                <p class="text-lead mt-6 {{ $hasSlides ? 'text-white/80' : 'text-muted' }}">
                    {{ $settings->hero_subheading }}
                </p>
            </x-ui.reveal>

            <x-ui.reveal :delay="320" class="mt-10 flex flex-wrap items-center gap-4">
                <x-ui.button :href="$heroCta" size="lg">
                    {{ $settings->hero_cta_label ?: __('See our work') }}
                </x-ui.button>

                <x-ui.button :href="route('products.index')"
                             :variant="$hasSlides ? 'ghost-light' : 'outline'" size="lg">
                    {{ __('Explore products') }}
                </x-ui.button>
            </x-ui.reveal>
        </div>

        @if ($hasSlides && $slides->count() > 1)
            <div class="mt-12 flex items-center gap-3" role="group" aria-label="{{ __('Choose a photograph') }}">
                @foreach ($slides as $index => $slide)
                    <button type="button"
                            x-on:click="goTo({{ $index }})"
                            x-on:focus="stop()"
                            x-on:blur="start()"
                            x-bind:aria-current="active === {{ $index }} ? 'true' : 'false'"
                            x-bind:class="active === {{ $index }} ? 'w-10 bg-accent' : 'w-4 bg-white/40 hover:bg-white/70'"
                            class="hero-dot h-1.5 rounded-full">
                        <span class="sr-only">{{ $slide->altText() ?: __('Photograph :number', ['number' => $index + 1]) }}</span>
                    </button>
                @endforeach
            </div>
        @endif

        @if ($stats->isNotEmpty())
            <div class="mt-(--spacing-band) grid grid-cols-1 gap-10 border-t pt-12 sm:grid-cols-3 {{ $hasSlides ? 'border-white/20' : 'border-content/10' }}">
                @foreach ($stats as $index => $stat)
                    <x-ui.reveal :delay="$index * 120">
                        <x-ui.stat :value="$stat->displayValue()" :label="$stat->label" :caption="$stat->caption"
                                   :tone="$hasSlides ? 'dark' : 'light'" />
                    </x-ui.reveal>
                @endforeach
            </div>
        @endif
    </div>
</section>

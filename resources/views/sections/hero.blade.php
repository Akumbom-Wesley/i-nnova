@props(['settings', 'stats'])

@php
    // A stored internal path such as /work has no locale in it, so it is
    // prefixed here. An absolute URL is left alone: it points off site.
    $heroCta = match (true) {
        blank($settings->hero_cta_url) => route('work.index'),
        str_starts_with($settings->hero_cta_url, '/') => url(app()->getLocale() . $settings->hero_cta_url),
        default => $settings->hero_cta_url,
    };
@endphp

<section class="relative overflow-hidden bg-paper">
    {{-- A soft blue wash behind the hero, so white does not read as empty. --}}
    <div class="pointer-events-none absolute inset-x-0 -top-40 h-[32rem] bg-gradient-to-b from-primary-soft to-transparent"
         aria-hidden="true"></div>

    {{-- The STEM motif, low contrast, as the ground the hero sits on. --}}
    <x-tech.circuit class="pointer-events-none absolute -right-16 top-10 hidden w-[34rem] text-primary/[0.13] lg:block" />

    <div class="relative mx-auto max-w-6xl px-4 pt-(--spacing-band) pb-(--spacing-band-lg) sm:px-6">
        <x-ui.reveal from="none">
            <p class="text-eyebrow font-semibold uppercase text-accent-text">Bamenda, Cameroon</p>
        </x-ui.reveal>

        <x-ui.reveal :delay="80">
            <h1 class="mt-6 max-w-4xl font-display text-display text-ink">
                {{ $settings->hero_heading ?: 'Practical software for institutions that cannot afford downtime.' }}
            </h1>
        </x-ui.reveal>

        {{-- STEM is the engine, named directly under the headline. --}}
        <x-ui.reveal :delay="160" class="mt-8 flex items-center gap-4">
            <span class="h-px w-10 bg-accent" aria-hidden="true"></span>
            <p class="font-display text-h3 text-primary">
                {!! __('Driven by :stem to solve real world problems', ['stem' => '<span class="text-accent-text">STEM</span>']) !!}
            </p>
        </x-ui.reveal>

        <x-ui.reveal :delay="240">
            <p class="text-lead mt-6 max-w-2xl text-muted">
                {{ $settings->hero_subheading }}
            </p>
        </x-ui.reveal>

        <x-ui.reveal :delay="320" class="mt-10 flex flex-wrap items-center gap-4">
            <x-ui.button :href="$heroCta" size="lg">
                {{ $settings->hero_cta_label ?: 'See our work' }}
            </x-ui.button>

            <x-ui.button :href="route('products.index')" variant="outline" size="lg">
                {{ __('Explore products') }}
            </x-ui.button>
        </x-ui.reveal>

        @if ($stats->isNotEmpty())
            <div class="mt-(--spacing-band) grid grid-cols-1 gap-10 border-t border-ink/10 pt-12 sm:grid-cols-3">
                @foreach ($stats as $index => $stat)
                    <x-ui.reveal :delay="$index * 120">
                        <x-ui.stat :value="$stat->value" :label="$stat->label" :caption="$stat->caption" />
                    </x-ui.reveal>
                @endforeach
            </div>
        @endif
    </div>
</section>

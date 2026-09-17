@props(['settings'])

@if ($settings->hasMap())
    <section class="section-seam bg-paper py-(--spacing-band)">
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.seam />

            <div class="grid gap-12 lg:grid-cols-[1fr_1.35fr] lg:items-center">
                <div>
                    <x-ui.section-header
                        eyebrow="{{ __('Find us') }}"
                        title="{{ __('Where we work from') }}"
                    />

                    @if ($settings->address)
                        <x-ui.reveal :delay="200" class="mt-8">
                            <p class="whitespace-pre-line leading-relaxed text-muted">{{ $settings->address }}</p>
                        </x-ui.reveal>
                    @endif

                    <x-ui.reveal :delay="260" class="mt-8 flex flex-wrap gap-4">
                        <x-ui.button :href="$settings->mapDirectionsUrl()" variant="outline">
                            {{ __('Open in maps') }}
                        </x-ui.button>

                        @if ($settings->contact_phone)
                            <x-ui.button :href="'tel:' . preg_replace('/\s+/', '', $settings->contact_phone)" variant="outline">
                                {{ $settings->contact_phone }}
                            </x-ui.button>
                        @endif
                    </x-ui.reveal>
                </div>

                <x-ui.reveal from="right" :delay="120"
                             class="overflow-hidden rounded-2xl border border-hairline shadow-xl shadow-shade/5">
                    {{--
                        OpenStreetMap: no key to manage and no consent banner to
                        add, and lazy so nothing third party loads until the
                        reader has actually scrolled this far.
                    --}}
                    <iframe
                        src="{{ $settings->mapEmbedUrl() }}"
                        title="{{ __('Map showing where I-NNOVA is based') }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="aspect-[4/3] w-full border-0 lg:aspect-[16/10]"
                    ></iframe>
                </x-ui.reveal>
            </div>
        </div>
    </section>
@endif

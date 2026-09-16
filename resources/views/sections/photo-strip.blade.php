@props(['images'])

{{--
    The home page photo band. Runs edge to edge and scrolls sideways on a
    phone, so a row of photographs never squashes into stamps.
--}}
@if ($images->isNotEmpty())
    <section class="band-sheen overflow-hidden bg-ink py-(--spacing-band)" aria-labelledby="life-here">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                eyebrow="{{ __('Inside I-NNOVA') }}"
                title="{{ __('The work, the place and the people') }}"
                lead="{{ __('Engineers building, cohorts learning, and the building in Bamenda where both happen.') }}"
                tone="dark"
            />
        </div>

        <div class="mt-14 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 sm:px-6 lg:justify-center lg:overflow-visible">
            @foreach ($images as $index => $image)
                <figure data-reveal="scale" style="--reveal-delay: {{ $index * 90 }}ms"
                        class="card-lift group relative w-[20rem] shrink-0 snap-start overflow-hidden rounded-2xl sm:w-[26rem] lg:w-[30rem]">
                    <div class="aspect-[4/3] overflow-hidden bg-ink-soft">
                        <x-ui.photo
                            :image="$image"
                            :width="960"
                            :height="720"
                            sizes="(min-width: 64rem) 30rem, (min-width: 40rem) 26rem, 20rem"
                            class="transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.05]"
                        />
                    </div>

                    @if ($image->caption)
                        <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-ink to-transparent p-5 pt-14">
                            <span class="text-sm font-medium text-white">{{ $image->caption }}</span>
                        </figcaption>
                    @endif
                </figure>
            @endforeach
        </div>
    </section>
@endif

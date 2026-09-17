@props(['milestones'])

@if ($milestones->isNotEmpty())
    <section class="section-seam bg-paper py-(--spacing-band)">
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.seam />

            <x-ui.section-header
                eyebrow="{{ __('How we got here') }}"
                title="{{ __('From one idea in Bamenda') }}"
            />

            <ol class="relative mt-16">
                {{-- The spine. It runs from blue at the founding down to orange
                     at the present, so the eye has somewhere to travel. Hidden
                     on a phone, where the entries stack anyway. --}}
                <span class="absolute left-[7.5rem] top-3 hidden h-[calc(100%-1.5rem)] w-px bg-gradient-to-b from-primary/40 via-primary/20 to-accent/50 lg:block"
                      aria-hidden="true"></span>

                @foreach ($milestones as $index => $milestone)
                    @php $isLast = $loop->last; @endphp

                    <x-ui.reveal :as="'li'" from="left" :delay="$index * 110"
                                 class="group relative grid gap-4 pb-12 last:pb-0 lg:grid-cols-[7.5rem_1fr] lg:gap-10">
                        <div class="lg:text-right">
                            {{-- The year as a chip: the most recent one filled,
                                 the earlier ones outlined. accent-dark rather than
                                 accent, because white on accent is 3.50:1 and this
                                 is 18px semibold, under the size WCAG counts as
                                 large text. accent-dark measures 4.61:1. --}}
                            <span @class([
                                'inline-flex items-center rounded-full px-3 py-1 font-display text-lg transition-colors duration-300 ease-[var(--ease-brand)]',
                                'bg-accent-dark text-white' => $isLast,
                                'bg-primary-soft text-primary group-hover:bg-primary group-hover:text-white' => ! $isLast,
                            ])>
                                {{ $milestone->year }}
                            </span>
                        </div>

                        <div class="relative lg:pl-10">
                            <span @class([
                                'absolute -left-1.5 top-3 hidden h-3 w-3 rounded-full ring-4 ring-paper transition-transform duration-300 ease-[var(--ease-brand)] group-hover:scale-125 lg:block',
                                'bg-accent' => $isLast,
                                'bg-primary' => ! $isLast,
                            ]) aria-hidden="true"></span>

                            <h3 class="font-display text-h3 text-ink">{{ $milestone->title }}</h3>

                            @if ($milestone->body)
                                <p class="mt-3 max-w-2xl leading-relaxed text-muted">{{ $milestone->body }}</p>
                            @endif
                        </div>
                    </x-ui.reveal>
                @endforeach
            </ol>
        </div>
    </section>
@endif

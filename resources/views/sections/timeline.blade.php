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
                {{-- The spine runs from blue at the founding to orange at the
                     present, so the eye has somewhere to travel. It sits at the
                     left edge on a phone and beside the year column on a wide
                     screen, rather than disappearing on small screens. --}}
                <span class="absolute left-[0.3125rem] top-3 h-[calc(100%-1.5rem)] w-px bg-gradient-to-b from-primary/40 via-primary/20 to-accent/50 lg:left-[7.5rem]"
                      aria-hidden="true"></span>

                @foreach ($milestones as $index => $milestone)
                    @php $isLast = $loop->last; @endphp

                    <x-ui.reveal :as="'li'" from="left" :delay="$index * 110"
                                 class="group relative grid gap-2 pb-12 pl-9 last:pb-0 lg:grid-cols-[7.5rem_1fr] lg:gap-10 lg:pl-0">
                        {{-- The dot. Anchored to the list on a phone and to the
                             entry on a wide screen, so it meets the spine in
                             both places. --}}
                        <span @class([
                            'absolute left-0 top-3 h-2.5 w-2.5 rounded-full ring-4 ring-paper transition-transform duration-300 ease-[var(--ease-brand)] group-hover:scale-125 lg:hidden',
                            'bg-accent' => $isLast,
                            'bg-primary' => ! $isLast,
                        ]) aria-hidden="true"></span>

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

                            <h3 class="mt-2 font-display text-h3 text-content lg:mt-0">{{ $milestone->title }}</h3>

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

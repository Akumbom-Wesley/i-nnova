@props(['milestones'])

@if ($milestones->isNotEmpty())
    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                eyebrow="{{ __('How we got here') }}"
                title="{{ __('From one idea in Bamenda') }}"
            />

            <ol class="relative mt-16">
                {{-- The spine. Drawn behind the entries, hidden on a phone
                     where the entries stack anyway. --}}
                <span class="absolute left-[7.5rem] top-2 hidden h-full w-px bg-ink/10 lg:block" aria-hidden="true"></span>

                @foreach ($milestones as $index => $milestone)
                    <x-ui.reveal :as="'li'" from="left" :delay="$index * 110"
                                 class="relative grid gap-4 pb-12 last:pb-0 lg:grid-cols-[7.5rem_1fr] lg:gap-10">
                        <div class="lg:text-right">
                            <span class="font-display text-h3 text-primary">{{ $milestone->year }}</span>
                        </div>

                        <div class="relative lg:pl-10">
                            <span class="absolute -left-1.5 top-2 hidden h-3 w-3 rounded-full bg-accent ring-4 ring-paper lg:block"
                                  aria-hidden="true"></span>

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

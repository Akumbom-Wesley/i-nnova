@props(['values'])

@if ($values->isNotEmpty())
    <section class="border-y border-content/10 bg-paper-dim py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                eyebrow="{{ __('What we stand for') }}"
                title="{{ __('Smart solutions that solve real world problems') }}"
                lead="{{ __('Four things the work is held to.') }}"
            />

            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($values as $index => $value)
                    <x-ui.reveal :delay="$index * 90"
                                 class="card-lift group h-full rounded-2xl border border-content/10 bg-paper p-7 hover:border-accent/40 hover:shadow-lg hover:shadow-shade/5">
                        <span class="font-display text-sm text-accent-text">0{{ $index + 1 }}</span>

                        <h3 class="mt-3 font-display text-h3 text-content">{{ $value->title }}</h3>

                        <div class="rule-draw mt-4 h-0.5 w-10 bg-accent" aria-hidden="true"></div>

                        <div class="mt-4 leading-relaxed text-muted [&_p]:m-0">
                            {!! $value->body !!}
                        </div>
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

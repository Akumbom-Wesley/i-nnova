@props(['clients'])

@if ($clients->isNotEmpty())
    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                eyebrow="{{ __('Deployments') }}"
                title="{{ __('Running in real institutions') }}"
                lead="{{ __('The products are not demos. They are in service, and the institutions running them are named.') }}"
            />

            <div class="mt-16 grid gap-6 md:grid-cols-2">
                @foreach ($clients as $index => $client)
                    @php
                        // Institution crests are supplied as large source artwork, so
                        // always prefer the conversion. The SAHIK crest is 1.4 MB at
                        // full size and renders here at 80px.
                        $logo = $client->getFirstMediaUrl('logo', 'thumb')
                            ?: $client->getFirstMediaUrl('logo');
                    @endphp

                    <x-ui.reveal :delay="$index * 120" class="h-full">
                        <a href="{{ route('work.show', $client) }}"
                           class="card-lift group flex h-full flex-col rounded-2xl border border-content/10 bg-paper p-8 hover:border-accent/40 hover:shadow-xl hover:shadow-shade/5">
                            <div class="flex items-start gap-6">
                                @if ($logo)
                                    <img src="{{ $logo }}" alt="" width="80" height="80" loading="lazy"
                                         class="h-20 w-20 shrink-0 object-contain transition-transform duration-500 ease-[var(--ease-brand)] group-hover:scale-105">
                                @endif

                                <div class="min-w-0">
                                    @if ($client->sector)
                                        <span class="text-eyebrow font-semibold uppercase text-muted">
                                            {{ $client->sector->name }}
                                        </span>
                                    @endif

                                    <h3 class="mt-2 font-display text-h3 text-content">{{ $client->name }}</h3>

                                    @if ($client->products->isNotEmpty())
                                        <p class="mt-2 text-sm font-semibold text-primary">
                                            {{ $client->products->pluck('name')->join(', ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <p class="mt-6 flex-1 leading-relaxed text-muted">{{ $client->summary }}</p>

                            <span class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-primary" aria-hidden="true">
                                {{ __('Read the case study') }}
                                <svg class="h-4 w-4 transition-transform duration-300 ease-[var(--ease-brand)] group-hover:translate-x-1"
                                     fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                </svg>
                            </span>
                        </a>
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

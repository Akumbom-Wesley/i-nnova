@props(['team'])

{{--
    The executives, not a sample of everyone. The controller passes the
    first TeamMember::LEADERSHIP_COUNT by the order set in the admin, so
    who appears here is decided by dragging rows rather than by a separate
    flag. Three across at every width above mobile, in a narrower column,
    so they sit evenly instead of leaving a gap.
--}}

@if ($team->isNotEmpty())
    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <div class="flex flex-wrap items-end justify-between gap-8">
                <x-ui.section-header
                    eyebrow="{{ __('Leadership') }}"
                    title="{{ __('The people who run it') }}"
                />

                <x-ui.reveal from="right">
                    <x-ui.button :href="route('about') . '#team'" variant="outline">
                        {{ __('Meet the full team') }}
                    </x-ui.button>
                </x-ui.reveal>
            </div>

            <div class="mx-auto mt-16 grid max-w-4xl gap-10 sm:grid-cols-3">
                @foreach ($team as $index => $member)
                    <x-ui.reveal :delay="$index * 90" from="scale" class="group text-center">
                        <div class="relative mx-auto aspect-square w-40 overflow-hidden rounded-full bg-paper-dim sm:w-44">
                            @if ($photo = $member->getFirstMediaUrl('photo', 'thumb'))
                                <img src="{{ $photo }}" alt="{{ $member->name }}" width="160" height="160" loading="lazy" decoding="async"
                                     class="h-full w-full object-cover transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-105">
                            @endif

                            {{-- An orange ring that closes on hover. --}}
                            <span class="pointer-events-none absolute inset-0 rounded-full ring-0 ring-accent transition-all duration-300 ease-[var(--ease-brand)] group-hover:ring-4"
                                  aria-hidden="true"></span>
                        </div>

                        <h3 class="mt-6 font-display text-h3 text-content">{{ $member->name }}</h3>
                        <p class="mt-1 text-sm text-muted">{{ $member->role }}</p>
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

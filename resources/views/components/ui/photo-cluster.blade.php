@props(['images'])

{{--
    Three photographs as an inverted triangle: two across the top, one
    centred beneath. Each is offset and drifts on its own clock, so the
    group reads as a composition rather than a grid.

    Decorative beside a heading that already says what the page is, so the
    photographs keep their alt text but the arrangement adds nothing to
    announce.
--}}
@php $cluster = $images->take(3); @endphp

@if ($cluster->isNotEmpty())
    <div {{ $attributes->class('grid grid-cols-2 gap-4 sm:gap-5') }}>
        @foreach ($cluster as $index => $image)
            @php
                // Top-left sits high, top-right drops, the third centres below.
                $offset = match ($index) {
                    0 => '-translate-y-2 lg:-translate-y-6',
                    1 => 'translate-y-4 lg:translate-y-8',
                    default => 'col-span-2 mx-auto w-3/4 -translate-y-1 lg:-translate-y-2',
                };

                $ratio = $index === 2 ? 'aspect-[16/10]' : 'aspect-[4/5]';
            @endphp

            <div class="{{ $offset }}">
                <div class="float-drift" style="--float-delay: {{ $index * 1.4 }}s">
                    <div class="card-lift group overflow-hidden rounded-2xl border border-white/15 shadow-2xl shadow-ink/40 {{ $ratio }}">
                        <x-ui.photo
                            :image="$image"
                            :width="$index === 2 ? 640 : 480"
                            :height="$index === 2 ? 400 : 600"
                            sizes="(min-width: 64rem) 18rem, 40vw"
                            class="transition-transform duration-700 ease-[var(--ease-brand)] group-hover:scale-[1.05]"
                        />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

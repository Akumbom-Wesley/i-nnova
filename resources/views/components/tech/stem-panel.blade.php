@props(['class' => ''])

@php
    // The word itself, one letter per tile, each with the discipline it stands
    // for and the thing that discipline actually buys a customer. The motif in
    // each tile is drawn from the tech vocabulary rather than the laboratory.
    $tiles = [
        ['letter' => 'S', 'word' => __('Science'), 'line' => __('Test, measure, learn'), 'accent' => false],
        ['letter' => 'T', 'word' => __('Technology'), 'line' => __('Build what runs'), 'accent' => true],
        ['letter' => 'E', 'word' => __('Engineering'), 'line' => __('Make it hold'), 'accent' => false],
        ['letter' => 'M', 'word' => __('Mathematics'), 'line' => __('Prove it works'), 'accent' => true],
    ];
@endphp

{{--
    The About header panel. Decorative as a composition, but the words in it
    are real content, so it is not hidden from assistive tech: it reads as a
    list of the four disciplines.
--}}
<div class="{{ $class }}">
    <ul class="grid grid-cols-2 gap-4 sm:gap-5">
        @foreach ($tiles as $index => $tile)
            <li @class([
                'float-drift' => true,
                '-translate-y-2 lg:-translate-y-5' => $index === 0,
                'translate-y-3 lg:translate-y-6' => $index === 1,
                '-translate-y-1 lg:-translate-y-2' => $index === 2,
                'translate-y-4 lg:translate-y-8' => $index === 3,
            ]) style="--float-delay: {{ $index * 1.1 }}s">
                <div @class([
                    'card-lift group relative h-full overflow-hidden rounded-2xl border p-6 backdrop-blur-sm',
                    'border-accent/40 bg-accent/10 hover:border-accent/70' => $tile['accent'],
                    'border-white/15 bg-white/5 hover:border-white/35' => ! $tile['accent'],
                ])>
                    {{-- A different tech motif behind each letter. --}}
                    <span class="pointer-events-none absolute -right-4 -top-4 opacity-40" aria-hidden="true">
                        @if ($index === 0)
                            <svg viewBox="0 0 80 80" class="h-20 w-20" fill="none">
                                <g fill="currentColor">
                                    <circle cx="20" cy="26" r="3.5" class="circuit-node"/>
                                    <circle cx="46" cy="18" r="3.5" class="circuit-node" style="--node-delay: .6s"/>
                                    <circle cx="60" cy="42" r="3.5" class="circuit-node" style="--node-delay: 1.2s"/>
                                    <circle cx="32" cy="50" r="3.5" class="circuit-node" style="--node-delay: 1.8s"/>
                                </g>
                                <g stroke="currentColor" stroke-width="1.5" opacity="0.5">
                                    <path d="M20 26L46 18L60 42L32 50Z"/>
                                </g>
                            </svg>
                        @elseif ($index === 1)
                            <svg viewBox="0 0 80 80" class="h-20 w-20" fill="none">
                                <g stroke="currentColor" stroke-width="2" stroke-linecap="square">
                                    <path class="circuit-trace" style="--trace-length: 90" d="M8 22h26l12-12h26"/>
                                    <path class="circuit-trace" style="--trace-length: 90; --reveal-delay: 160ms" d="M8 46h18l14 14h32"/>
                                </g>
                                <g stroke="currentColor" stroke-width="3" stroke-linecap="round">
                                    <path class="circuit-packet" style="--path-length: 90; --packet-duration: 4s" d="M8 22h26l12-12h26"/>
                                </g>
                            </svg>
                        @elseif ($index === 2)
                            <svg viewBox="0 0 80 80" class="h-20 w-20" fill="currentColor">
                                <rect x="14" y="30" width="7" height="34" rx="2" class="circuit-bar"/>
                                <rect x="28" y="30" width="7" height="34" rx="2" class="circuit-bar" style="--bar-delay: .3s"/>
                                <rect x="42" y="30" width="7" height="34" rx="2" class="circuit-bar" style="--bar-delay: .6s"/>
                                <rect x="56" y="30" width="7" height="34" rx="2" class="circuit-bar" style="--bar-delay: .9s"/>
                            </svg>
                        @else
                            <svg viewBox="0 0 80 80" class="h-20 w-20" fill="none">
                                <g stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M30 22L18 40l12 18"/>
                                    <path d="M50 22l12 18-12 18"/>
                                </g>
                                <rect x="36" y="52" width="10" height="4" rx="1" fill="currentColor" class="circuit-cursor"/>
                            </svg>
                        @endif
                    </span>

                    <span class="relative block font-display text-5xl leading-none {{ $tile['accent'] ? 'text-accent' : 'text-white' }}">
                        {{ $tile['letter'] }}
                    </span>

                    <span class="relative mt-4 block text-eyebrow font-semibold uppercase text-white/50">
                        {{ $tile['word'] }}
                    </span>

                    <span class="relative mt-2 block text-sm font-medium text-white/85">
                        {{ $tile['line'] }}
                    </span>
                </div>
            </li>
        @endforeach
    </ul>
</div>

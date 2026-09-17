@props([
    'images',
    'id' => null,
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'tone' => 'light',
    'ctaUrl' => null,
    'ctaLabel' => null,
])

{{--
    A short selection, not the whole set. When there is more than this behind
    it, the band ends with a way through to the full gallery rather than
    growing until the page is nothing but photographs.
--}}
@if ($images->isNotEmpty())
    <section
        @if ($id) id="{{ $id }}" @endif
        @class([
            'scroll-mt-24 py-(--spacing-band)',
            'bg-paper' => $tone === 'light',
            'bg-ink text-white' => $tone === 'dark',
        ])
    >
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                :eyebrow="$eyebrow"
                :title="$title"
                :lead="$lead"
                :tone="$tone"
            />

            <x-ui.gallery :images="$images" class="mt-14" />

            @if ($ctaUrl)
                <x-ui.reveal :delay="160" class="mt-12 flex justify-center">
                    <x-ui.button
                        :href="$ctaUrl"
                        :variant="$tone === 'dark' ? 'ghost-light' : 'outline'"
                        size="lg"
                    >
                        {{ $ctaLabel ?? __('See the full gallery') }}
                    </x-ui.button>
                </x-ui.reveal>
            @endif
        </div>
    </section>
@endif

@props([
    'images',
    'eyebrow' => null,
    'title' => null,
    'lead' => null,
    'tone' => 'light',
])

@if ($images->isNotEmpty())
    <section @class([
        'py-(--spacing-band)',
        'bg-paper' => $tone === 'light',
        'bg-ink text-white' => $tone === 'dark',
    ])>
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                :eyebrow="$eyebrow"
                :title="$title"
                :lead="$lead"
                :tone="$tone"
            />

            <x-ui.gallery :images="$images" class="mt-14" />
        </div>
    </section>
@endif

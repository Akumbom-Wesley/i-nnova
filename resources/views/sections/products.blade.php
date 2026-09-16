@props(['products', 'comingSoon'])

<section class="relative overflow-hidden border-y border-ink/10 bg-paper-dim py-(--spacing-band)">
    <x-tech.grid class="pointer-events-none absolute inset-x-0 top-0 h-60 w-full text-primary/[0.10]" />

    <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
        <x-ui.section-header
            eyebrow="Our solutions"
            title="Software that runs the working day"
            lead="Four products in service across education, health, hospitality and retail, plus custom work where none of them fit."
        />

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($products as $index => $product)
                <x-ui.reveal :delay="$index * 90" class="h-full">
                    <x-ui.card
                        :href="url('/products/' . $product->slug)"
                        :image="$product->getFirstMediaUrl('cover', 'thumb') ?: null"
                        :image-alt="$product->name"
                        :eyebrow="$product->sector?->name"
                        :title="$product->name"
                        :body="$product->tagline"
                        badge="Live"
                        badge-tone="primary"
                    />
                </x-ui.reveal>
            @endforeach
        </div>

        @if ($comingSoon->isNotEmpty())
            <x-ui.reveal class="mt-14 rounded-2xl border border-dashed border-ink/20 bg-paper p-8">
                <p class="text-eyebrow font-semibold uppercase text-muted">In development</p>

                <div class="mt-5 flex flex-wrap gap-x-8 gap-y-4">
                    @foreach ($comingSoon as $product)
                        <div class="flex items-baseline gap-3">
                            <span class="font-display text-h3 text-ink">{{ $product->name }}</span>
                            @if ($product->launch_date)
                                <span class="text-sm text-muted">{{ $product->launch_date->format('F Y') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-ui.reveal>
        @endif
    </div>
</section>

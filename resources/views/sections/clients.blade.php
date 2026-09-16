@props(['clients'])

@if ($clients->isNotEmpty())
    <section class="border-t border-ink/10 bg-paper py-(--spacing-band-sm)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.reveal from="none">
                <p class="text-eyebrow text-center font-semibold uppercase text-muted">
                    Trusted by
                </p>
            </x-ui.reveal>

            <x-ui.logo-wall :clients="$clients" class="mt-12" />
        </div>
    </section>
@endif

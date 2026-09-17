@props(['testimonials'])

@if ($testimonials->isNotEmpty())
    <section class="border-t border-content/10 bg-paper-dim py-(--spacing-band)">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <x-ui.section-header
                eyebrow="{{ __('In their words') }}"
                title="{{ __('What the institutions say') }}"
            />

            <div class="mt-16 grid gap-6 md:grid-cols-2">
                @foreach ($testimonials as $index => $testimonial)
                    <x-ui.reveal :delay="$index * 120" class="h-full">
                        <x-ui.quote
                            :quote="$testimonial->quote"
                            :name="$testimonial->person_name"
                            :role="$testimonial->person_role"
                            :organisation="$testimonial->organisation"
                            :photo="$testimonial->getFirstMediaUrl('photo', 'thumb') ?: null"
                        />
                    </x-ui.reveal>
                @endforeach
            </div>
        </div>
    </section>
@endif

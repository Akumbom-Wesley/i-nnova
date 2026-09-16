@extends('layouts.app')

@section('title', 'Products | I-NNOVA')
@section('description', 'Software in service across education, health, hospitality and retail in Cameroon.')

@section('content')
    <x-ui.page-header
        eyebrow="Our solutions"
        title="Software that runs the working day"
        lead="Built for institutions where a system going down is not an inconvenience but a stopped day."
        motif="circuit"
    />

    @if ($live->isNotEmpty())
        <section class="bg-paper py-(--spacing-band)">
            <div class="mx-auto max-w-6xl px-4 sm:px-6">
                <div class="grid gap-6 sm:grid-cols-2">
                    @foreach ($live as $index => $product)
                        <x-ui.reveal :delay="$index * 90" class="h-full">
                            <x-ui.card
                                :href="route('products.show', $product)"
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
            </div>
        </section>
    @endif

    @if ($comingSoon->isNotEmpty())
        <section class="relative overflow-hidden border-t border-ink/10 bg-paper-dim py-(--spacing-band)">
            <x-tech.grid class="pointer-events-none absolute inset-x-0 top-0 h-52 w-full text-primary/[0.09]" />

            <div class="relative mx-auto max-w-6xl px-4 sm:px-6">
                <x-ui.section-header
                    eyebrow="In development"
                    title="Not shipped yet"
                    lead="Listed so you know they are coming, and marked so nobody mistakes them for something you can buy today."
                />

                <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($comingSoon as $index => $product)
                        <x-ui.reveal :delay="$index * 90" class="h-full">
                            <x-ui.card
                                :href="route('products.show', $product)"
                                :eyebrow="$product->sector?->name"
                                :title="$product->name"
                                :body="$product->tagline"
                                :badge="$product->launch_date ? 'Expected ' . $product->launch_date->format('M Y') : 'Coming soon'"
                                badge-tone="accent"
                            />
                        </x-ui.reveal>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @include('sections.cta', ['settings' => $settings])
@endsection

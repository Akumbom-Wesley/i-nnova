@extends('layouts.app')

@section('title', $title . ' | I-NNOVA')
@section('description', '')

@section('content')
    <section class="relative overflow-hidden bg-paper py-(--spacing-band-lg)">
        <div class="pointer-events-none absolute inset-x-0 -top-40 h-96 bg-gradient-to-b from-primary-soft to-transparent"
             aria-hidden="true"></div>

        <x-tech.circuit class="pointer-events-none absolute -right-20 top-0 hidden w-[30rem] text-primary/[0.11] lg:block" />

        <div class="relative mx-auto max-w-3xl px-4 sm:px-6">
            <p class="font-display text-display text-primary/25" aria-hidden="true">{{ $code }}</p>

            <h1 class="mt-2 font-display text-h1 text-ink">{{ $title }}</h1>

            <div class="rule-draw mt-6 h-0.5 w-16 bg-accent" data-revealed aria-hidden="true"></div>

            <p class="text-lead mt-7 max-w-xl text-muted">{{ $body }}</p>

            <div class="mt-10 flex flex-wrap gap-4">
                <x-ui.button :href="url('/')" size="lg">Back to the home page</x-ui.button>
                <x-ui.button :href="url('/contact')" variant="outline" size="lg">Tell us what broke</x-ui.button>
            </div>

            <div class="mt-16 border-t border-ink/10 pt-10">
                <p class="text-eyebrow font-semibold uppercase text-muted">Or try one of these</p>

                <ul class="mt-5 flex flex-wrap gap-x-8 gap-y-3">
                    @foreach ([
                        'Products' => '/products',
                        'Work' => '/work',
                        'Kickstarter' => '/kickstarter',
                        'About' => '/about',
                    ] as $label => $href)
                        <li>
                            <a href="{{ url($href) }}" class="link-underline font-semibold text-primary">{{ $label }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>
@endsection

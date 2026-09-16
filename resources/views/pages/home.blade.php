@extends('layouts.app')

@section('title', 'I-NNOVA — Software for institutions, built in Bamenda')
@section('description', 'I-NNOVA builds and operates software for universities, schools, hotels and retail businesses in Cameroon — and trains the engineers who build it.')

@section('content')
    {{-- Sprint 2 replaces this with the real hero, products, proof, values and team sections. --}}
    <section class="mx-auto max-w-6xl px-4 pt-20 pb-24 sm:px-6 sm:pt-28">
        <p class="text-xs font-semibold uppercase tracking-widest text-primary">Bamenda, Cameroon</p>

        <h1 class="mt-6 max-w-3xl font-display text-5xl leading-[1.05] tracking-tight sm:text-6xl lg:text-7xl">
            Practical software for institutions that cannot afford downtime.
        </h1>

        <p class="mt-8 max-w-xl text-lg leading-relaxed text-muted">
            We design, build and run the systems universities, schools and businesses depend on every day —
            and we train the engineers who build them.
        </p>

        <div class="mt-10 flex flex-wrap items-center gap-4">
            <a href="{{ url('/work') }}"
               class="rounded-full bg-accent px-6 py-3 text-base font-semibold text-white transition-colors hover:bg-accent-dark">
                See our work
            </a>
            <a href="{{ url('/products') }}"
               class="rounded-full border border-ink/20 px-6 py-3 text-base font-semibold transition-colors hover:border-ink/40">
                Explore products
            </a>
        </div>
    </section>

    <section class="border-y border-ink/10 bg-paper-dim">
        <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
            <p class="font-display text-3xl leading-snug sm:text-4xl">
                We don't just build software — we build the builders.
            </p>
        </div>
    </section>
@endsection

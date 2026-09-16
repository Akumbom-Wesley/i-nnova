@extends('layouts.app')

@php
    $seoTitle = __('Contact') . ' | I-NNOVA';
    $seoDescription = __('Tell us what your institution is wrestling with.');
@endphp

@section('content')
    <x-ui.page-header
        eyebrow="{{ __('Contact') }}"
        title="{{ __('Tell us what your institution is wrestling with.') }}"
        lead="{{ __('A short conversation is usually enough to tell whether we are the right fit.') }}"
        motif="waveform"
    />

    <section class="bg-paper py-(--spacing-band)">
        <div class="mx-auto grid max-w-6xl gap-16 px-4 sm:px-6 lg:grid-cols-[1.3fr_1fr]">
            <div>
                @if (session('status'))
                    <div role="status"
                         class="mb-10 flex items-start gap-4 rounded-2xl border border-primary/30 bg-primary-soft p-6">
                        <svg class="mt-0.5 h-6 w-6 shrink-0 text-primary" fill="none" stroke="currentColor"
                             stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/>
                        </svg>
                        <p class="font-medium text-ink">{{ session('status') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert"
                         class="mb-10 rounded-2xl border border-accent-text/30 bg-accent-soft p-6">
                        <p class="font-semibold text-accent-text">
                            Please check {{ $errors->count() === 1 ? 'one field' : $errors->count() . ' fields' }} below.
                        </p>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.store') }}" class="relative space-y-6" novalidate>
                    @csrf
                    <x-form.honeypot />

                    <div class="grid gap-6 sm:grid-cols-2">
                        <x-form.field name="name" label="{{ __('Your name') }}" :required="true" />
                        <x-form.field name="email" label="{{ __('Email') }}" type="email" :required="true" />
                        <x-form.field name="phone" label="{{ __('Phone') }}" type="tel" />
                        <x-form.field name="organisation" label="{{ __('Organisation') }}" />
                    </div>

                    <x-form.field name="subject" label="{{ __('Subject') }}" />

                    <x-form.field
                        name="message"
                        label="{{ __('Message') }}"
                        :rows="7"
                        :required="true"
                        hint="What are you trying to solve, and roughly how many people does it affect?"
                    />

                    <x-ui.button type="submit" size="lg">{{ __('Send message') }}</x-ui.button>
                </form>
            </div>

            <aside class="space-y-10">
                <x-ui.reveal from="right" class="rounded-2xl border border-ink/10 bg-paper-dim p-8">
                    <h2 class="text-eyebrow font-semibold uppercase text-muted">Reach us directly</h2>

                    <ul class="mt-6 space-y-5">
                        @if ($settings->whatsapp_number)
                            @php $whatsapp = preg_replace('/\D+/', '', $settings->whatsapp_number); @endphp
                            <li>
                                <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                                   class="group inline-flex items-center gap-3 font-semibold text-ink">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-accent text-white transition-transform duration-300 ease-[var(--ease-brand)] group-hover:scale-105">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path d="M12 2a10 10 0 0 0-8.6 15L2 22l5.2-1.4A10 10 0 1 0 12 2Zm5.8 14.2c-.2.7-1.2 1.3-1.9 1.4-.5.1-1.2.1-1.9-.1a13.6 13.6 0 0 1-6.5-5.6c-.5-.8-.8-1.7-.8-2.5 0-.8.4-1.5.9-1.9.2-.2.4-.3.6-.3h.5c.2 0 .4 0 .5.4l.8 1.9c0 .2 0 .3-.1.5l-.4.5c-.1.2-.3.3-.1.6a9 9 0 0 0 4 3.4c.3.1.5.1.6-.1l.8-.9c.2-.2.3-.2.6-.1l1.8.9c.3.1.4.2.4.4 0 .2 0 .8-.2 1.5Z"/>
                                        </svg>
                                    </span>
                                    <span class="link-underline">WhatsApp</span>
                                </a>
                            </li>
                        @endif

                        @if ($settings->contact_phone)
                            <li>
                                <p class="text-eyebrow font-semibold uppercase text-muted">Phone</p>
                                <a href="tel:{{ preg_replace('/\s+/', '', $settings->contact_phone) }}"
                                   class="link-underline mt-1 inline-block font-semibold text-ink">
                                    {{ $settings->contact_phone }}
                                </a>
                            </li>
                        @endif

                        @if ($settings->contact_email)
                            <li>
                                <p class="text-eyebrow font-semibold uppercase text-muted">Email</p>
                                <a href="mailto:{{ $settings->contact_email }}"
                                   class="link-underline mt-1 inline-block font-semibold text-ink">
                                    {{ $settings->contact_email }}
                                </a>
                            </li>
                        @endif

                        @if ($settings->address)
                            <li>
                                <p class="text-eyebrow font-semibold uppercase text-muted">Find us</p>
                                <p class="mt-1 whitespace-pre-line text-muted">{{ $settings->address }}</p>
                            </li>
                        @endif
                    </ul>
                </x-ui.reveal>
            </aside>
        </div>
    </section>
@endsection

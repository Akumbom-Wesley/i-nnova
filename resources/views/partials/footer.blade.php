@php
    $socials = array_filter($settings->socials ?? []);

    $socialLabels = [
        'facebook' => 'Facebook',
        'twitter' => 'X',
        'x' => 'X',
        'linkedin' => 'LinkedIn',
        'instagram' => 'Instagram',
        'github' => 'GitHub',
        'youtube' => 'YouTube',
    ];
@endphp

<footer class="relative overflow-hidden bg-ink text-white/75">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-(--spacing-band-sm) sm:px-6 lg:grid-cols-[1.5fr_1fr_1fr]">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="I-NNOVA" width="180" height="180"
                 loading="lazy" decoding="async" class="h-14 w-auto object-contain">

            <p class="mt-5 max-w-sm text-sm leading-relaxed">
                {{ __('Software for institutions, built in Bamenda, plus a programme building the people who write it.') }}
            </p>

            <p class="mt-5 font-display text-lg text-accent">{{ __('Build Your Creativity') }}</p>

            @if ($socials !== [])
                <ul class="mt-6 flex flex-wrap items-center gap-3">
                    @foreach ($socials as $network => $url)
                        <li>
                            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer"
                               class="group flex h-10 w-10 items-center justify-center rounded-full border border-white/20 transition-all duration-300 ease-[var(--ease-brand)] hover:-translate-y-0.5 hover:border-accent hover:bg-accent">
                                <x-brand.social-icon :network="$network" class="h-4 w-4 text-white/70 transition-colors duration-300 group-hover:text-white" />
                                <span class="sr-only">{{ $socialLabels[$network] ?? ucfirst($network) }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div>
            <h2 class="text-eyebrow font-semibold uppercase text-white/45">{{ __('Company') }}</h2>

            <ul class="mt-4 space-y-2 text-sm">
                @foreach ([
                    __('Products') => route('products.index'),
                    __('Work') => route('work.index'),
                    __('Kickstarter') => route('kickstarter'),
                    __('About') => route('about'),
                    __('Team') => route('about') . '#team',
                    __('Contact') => route('contact'),
                ] as $label => $href)
                    <li>
                        <a href="{{ $href }}" class="link-underline hover:text-white">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-eyebrow font-semibold uppercase text-white/45">{{ __('Contact') }}</h2>

            <ul class="mt-4 space-y-2 text-sm">
                @if ($settings->contact_phone)
                    <li>
                        <a href="tel:{{ preg_replace('/\s+/', '', $settings->contact_phone) }}"
                           class="link-underline hover:text-white">{{ $settings->contact_phone }}</a>
                    </li>
                @endif

                @if ($settings->contact_email)
                    <li>
                        <a href="mailto:{{ $settings->contact_email }}"
                           class="link-underline hover:text-white">{{ $settings->contact_email }}</a>
                    </li>
                @endif

                @if ($settings->address)
                    <li class="whitespace-pre-line pt-1 text-white/55">{{ $settings->address }}</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- The orange rule from the brand artwork, running the full width. --}}
    <div class="h-1 bg-accent" aria-hidden="true"></div>

    <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-5 text-xs sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p>&copy; {{ date('Y') }} I-NNOVA. {{ __('All rights reserved.') }}</p>

        <p class="text-white/45">{{ __('Transforming communities, empowering innovators') }}</p>
    </div>
</footer>

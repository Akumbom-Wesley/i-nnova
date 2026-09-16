<footer class="relative overflow-hidden bg-ink text-white/75">
    <div class="mx-auto grid max-w-6xl gap-12 px-4 py-(--spacing-band) sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <span class="font-display text-2xl text-white">I-NNOVA</span>

            <p class="mt-4 max-w-sm leading-relaxed">
                Software for institutions, built in Bamenda, plus a programme building the people who write it.
            </p>

            <p class="mt-6 font-display text-xl text-accent">Build Your Creativity</p>

            <p class="mt-2 text-eyebrow font-semibold uppercase text-white/45">
                Transforming communities, empowering innovators
            </p>
        </div>

        <div>
            <h2 class="text-eyebrow font-semibold uppercase text-white/45">Company</h2>

            <ul class="mt-5 space-y-3">
                @foreach ([
                    'Products'    => '/products',
                    'Work'        => '/work',
                    'Kickstarter' => '/kickstarter',
                    'About'       => '/about',
                    'Contact'     => '/contact',
                ] as $label => $href)
                    <li>
                        <a href="{{ url($href) }}" class="link-underline hover:text-white">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-eyebrow font-semibold uppercase text-white/45">Contact</h2>

            <ul class="mt-5 space-y-3">
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
                    <li class="whitespace-pre-line pt-1 text-sm text-white/55">{{ $settings->address }}</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- The orange rule from the brand artwork, running the full width. --}}
    <div class="h-1 bg-accent" aria-hidden="true"></div>

    <div class="mx-auto flex max-w-6xl flex-col gap-3 px-4 py-6 text-xs sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <p>&copy; {{ date('Y') }} I-NNOVA. All rights reserved.</p>

        <div class="flex gap-6">
            <a href="{{ url('/privacy') }}" class="link-underline hover:text-white">Privacy</a>
            <a href="{{ url('/terms') }}" class="link-underline hover:text-white">Terms</a>
        </div>
    </div>
</footer>

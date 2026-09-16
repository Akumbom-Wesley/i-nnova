<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-ink/10 bg-paper/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4 sm:px-6">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <img src="{{ asset('images/logo-mark.png') }}" alt="" class="h-9 w-9 object-contain" aria-hidden="true">
            <span class="font-display text-xl leading-none tracking-tight">I-NNOVA</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex" aria-label="Primary">
            @foreach ([
                'Products'    => '/products',
                'Work'        => '/work',
                'Kickstarter' => '/kickstarter',
                'About'       => '/about',
            ] as $label => $href)
                <a href="{{ url($href) }}"
                   class="text-sm font-medium text-ink/80 transition-colors hover:text-primary">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-4 md:flex">
            <div class="flex items-center gap-1 text-xs font-medium text-muted" aria-label="Language">
                <span class="rounded px-1.5 py-0.5 text-ink">EN</span>
                <span aria-hidden="true">/</span>
                <span class="rounded px-1.5 py-0.5 transition-colors hover:text-ink">FR</span>
            </div>
            <a href="{{ url('/contact') }}"
               class="rounded-full bg-accent px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-accent-dark">
                Contact
            </a>
        </div>

        <button type="button" @click="open = !open"
                class="inline-flex h-10 w-10 items-center justify-center rounded md:hidden"
                :aria-expanded="open" aria-controls="mobile-nav" aria-label="Toggle menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <div id="mobile-nav" x-show="open" x-cloak x-transition.opacity class="border-t border-ink/10 bg-paper md:hidden">
        <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-4 sm:px-6" aria-label="Mobile">
            @foreach ([
                'Products'    => '/products',
                'Work'        => '/work',
                'Kickstarter' => '/kickstarter',
                'About'       => '/about',
                'Contact'     => '/contact',
            ] as $label => $href)
                <a href="{{ url($href) }}" class="rounded px-2 py-3 text-base font-medium hover:bg-paper-dim">{{ $label }}</a>
            @endforeach
        </nav>
    </div>
</header>

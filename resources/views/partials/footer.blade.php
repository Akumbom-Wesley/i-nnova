<footer class="mt-24 bg-ink text-bone/80">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-16 sm:px-6 md:grid-cols-4">
        <div class="md:col-span-2">
            <span class="font-display text-2xl text-bone">I-NNOVA CM</span>
            <p class="mt-3 max-w-sm text-sm leading-relaxed">
                Software for institutions, built in Bamenda — and a programme building the people who write it.
            </p>
            <p class="mt-6 font-display text-xl text-accent">Make It Happen</p>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-bone/50">Company</h2>
            <ul class="mt-4 space-y-2 text-sm">
                @foreach ([
                    'Products'    => '/products',
                    'Work'        => '/work',
                    'Kickstarter' => '/kickstarter',
                    'About'       => '/about',
                    'Contact'     => '/contact',
                ] as $label => $href)
                    <li><a href="{{ url($href) }}" class="transition-colors hover:text-bone">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-bone/50">Contact</h2>
            {{-- Sprint 5: real details from Site Settings. Nothing placeholder ships. --}}
            <ul class="mt-4 space-y-2 text-sm">
                <li>Bamenda, North West Region</li>
                <li>Cameroon</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-ink-line">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-xs sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <p>&copy; {{ date('Y') }} I-NNOVA CM. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="{{ url('/privacy') }}" class="transition-colors hover:text-bone">Privacy</a>
                <a href="{{ url('/terms') }}" class="transition-colors hover:text-bone">Terms</a>
            </div>
        </div>
    </div>
</footer>

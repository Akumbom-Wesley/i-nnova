@php
    $nav = [
        __('Products') => route('products.index'),
        __('Work') => route('work.index'),
        __('Kickstarter') => route('kickstarter'),
        __('About') => route('about'),
    ];

    // The same page in the other language, so switching never dumps the
    // reader back on the home page.
    $route = request()->route();
    $current = app()->getLocale();
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-content/10 bg-paper/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4 sm:px-6">
        {{-- The cubed wordmark carries the name, so there is no text beside
             it to repeat. The alt text is the company name rather than a
             description of the picture. --}}
        <a href="{{ route('home') }}" class="flex items-center">
            <img src="{{ asset('images/wordmark-cubes.png') }}" alt="I-NNOVA"
                 width="245" height="77" fetchpriority="high" decoding="async"
                 class="h-9 w-auto object-contain sm:h-10">
            <span class="sr-only">, {{ __('Home') }}</span>
        </a>

        <nav class="hidden items-center gap-8 md:flex" aria-label="{{ __('Primary') }}">
            @foreach ($nav as $label => $href)
                <a href="{{ $href }}"
                   @if (url()->current() === $href) aria-current="page" @endif
                   @class([
                       'text-sm font-medium transition-colors hover:text-primary',
                       'text-primary' => url()->current() === $href,
                       'text-content/80' => url()->current() !== $href,
                   ])>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-4 md:flex">
            <div class="flex items-center gap-1 text-xs font-semibold" role="group" aria-label="{{ __('Language') }}">
                @foreach (config('site.locales') as $locale => $name)
                    @if ($locale === $current)
                        <span class="rounded px-1.5 py-0.5 text-content" aria-current="true">
                            {{ strtoupper($locale) }}
                            <span class="sr-only">({{ $name }}, {{ __('current') }})</span>
                        </span>
                    @else
                        <a href="{{ $route ? route($route->getName(), array_merge($route->parameters(), ['locale' => $locale])) : url('/' . $locale) }}"
                           hreflang="{{ $locale }}"
                           class="rounded px-1.5 py-0.5 text-muted transition-colors hover:text-content">
                            {{ strtoupper($locale) }}
                            <span class="sr-only">({{ $name }})</span>
                        </a>
                    @endif
                    @if (! $loop->last)
                        <span aria-hidden="true" class="text-content/25">/</span>
                    @endif
                @endforeach
            </div>

            <x-ui.theme-toggle />

            <a href="{{ route('contact') }}"
               class="rounded-full bg-accent px-5 py-2.5 text-base font-semibold text-white transition-colors hover:bg-accent-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-dark">
                {{ __('Contact') }}
            </a>
        </div>

        <button type="button" @click="open = !open"
                class="inline-flex h-10 w-10 items-center justify-center rounded focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary md:hidden"
                :aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-nav">
            <span class="sr-only">{{ __('Toggle menu') }}</span>
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
            </svg>
        </button>
    </div>

    <div id="mobile-nav" x-show="open" x-cloak x-transition.opacity class="border-t border-content/10 bg-paper md:hidden">
        <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-4 sm:px-6" aria-label="{{ __('Mobile') }}">
            @foreach ($nav + [__('Contact') => route('contact')] as $label => $href)
                <a href="{{ $href }}" class="rounded px-2 py-3 text-base font-medium hover:bg-paper-dim">{{ $label }}</a>
            @endforeach

            <div class="mt-3 flex items-center gap-3 border-t border-content/10 px-2 pt-4 text-sm font-semibold">
                <span class="text-muted">{{ __('Language') }}</span>

                @foreach (config('site.locales') as $locale => $name)
                    @if ($locale === $current)
                        <span class="text-content" aria-current="true">{{ $name }}</span>
                    @else
                        <a href="{{ $route ? route($route->getName(), array_merge($route->parameters(), ['locale' => $locale])) : url('/' . $locale) }}"
                           hreflang="{{ $locale }}" class="text-primary hover:text-accent-text">{{ $name }}</a>
                    @endif
                @endforeach
            </div>
        </nav>
    </div>
</header>

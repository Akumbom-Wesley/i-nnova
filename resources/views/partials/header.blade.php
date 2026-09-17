@php
    use App\Support\Navigation;

    $menu = Navigation::menu();

    // The same page in the other language, so switching never dumps the
    // reader back on the home page.
    $route = request()->route();
    $current = app()->getLocale();

    $localeUrl = fn (string $locale): string => $route && $route->getName()
        ? route($route->getName(), array_merge($route->parameters(), ['locale' => $locale]))
        : url('/' . $locale);
@endphp

<header x-data="{ open: false, panel: null }" x-on:keydown.escape="panel = null; open = false"
        class="sticky top-0 z-40 border-b border-hairline bg-paper/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-6 px-4 py-4 sm:px-6">
        {{-- The cubed wordmark carries the name, so there is no text beside it
             to repeat. The alt text is the company name rather than a
             description of the picture. --}}
        <a href="{{ route('home') }}" class="flex shrink-0 items-center">
            <img src="{{ asset('images/wordmark-cubes.png') }}" alt="I-NNOVA"
                 width="245" height="77" fetchpriority="high" decoding="async"
                 class="h-9 w-auto object-contain sm:h-10">
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="{{ __('Primary') }}">
            @foreach ($menu as $index => $item)
                @php $isCurrent = Navigation::isCurrent($item); @endphp

                @if ($item['children'] === [])
                    <a href="{{ $item['url'] }}"
                       @if ($isCurrent) aria-current="page" @endif
                       @class([
                           'rounded-full px-3 py-2 text-sm font-medium transition-colors hover:text-primary',
                           'text-primary' => $isCurrent,
                           'text-content/80' => ! $isCurrent,
                       ])>{{ $item['label'] }}</a>
                @else
                    {{-- Opens on hover for a pointer and on click for a keyboard,
                         and closes when focus leaves the group entirely. --}}
                    <div class="relative"
                         x-on:mouseenter="panel = {{ $index }}"
                         x-on:mouseleave="panel === {{ $index }} && (panel = null)"
                         x-on:focusout="!$el.contains($event.relatedTarget) && panel === {{ $index }} && (panel = null)">
                        <button type="button"
                                x-on:click="panel = panel === {{ $index }} ? null : {{ $index }}"
                                x-bind:aria-expanded="panel === {{ $index }} ? 'true' : 'false'"
                                @if ($isCurrent) aria-current="page" @endif
                                @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-3 py-2 text-sm font-medium transition-colors hover:text-primary',
                                    'text-primary' => $isCurrent,
                                    'text-content/80' => ! $isCurrent,
                                ])>
                            {{ $item['label'] }}
                            <svg class="h-3.5 w-3.5 transition-transform duration-200"
                                 x-bind:class="panel === {{ $index }} && 'rotate-180'"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div x-show="panel === {{ $index }}" x-cloak x-transition.opacity.duration.150ms
                             class="absolute left-0 top-full w-60 pt-2">
                            <ul class="overflow-hidden rounded-2xl border border-hairline bg-paper py-2 shadow-xl shadow-shade/10">
                                @foreach ($item['children'] as $child)
                                    <li>
                                        <a href="{{ $child['url'] }}"
                                           class="block px-4 py-2.5 text-sm text-content/80 transition-colors hover:bg-paper-dim hover:text-primary">
                                            {{ $child['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            <div class="flex items-center gap-1 text-xs font-semibold" role="group" aria-label="{{ __('Language') }}">
                @foreach (config('site.locales') as $locale => $name)
                    @if ($locale === $current)
                        <span class="rounded px-1.5 py-0.5 text-content" aria-current="true">
                            {{ strtoupper($locale) }}
                            <span class="sr-only">({{ $name }}, {{ __('current') }})</span>
                        </span>
                    @else
                        <a href="{{ $localeUrl($locale) }}" hreflang="{{ $locale }}"
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
               class="ml-1 rounded-full bg-accent px-5 py-2.5 text-base font-semibold text-white transition-colors hover:bg-accent-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent-dark">
                {{ __('Contact') }}
            </a>
        </div>

        <div class="flex items-center gap-1 lg:hidden">
            <x-ui.theme-toggle />

            <button type="button" x-on:click="open = !open"
                    class="inline-flex h-10 w-10 items-center justify-center rounded focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
                    x-bind:aria-expanded="open ? 'true' : 'false'" aria-controls="mobile-nav">
                <span class="sr-only">{{ __('Toggle menu') }}</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path x-show="!open" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/>
                    <path x-show="open" x-cloak stroke-linecap="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-nav" x-show="open" x-cloak x-transition.opacity
         class="max-h-[calc(100dvh-5rem)] overflow-y-auto border-t border-hairline bg-paper lg:hidden">
        <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-4 sm:px-6" aria-label="{{ __('Mobile') }}">
            @foreach ($menu as $index => $item)
                @if ($item['children'] === [])
                    <a href="{{ $item['url'] }}" class="rounded px-2 py-3 text-base font-medium hover:bg-paper-dim">
                        {{ $item['label'] }}
                    </a>
                @else
                    <div>
                        <button type="button" x-on:click="panel = panel === {{ $index }} ? null : {{ $index }}"
                                x-bind:aria-expanded="panel === {{ $index }} ? 'true' : 'false'"
                                class="flex w-full items-center justify-between rounded px-2 py-3 text-base font-medium hover:bg-paper-dim">
                            {{ $item['label'] }}
                            <svg class="h-4 w-4 transition-transform duration-200"
                                 x-bind:class="panel === {{ $index }} && 'rotate-180'"
                                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <ul x-show="panel === {{ $index }}" x-cloak class="mb-1 ml-2 border-l border-hairline pl-3">
                            @foreach ($item['children'] as $child)
                                <li>
                                    <a href="{{ $child['url'] }}" class="block rounded px-2 py-2.5 text-sm text-muted hover:bg-paper-dim hover:text-content">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @endforeach

            <a href="{{ route('contact') }}"
               class="mt-2 rounded-full bg-accent px-5 py-3 text-center text-base font-semibold text-white">
                {{ __('Contact') }}
            </a>

            <div class="mt-3 flex items-center gap-3 border-t border-hairline px-2 pt-4 text-sm font-semibold">
                <span class="text-muted">{{ __('Language') }}</span>

                @foreach (config('site.locales') as $locale => $name)
                    @if ($locale === $current)
                        <span class="text-content" aria-current="true">{{ $name }}</span>
                    @else
                        <a href="{{ $localeUrl($locale) }}" hreflang="{{ $locale }}" class="text-primary hover:text-accent-text">{{ $name }}</a>
                    @endif
                @endforeach
            </div>
        </nav>
    </div>
</header>

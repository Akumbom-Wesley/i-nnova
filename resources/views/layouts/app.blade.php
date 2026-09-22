<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{--
        Pages pass their own values through the seoTitle, seoDescription and
        seoImage stack variables. Anything not set falls back to Site Settings.

        Note for future pages: Blade's two-argument @section calls ob_start()
        when the value is null, which leaks an output buffer, so pass a string
        rather than a nullable model attribute.
    --}}
    <x-seo.meta
        :title="$seoTitle ?? null"
        :description="$seoDescription ?? null"
        :image="$seoImage ?? null"
        :type="$seoType ?? 'website'"
    />

    {{--
        Applies a remembered appearance before the stylesheet is parsed, so
        a reader who has chosen dark never gets a white flash first. It is
        inline and tiny on purpose: a separate file would arrive too late
        to be any use.
    --}}
    <script nonce="{{ $cspNonce ?? '' }}">
        (function () {
            try {
                var mode = localStorage.getItem('theme');
                if (mode === 'dark' || mode === 'light') {
                    document.documentElement.setAttribute('data-theme', mode);
                }
            } catch (e) {
                // Storage can be blocked. Falling through leaves the system
                // preference in charge, which is the right default anyway.
            }
        })();
    </script>

    <link rel="icon" href="{{ asset('images/logo-mark.png') }}" type="image/png">

    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-seo.organization />

    @stack('schema')
</head>
<body class="min-h-screen bg-paper font-sans text-content antialiased">
    <a href="#main"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:rounded focus:bg-ink focus:px-4 focus:py-2 focus:text-paper">
        {{ __('Skip to content') }}
    </a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')
</body>
</html>

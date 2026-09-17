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

    <link rel="icon" href="{{ asset('images/logo-mark.png') }}" type="image/png">

    {{ Vite::fonts() }}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <x-seo.organization />

    @stack('schema')
</head>
<body class="min-h-screen bg-paper font-sans text-ink antialiased">
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

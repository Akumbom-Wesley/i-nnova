@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'type' => 'website',
])

@php
    $settings = \App\Models\SiteSetting::instance();
    $route = request()->route();

    $title = $title ?: ($settings->seo_title ?: config('app.name'));
    $description = $description ?: ($settings->seo_description ?: '');

    // Falls back to the OG image set in the admin. The old site linked one
    // that 404d, so anything emitted here has to resolve.
    $image = $image ?: $settings->getFirstMediaUrl('og_image');

    $canonical = url()->current();

    $alternates = [];

    if ($route && $route->getName()) {
        foreach (array_keys(config('site.locales')) as $locale) {
            $alternates[$locale] = route(
                $route->getName(),
                array_merge($route->parameters(), ['locale' => $locale]),
            );
        }
    }
@endphp

<title>{{ $title }}</title>
<link rel="canonical" href="{{ $canonical }}">

@if ($description)
    <meta name="description" content="{{ $description }}">
@endif

@foreach ($alternates as $locale => $href)
    <link rel="alternate" hreflang="{{ $locale }}" href="{{ $href }}">
@endforeach

@if ($alternates !== [])
    <link rel="alternate" hreflang="x-default" href="{{ $alternates[config('site.default_locale')] ?? $canonical }}">
@endif

<meta property="og:type" content="{{ $type }}">
<meta property="og:site_name" content="I-NNOVA">
<meta property="og:title" content="{{ $title }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="{{ str_replace('-', '_', app()->getLocale()) }}">

@if ($description)
    <meta property="og:description" content="{{ $description }}">
@endif

@if ($image)
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $image }}">
@else
    <meta name="twitter:card" content="summary">
@endif

<meta name="twitter:title" content="{{ $title }}">

@if ($description)
    <meta name="twitter:description" content="{{ $description }}">
@endif

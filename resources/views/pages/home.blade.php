@extends('layouts.app')

{{--
    Both of these must be non-null. Blade's two-argument @section treats a null
    value as the start of a block section and calls ob_start(), so a null here
    leaks an output buffer on every render.
--}}
@php
    $seoTitle = $settings->seo_title ?: 'I-NNOVA | Software for institutions, built in Bamenda';
    $seoDescription = $settings->seo_description ?? '';
@endphp

@section('content')
    @include('sections.hero', ['settings' => $settings, 'stats' => $stats])
    @include('sections.products', ['products' => $products, 'comingSoon' => $comingSoon])
    @include('sections.stem', ['tracks' => $tracks])
    @include('sections.deployments', ['caseStudies' => $caseStudies])
    @include('sections.clients', ['clients' => $clients])
    @include('sections.photo-strip', ['images' => $photos])
    @include('sections.values', ['values' => $values])
    @include('sections.team', ['team' => $team])
    @include('sections.kickstarter', ['settings' => $settings])
    @include('sections.testimonials', ['testimonials' => $testimonials])
    @include('sections.cta', ['settings' => $settings])
@endsection

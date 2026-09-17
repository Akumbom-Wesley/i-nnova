@props([
    'image',
    'conversion' => 'thumb',
    'width' => 800,
    'height' => 600,
])

{{--
    One tile in a gallery: a photograph, an embedded video, or an uploaded
    video file. A video still uses its image as the poster frame, so a tile
    never renders as a black rectangle while the player loads.
--}}
@php
    $embed = $image->videoEmbedUrl();
    $file = $image->videoFileUrl();
    $poster = $image->displayUrl('thumb');
@endphp

@if ($embed)
    <iframe
        src="{{ $embed }}"
        title="{{ $image->altText() ?: __('Video') }}"
        loading="lazy"
        referrerpolicy="strict-origin-when-cross-origin"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
        {{ $attributes->class('h-full w-full border-0') }}
    ></iframe>
@elseif ($file)
    <video
        controls
        preload="none"
        @if ($poster) poster="{{ $poster }}" @endif
        {{ $attributes->class('h-full w-full object-cover') }}
    >
        <source src="{{ $file }}">
        {{ __('Your browser cannot play this video.') }}
    </video>
@else
    <x-ui.photo :image="$image" :conversion="$conversion" :width="$width" :height="$height" {{ $attributes }} />
@endif

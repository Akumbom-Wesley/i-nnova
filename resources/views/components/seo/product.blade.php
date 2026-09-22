@props(['product'])

@php
    $schema = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'SoftwareApplication',
        'name' => $product->name,
        'description' => $product->tagline,
        'url' => route('products.show', $product),
        'applicationCategory' => 'BusinessApplication',
        'image' => $product->getFirstMediaUrl('cover') ?: null,
        'provider' => [
            '@type' => 'Organization',
            'name' => 'I-NNOVA',
            'url' => route('home'),
        ],
    ]);
@endphp

<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

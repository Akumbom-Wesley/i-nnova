@props(['people'])

@php
    $schema = $people
        ->map(fn ($person) => array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $person->name,
            'jobTitle' => $person->role ?? $person->title ?? null,
            'image' => $person->getFirstMediaUrl('photo') ?: null,
            'worksFor' => [
                '@type' => 'Organization',
                'name' => 'I-NNOVA',
                'url' => route('home'),
            ],
        ]))
        ->values()
        ->all();
@endphp

@if ($schema !== [])
    <script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif

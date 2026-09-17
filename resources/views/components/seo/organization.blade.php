@php
    $settings = \App\Models\SiteSetting::instance();

    $organization = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'I-NNOVA',
        'url' => route('home'),
        'logo' => asset('images/logo.png'),
        'description' => $settings->seo_description,
        'email' => $settings->contact_email,
        'telephone' => $settings->contact_phone,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $settings->address ? str_replace("\n", ', ', $settings->address) : null,
            'addressLocality' => 'Bamenda',
            'addressRegion' => 'North West Region',
            'addressCountry' => 'CM',
        ]),
        'sameAs' => array_values(array_filter($settings->socials ?? [])),
    ]);
@endphp

<script type="application/ld+json">{!! json_encode($organization, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
@foreach ($routes as $entry)
@foreach ($locales as $locale)
    <url>
        <loc>{{ route($entry['name'], array_merge(['locale' => $locale], $entry['params'])) }}</loc>
        <changefreq>{{ $entry['frequency'] }}</changefreq>
        <priority>{{ $entry['priority'] }}</priority>
@foreach ($locales as $alternate)
        <xhtml:link rel="alternate" hreflang="{{ $alternate }}" href="{{ route($entry['name'], array_merge(['locale' => $alternate], $entry['params'])) }}"/>
@endforeach
    </url>
@endforeach
@endforeach
</urlset>

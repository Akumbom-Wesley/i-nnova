@props(['client', 'logo'])

<a href="{{ $client->website_url }}" target="_blank" rel="noopener noreferrer"
   class="block focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
    <x-ui.logo-wall-item :client="$client" :logo="$logo" />
</a>

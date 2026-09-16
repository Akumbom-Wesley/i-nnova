@props(['partner', 'logo'])

<a href="{{ $partner->website_url }}" target="_blank" rel="noopener noreferrer"
   class="block rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
    <x-ui.partner-mark :partner="$partner" :logo="$logo" />
    <span class="sr-only">{{ $partner->name }}</span>
</a>

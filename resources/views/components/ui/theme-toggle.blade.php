{{--
    Light and dark. Three states rather than two: following the system is the
    default, and choosing either one overrides it until the reader clears it.

    Rendered with both icons present and one hidden by CSS, so the correct one
    shows on the very first paint without waiting for a script.
--}}
<button
    type="button"
    x-data="themeToggle"
    x-on:click="cycle()"
    x-bind:aria-label="label"
    x-bind:title="label"
    class="inline-flex h-9 w-9 items-center justify-center rounded-full text-content/70 transition-colors hover:bg-paper-dim hover:text-content"
>
    <svg class="h-5 w-5" x-show="mode === 'light'" x-cloak fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="4"/>
        <path stroke-linecap="round" d="M12 3v2m0 14v2M5.6 5.6l1.4 1.4m10 10 1.4 1.4M3 12h2m14 0h2M5.6 18.4 7 17m10-10 1.4-1.4"/>
    </svg>

    <svg class="h-5 w-5" x-show="mode === 'dark'" x-cloak fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13.5A8 8 0 0 1 10.5 4a8 8 0 1 0 9.5 9.5Z"/>
    </svg>

    <svg class="h-5 w-5" x-show="mode === 'system'" x-cloak fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
        <rect x="3" y="4" width="18" height="13" rx="2"/>
        <path stroke-linecap="round" d="M8 21h8m-4-4v4"/>
    </svg>
</button>

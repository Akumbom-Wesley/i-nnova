@php $items = $this->getItems(); @endphp

<x-filament-widgets::widget>
    <x-filament::section
        icon="heroicon-o-clipboard-document-check"
        :icon-color="$items === [] ? 'success' : 'warning'"
        heading="Before launch"
        :description="$items === []
            ? 'Nothing outstanding. Every check below has been satisfied.'
            : 'Each of these is a live check, so an item disappears once it is actually done.'"
    >
        @if ($items === [])
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No stand-in photographs, no unverified clients, and the contact details are set.
            </p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($items as $item)
                    <li>
                        <a href="{{ $item['url'] }}"
                           class="group flex items-center gap-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                            <span @class([
                                'flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-sm font-semibold tabular-nums',
                                'bg-warning-100 text-warning-700 dark:bg-warning-400/10 dark:text-warning-400',
                            ])>
                                {{ $item['count'] }}
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-medium text-gray-950 dark:text-white">
                                    {{ $item['label'] }}
                                </span>
                                <span class="block text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item['detail'] }}
                                </span>
                            </span>

                            <x-filament::icon
                                icon="heroicon-m-chevron-right"
                                class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-x-0.5"
                            />
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

@props([
    'paginator',
    'label' => null,
])

{{--
    Pagination in the site's own vocabulary rather than the framework default.
    Numbers on a wide screen, previous and next plus a count on a phone, where
    a row of eleven numbers would wrap into a mess.
--}}
@if ($paginator->hasPages())
    <nav class="mt-16 flex flex-col items-center gap-5"
         aria-label="{{ $label ?? __('Pagination') }}">
        <ul class="flex flex-wrap items-center justify-center gap-2">
            <li>
                @if ($paginator->onFirstPage())
                    <span class="pagination-link is-disabled" aria-disabled="true">
                        <x-icon.chevron-left />
                        <span class="sr-only">{{ __('Previous page') }}</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link">
                        <x-icon.chevron-left />
                        <span class="sr-only">{{ __('Previous page') }}</span>
                    </a>
                @endif
            </li>

            {{-- Numbers are for pointing devices and wide screens. On a phone
                 the previous, next and position line below carry it. --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @php
                    $isCurrent = $page === $paginator->currentPage();
                    // Always the first, the last, and the pages either side of
                    // where you are. Everything else collapses to an ellipsis.
                    $isNear = abs($page - $paginator->currentPage()) <= 1
                        || $page === 1
                        || $page === $paginator->lastPage();
                @endphp

                @if ($isNear)
                    <li class="hidden sm:block">
                        @if ($isCurrent)
                            <span class="pagination-link is-current" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="pagination-link">
                                <span class="sr-only">{{ __('Page') }}</span>{{ $page }}
                            </a>
                        @endif
                    </li>
                @elseif (abs($page - $paginator->currentPage()) === 2)
                    <li class="hidden px-1 text-muted sm:block" aria-hidden="true">&hellip;</li>
                @endif
            @endforeach

            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link">
                        <x-icon.chevron-right />
                        <span class="sr-only">{{ __('Next page') }}</span>
                    </a>
                @else
                    <span class="pagination-link is-disabled" aria-disabled="true">
                        <x-icon.chevron-right />
                        <span class="sr-only">{{ __('Next page') }}</span>
                    </span>
                @endif
            </li>
        </ul>

        <p class="text-sm text-muted">
            {{ __('Showing :first to :last of :total', [
                'first' => $paginator->firstItem(),
                'last' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </p>
    </nav>
@endif

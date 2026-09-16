@props([
    'as' => 'div',
    'from' => 'up',
    'delay' => 0,
])

{{--
    Scroll reveal wrapper. app.js adds [data-revealed] when this enters the
    viewport, which also starts any drawn rules or board traces nested inside.

    Under prefers-reduced-motion the CSS renders the finished state, so nothing
    here is load bearing for legibility.
--}}
<{{ $as }}
    data-reveal="{{ $from }}"
    @style(['--reveal-delay: ' . (int) $delay . 'ms' => $delay])
    {{ $attributes }}
>
    {{ $slot }}
</{{ $as }}>

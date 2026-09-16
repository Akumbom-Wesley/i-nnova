@props(['class' => ''])

{{--
    A terminal window with lines typing themselves out. The accelerator tracks
    are about shipping real work, and this is what that looks like.
--}}
<svg viewBox="0 0 420 300" fill="none" xmlns="http://www.w3.org/2000/svg"
     class="{{ $class }}" aria-hidden="true" focusable="false">

    <rect x="10" y="18" width="400" height="264" rx="14"
          stroke="currentColor" stroke-width="2.5" opacity="0.6"/>

    {{-- Title bar. --}}
    <path d="M10 58h400" stroke="currentColor" stroke-width="2.5" opacity="0.45"/>
    <g fill="currentColor" opacity="0.55">
        <circle cx="36" cy="38" r="5"/>
        <circle cx="56" cy="38" r="5"/>
        <circle cx="76" cy="38" r="5"/>
    </g>

    {{-- Lines, each typing itself and indented like real code. --}}
    @php
        $lines = [
            ['y' => 88,  'x' => 36,  'w' => 190, 'delay' => 0,    'accent' => true],
            ['y' => 116, 'x' => 56,  'w' => 240, 'delay' => 0.35, 'accent' => false],
            ['y' => 144, 'x' => 56,  'w' => 168, 'delay' => 0.7,  'accent' => false],
            ['y' => 172, 'x' => 76,  'w' => 210, 'delay' => 1.05, 'accent' => true],
            ['y' => 200, 'x' => 56,  'w' => 132, 'delay' => 1.4,  'accent' => false],
            ['y' => 228, 'x' => 36,  'w' => 96,  'delay' => 1.75, 'accent' => false],
        ];
    @endphp

    @foreach ($lines as $line)
        <rect
            x="{{ $line['x'] }}" y="{{ $line['y'] }}"
            width="{{ $line['w'] }}" height="9" rx="4.5"
            fill="{{ $line['accent'] ? 'var(--color-accent)' : 'currentColor' }}"
            opacity="{{ $line['accent'] ? '0.9' : '0.45' }}"
            class="code-line"
            style="--line-delay: {{ $line['delay'] }}s"
        />
    @endforeach

    {{-- Prompt and cursor on the last line. --}}
    <path d="M36 250l9 9-9 9" stroke="var(--color-accent)" stroke-width="3"
          stroke-linecap="round" stroke-linejoin="round"/>
    <rect x="56" y="250" width="14" height="18" rx="2" fill="currentColor" class="circuit-cursor"/>
</svg>

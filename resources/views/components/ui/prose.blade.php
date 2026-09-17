@props(['tone' => 'light'])

@php $isDark = $tone === 'dark'; @endphp

{{--
    Wrapper for rich text coming out of the admin editor. Tailwind's reset
    strips heading and list styling, so it has to be put back here rather than
    hoping editors only ever write paragraphs.
--}}
<div {{ $attributes->class([
    'max-w-2xl leading-relaxed',
    '[&_h2]:mt-10 [&_h2]:font-display [&_h2]:text-h3',
    '[&_h3]:mt-8 [&_h3]:font-display [&_h3]:text-lg [&_h3]:font-semibold',
    '[&_p]:mt-5 [&_p:first-child]:mt-0',
    '[&_ul]:mt-5 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mt-5 [&_ol]:list-decimal [&_ol]:pl-6',
    '[&_li]:mt-2',
    '[&_a]:font-semibold [&_a]:underline [&_a]:underline-offset-4',
    '[&_strong]:font-semibold',
    'text-muted [&_h2]:text-content [&_h3]:text-content [&_a]:text-primary' => ! $isDark,
    'text-white/70 [&_h2]:text-white [&_h3]:text-white [&_a]:text-accent' => $isDark,
]) }}>
    {{ $slot }}
</div>

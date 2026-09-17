@props([
    'name',
    'label',
    'type' => 'text',
    'rows' => null,
    'required' => false,
    'hint' => null,
])

@php
    $id = 'field-' . $name;
    $hintId = $hint ? $id . '-hint' : null;
    $errorId = $id . '-error';

    $control = 'w-full rounded-xl border bg-paper px-4 py-3 text-content placeholder:text-muted/60'
        . ' transition-colors duration-200 focus:outline-2 focus:outline-offset-2 focus:outline-primary';
@endphp

<div {{ $attributes->class('flex flex-col') }}>
    <label for="{{ $id }}" class="text-sm font-semibold text-content">
        {{ $label }}
        @unless ($required)
            <span class="ml-1 font-normal text-muted">{{ __('(optional)') }}</span>
        @endunless
    </label>

    @if ($hint)
        <p id="{{ $hintId }}" class="mt-1 text-sm text-muted">{{ $hint }}</p>
    @endif

    @if ($rows)
        <textarea
            id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
            @if ($required) required @endif
            @if ($errors->has($name)) aria-invalid="true" @endif
            aria-describedby="{{ trim(($hintId ?? '') . ' ' . ($errors->has($name) ? $errorId : '')) ?: null }}"
            class="mt-3 {{ $control }} {{ $errors->has($name) ? 'border-accent-text' : 'border-content/20' }}"
        >{{ old($name) }}</textarea>
    @else
        <input
            id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}"
            @if ($required) required @endif
            @if ($errors->has($name)) aria-invalid="true" @endif
            aria-describedby="{{ trim(($hintId ?? '') . ' ' . ($errors->has($name) ? $errorId : '')) ?: null }}"
            class="mt-3 {{ $control }} {{ $errors->has($name) ? 'border-accent-text' : 'border-content/20' }}"
        >
    @endif

    @error($name)
        <p id="{{ $errorId }}" class="mt-2 text-sm font-medium text-accent-text">{{ $message }}</p>
    @enderror
</div>

@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-primary text-start text-base font-medium text-primary bg-primary/5 focus:outline-none focus:text-primary-dark focus:bg-primary/10 focus:border-primary-dark transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-foreground-muted hover:text-foreground hover:bg-surface-muted hover:border-border-strong focus:outline-none focus:text-foreground focus:bg-surface-muted focus:border-border-strong transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

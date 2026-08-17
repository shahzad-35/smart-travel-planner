@props(['size' => 'sm'])

@php
// Literal classes so Tailwind's JIT scanner picks them up.
$sizes = [
    'xs' => 'h-3.5 w-3.5',
    'sm' => 'h-4 w-4',
    'md' => 'h-5 w-5',
    'lg' => 'h-6 w-6',
];
@endphp

<svg {{ $attributes->merge(['class' => 'animate-spin ' . ($sizes[$size] ?? $sizes['sm'])]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
</svg>

@props(['variant' => 'info'])

@php
$styles = [
    'success' => 'bg-primary/10 border-primary/20 text-primary',
    'error' => 'bg-destructive/10 border-destructive/20 text-destructive',
    'info' => 'bg-secondary/10 border-secondary/20 text-secondary',
];
$icons = [
    'success' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'error' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    'info' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-2.5 px-4 py-3 rounded-xl border font-medium text-sm {$styles[$variant]}"]) }} role="alert">
    <svg class="w-5 h-5 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$variant] }}"/>
    </svg>
    <div class="min-w-0">{{ $slot }}</div>
</div>

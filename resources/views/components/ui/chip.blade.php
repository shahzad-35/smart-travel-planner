@props(['color' => 'primary'])

@php
$colors = [
    'primary' => 'bg-primary/10 text-primary',
    'secondary' => 'bg-secondary/10 text-secondary',
    'accent' => 'bg-accent/10 text-accent',
    'neutral' => 'bg-surface-muted text-foreground-muted',
    'destructive' => 'bg-destructive/10 text-destructive',
];
@endphp

<span {{ $attributes->merge(['class' => "chip {$colors[$color]}"]) }}>{{ $slot }}</span>

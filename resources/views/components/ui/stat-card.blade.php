@props(['label', 'value', 'hint' => null, 'color' => 'primary'])

@php
$tints = [
    'primary' => 'from-primary/15 via-primary/5 border-primary/15',
    'secondary' => 'from-secondary/15 via-secondary/5 border-secondary/15',
    'accent' => 'from-accent/15 via-accent/5 border-accent/15',
    'neutral' => 'from-foreground/10 via-foreground/[0.03] border-border',
];
$values = [
    'primary' => 'text-primary',
    'secondary' => 'text-secondary',
    'accent' => 'text-accent',
    'neutral' => 'text-foreground',
];
$iconTints = [
    'primary' => 'text-primary/10',
    'secondary' => 'text-secondary/10',
    'accent' => 'text-accent/10',
    'neutral' => 'text-foreground/5',
];
@endphp

<div {{ $attributes->merge(['class' => "relative overflow-hidden p-5 rounded-xl bg-gradient-to-br to-transparent border {$tints[$color]}"]) }}>
    @isset($icon)
        <span class="absolute -right-3 -bottom-3 w-16 h-16 {{ $iconTints[$color] }}" aria-hidden="true">{{ $icon }}</span>
    @endisset
    <h3 class="text-foreground-muted text-sm font-medium">{{ $label }}</h3>
    <p class="mt-1 text-3xl font-extrabold tabular-nums truncate {{ $values[$color] }}" @isset($title) title="{{ $title }}" @endisset>
        @if(is_numeric($value))
            <span data-countup="{{ $value }}">{{ number_format($value) }}</span>
        @else
            {{ $value }}
        @endif
    </p>
    @if($hint)
        <div class="text-xs text-foreground-subtle mt-1.5">{{ $hint }}</div>
    @endif
</div>

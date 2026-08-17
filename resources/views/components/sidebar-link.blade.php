@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
    ? 'group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold bg-primary/10 text-primary transition-colors'
    : 'group relative flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-foreground-muted hover:text-foreground hover:bg-surface-muted transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} wire:navigate @if($active) aria-current="page" @endif>
    @if($active)
        <span class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-5 rounded-r-full bg-primary" aria-hidden="true"></span>
    @endif
    @if($icon)
        <span class="shrink-0 {{ $active ? 'text-primary' : 'text-foreground-subtle group-hover:text-foreground-muted' }} transition-colors">{{ $icon }}</span>
    @endif
    <span class="truncate">{{ $slot }}</span>
</a>

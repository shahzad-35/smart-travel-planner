@props(['show' => false, 'title' => null, 'maxWidth' => 'md'])

@php
$widths = ['sm' => 'max-w-sm', 'md' => 'max-w-md', 'lg' => 'max-w-lg', 'xl' => 'max-w-xl'];
@endphp

@if($show)
<div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true" @if($title) aria-label="{{ $title }}" @endif>
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm ui-modal-overlay" aria-hidden="true"></div>

    <!-- Panel -->
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div {{ $attributes->merge(['class' => "relative w-full {$widths[$maxWidth]} bg-surface-card border border-border rounded-2xl shadow-card-hover p-6 ui-modal-panel"]) }}>
            @isset($icon)
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-accent/10 text-accent mb-4" aria-hidden="true">
                    {{ $icon }}
                </div>
            @endisset
            @if($title)
                <h3 class="text-lg font-bold text-foreground text-center mb-2">{{ $title }}</h3>
            @endif
            {{ $slot }}
            @isset($footer)
                <div class="mt-6 flex items-center justify-center gap-3">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
@endif

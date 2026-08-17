@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'aurora-bg rounded-xl text-center py-12 px-6']) }}>
    @isset($icon)
        <div class="w-16 h-16 mx-auto mb-5 bg-primary/10 rounded-2xl flex items-center justify-center text-primary" aria-hidden="true">
            {{ $icon }}
        </div>
    @endisset
    <h3 class="text-lg font-bold text-foreground mb-1.5">{{ $title }}</h3>
    @if($description)
        <p class="text-foreground-muted mb-6 max-w-sm mx-auto">{{ $description }}</p>
    @endif
    {{ $slot }}
</div>

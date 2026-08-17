@props(['title', 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="page-title {{ $subtitle ? 'mb-1.5' : '' }}">{{ $title }}</h1>
            @if($subtitle)
                <p class="text-foreground-muted">{{ $subtitle }}</p>
            @endif
        </div>
        @if(trim($slot))
            <div class="flex items-center gap-3">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>

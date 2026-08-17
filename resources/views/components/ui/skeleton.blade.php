@props(['variant' => 'card', 'count' => 1])

@for($i = 0; $i < $count; $i++)
    @if($variant === 'card')
        <div {{ $attributes->merge(['class' => 'bg-surface-card rounded-xl border border-border p-5 animate-pulse']) }} aria-hidden="true">
            <div class="flex items-start gap-4">
                <div class="w-12 h-8 bg-surface-muted dark:bg-surface rounded-lg"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-surface-muted dark:bg-surface rounded w-3/4"></div>
                    <div class="h-3 bg-surface-muted dark:bg-surface rounded w-1/2"></div>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <div class="h-3 bg-surface-muted dark:bg-surface rounded w-full"></div>
                <div class="h-3 bg-surface-muted dark:bg-surface rounded w-4/5"></div>
            </div>
        </div>
    @elseif($variant === 'row')
        <div {{ $attributes->merge(['class' => 'flex items-center gap-4 p-4 bg-surface-card rounded-xl border border-border animate-pulse']) }} aria-hidden="true">
            <div class="w-10 h-10 bg-surface-muted dark:bg-surface rounded-full shrink-0"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-surface-muted dark:bg-surface rounded w-1/3"></div>
                <div class="h-3 bg-surface-muted dark:bg-surface rounded w-1/2"></div>
            </div>
        </div>
    @elseif($variant === 'stats')
        <div {{ $attributes->merge(['class' => 'card p-6 animate-pulse']) }} aria-hidden="true">
            <div class="h-6 bg-surface-muted dark:bg-surface rounded w-44 mb-6"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                @for($j = 0; $j < 4; $j++)
                    <div class="h-28 bg-surface-muted dark:bg-surface rounded-xl"></div>
                @endfor
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="h-64 bg-surface-muted dark:bg-surface rounded-xl"></div>
                <div class="h-64 bg-surface-muted dark:bg-surface rounded-xl"></div>
            </div>
        </div>
    @else
        <div {{ $attributes->merge(['class' => 'h-24 bg-surface-muted dark:bg-surface rounded-xl animate-pulse']) }} aria-hidden="true"></div>
    @endif
@endfor

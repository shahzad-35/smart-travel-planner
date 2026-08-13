<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trip to {{ $trip->destination }} — Smart Travel Planner</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-surface min-h-screen antialiased">
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="mb-6 flex items-center justify-between">
            <span class="text-lg font-bold text-foreground">Smart Travel Planner</span>
            <span class="text-xs px-2 py-1 rounded-full bg-surface-muted text-foreground-muted">Shared trip · read only</span>
        </div>

        <div class="bg-surface-card rounded-2xl border border-border shadow-card p-8">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-foreground mb-1">Trip to {{ $trip->destination }}</h1>
                    <p class="text-foreground-muted">
                        {{ $trip->start_date->format('M j, Y') }} – {{ $trip->end_date->format('M j, Y') }}
                        · {{ $trip->start_date->diffInDays($trip->end_date) + 1 }} days
                    </p>
                </div>
                @if($trip->country_code)
                    <img src="https://flagcdn.com/{{ strtolower($trip->country_code) }}.svg"
                        alt="{{ $trip->country_code }} flag" class="w-14 h-10 object-cover rounded shadow-sm">
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-surface-muted rounded-lg p-4">
                    <div class="text-xs text-foreground-subtle uppercase tracking-wide mb-1">Type</div>
                    <div class="font-semibold text-foreground capitalize">{{ $trip->type ?? '—' }}</div>
                </div>
                <div class="bg-surface-muted rounded-lg p-4">
                    <div class="text-xs text-foreground-subtle uppercase tracking-wide mb-1">Status</div>
                    <div class="font-semibold text-foreground capitalize">{{ $trip->status ?? '—' }}</div>
                </div>
                <div class="bg-surface-muted rounded-lg p-4">
                    <div class="text-xs text-foreground-subtle uppercase tracking-wide mb-1">Travelers</div>
                    <div class="font-semibold text-foreground">{{ $trip->travelers ?? 1 }}</div>
                </div>
                <div class="bg-surface-muted rounded-lg p-4">
                    <div class="text-xs text-foreground-subtle uppercase tracking-wide mb-1">Packing</div>
                    <div class="font-semibold text-foreground">{{ $packingProgress }}%</div>
                </div>
            </div>

            @if($trip->notes)
                <div class="mb-2">
                    <h2 class="text-sm font-semibold text-foreground-subtle uppercase tracking-wide mb-2">Notes</h2>
                    <p class="text-foreground whitespace-pre-line">{{ $trip->notes }}</p>
                </div>
            @endif
        </div>

        <p class="mt-6 text-center text-xs text-foreground-subtle">
            This link shows a read-only summary and expires automatically.
        </p>
    </div>
</body>
</html>

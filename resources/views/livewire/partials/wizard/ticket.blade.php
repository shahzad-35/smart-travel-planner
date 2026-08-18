{{-- Live trip ticket: fills in as the wizard progresses. Pure presentation —
     reads the wizard component's public properties, binds nothing. --}}
@php
    $ticketDuration = null;
    if ($startDate && $endDate && strtotime($startDate) !== false && strtotime($endDate) !== false) {
        $ticketDuration = (new DateTime($startDate))->diff(new DateTime($endDate))->days + 1;
    }
@endphp
<div class="card overflow-hidden p-0">
    <!-- Cover -->
    <div class="relative h-36 overflow-hidden">
        @if($countryCode)
            <img src="https://flagcdn.com/w320/{{ strtolower($countryCode) }}.png" alt="" aria-hidden="true"
                class="absolute inset-0 w-full h-full object-cover scale-105 brightness-[0.7] saturate-[1.1]" loading="lazy">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/15 to-transparent" aria-hidden="true"></div>
            <div class="absolute bottom-3 left-4 right-4 flex items-center gap-2.5">
                <img src="https://flagcdn.com/{{ strtolower($countryCode) }}.svg" alt="{{ strtoupper($countryCode) }} flag"
                    class="w-9 h-6 rounded object-cover shadow ring-1 ring-white/30 shrink-0">
                <div class="min-w-0">
                    <div class="text-white font-bold leading-tight truncate">{{ $destination }}</div>
                    <div class="text-white/70 text-[11px] font-bold tracking-widest">{{ strtoupper($countryCode) }}</div>
                </div>
            </div>
        @else
            <div class="absolute inset-0 aurora-bg" aria-hidden="true"></div>
            <div class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-center px-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18zm0 0l4-9-9 4 5 5z"/>
                    </svg>
                </div>
                <p class="text-sm font-semibold text-foreground">Your next adventure</p>
                <p class="text-xs text-foreground-muted">Pick a destination to start the ticket</p>
            </div>
        @endif
    </div>

    <div class="ticket-divider" aria-hidden="true"></div>

    <!-- Details -->
    <div class="p-5 space-y-3.5">
        <p class="eyebrow text-foreground-subtle">Trip ticket</p>

        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-foreground-muted">Dates</span>
            @if($startDate && $endDate)
                <span class="text-sm font-semibold text-foreground tabular-nums text-right fade-up">
                    {{ date('M j', strtotime($startDate)) }} – {{ date('M j, Y', strtotime($endDate)) }}
                </span>
            @else
                <span class="text-sm text-foreground-subtle">—</span>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-foreground-muted">Duration</span>
            @if($ticketDuration)
                <x-ui.chip color="primary" class="fade-up tabular-nums">{{ $ticketDuration }} {{ $ticketDuration === 1 ? 'day' : 'days' }}</x-ui.chip>
            @else
                <span class="text-sm text-foreground-subtle">—</span>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-foreground-muted">Type</span>
            @if($type)
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-foreground capitalize fade-up">
                    <span class="text-primary">@include('livewire.partials.wizard.type-icon', ['typeKey' => $type, 'class' => 'w-4 h-4'])</span>
                    {{ $tripTypes[$type]['label'] ?? $type }}
                </span>
            @else
                <span class="text-sm text-foreground-subtle">—</span>
            @endif
        </div>

        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-foreground-muted">Travelers</span>
            <span class="text-sm font-semibold text-foreground tabular-nums">{{ $travelers }} {{ $travelers === 1 ? 'person' : 'people' }}</span>
        </div>

        <div class="flex items-center justify-between gap-3">
            <span class="text-sm text-foreground-muted">Budget</span>
            @if($budget)
                <span class="text-sm font-semibold text-foreground tabular-nums fade-up">PKR {{ number_format((float) $budget, 2) }}</span>
            @else
                <span class="text-sm text-foreground-subtle">—</span>
            @endif
        </div>

        @if($showWeatherPreview && !empty($weatherPreview))
            <div class="flex items-center justify-between gap-3 pt-3 border-t border-border fade-up">
                <span class="text-sm text-foreground-muted">Weather now</span>
                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-foreground">
                    @if(!empty($weatherPreview['icon']))
                        <img src="https://openweathermap.org/img/wn/{{ $weatherPreview['icon'] }}.png" alt="" aria-hidden="true" class="w-7 h-7 -my-1">
                    @endif
                    <span class="tabular-nums">{{ round($weatherPreview['temperature'] ?? 0) }}°C</span>
                    <span class="text-foreground-muted font-normal capitalize hidden xl:inline">{{ $weatherPreview['condition'] ?? '' }}</span>
                </span>
            </div>
        @endif

        @if($notes)
            <div class="pt-3 border-t border-border fade-up">
                <p class="eyebrow text-foreground-subtle mb-1">Notes</p>
                <p class="text-sm text-foreground-muted">{{ \Illuminate\Support\Str::limit($notes, 90) }}</p>
            </div>
        @endif
    </div>
</div>

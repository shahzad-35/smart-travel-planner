<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-end gap-3">
        <div>
            <label class="block text-sm text-foreground-muted">Start date</label>
            <input type="date" wire:model.live.debounce.300ms="startDate" class="mt-1 border border-border bg-surface-card text-foreground rounded px-2 py-1 focus:ring-primary focus:border-primary"/>
        </div>
        <div>
            <label class="block text-sm text-foreground-muted">End date</label>
            <input type="date" wire:model.live.debounce.300ms="endDate" class="mt-1 border border-border bg-surface-card text-foreground rounded px-2 py-1 focus:ring-primary focus:border-primary"/>
        </div>
        <div class="md:ml-auto">
            <a href="{{ route('calendar.holidays.range', ['code' => $code, 'start' => $startDate, 'end' => $endDate]) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-border rounded bg-surface-card hover:bg-surface-muted text-foreground">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8" /></svg>
                <span>Add to calendar (ICS)</span>
            </a>
        </div>
    </div>

    <ol class="relative border-s border-border">
        @forelse($holidays as $h)
            @php $inTrip = $startDate && $endDate && $h['date'] >= $startDate && $h['date'] <= $endDate; @endphp
            <li class="mb-6 ms-6">
                <span class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $inTrip ? 'bg-emerald-500 dark:bg-emerald-400' : 'bg-primary' }} ring-8 ring-surface-card"></span>
                <div class="p-4 card">
                    <div class="flex justify-between items-center">
                        <div class="font-semibold text-foreground">{{ $h['name'] }}</div>
                        <a class="text-sm text-primary hover:text-primary-dark hover:underline" href="{{ route('calendar.holidays.single', ['code' => $code, 'date' => $h['date'], 'name' => $h['name']]) }}">Add event</a>
                    </div>
                    <div class="text-sm text-foreground-muted mt-1">{{ $h['date'] }} • {{ $h['type'] }}</div>
                    @if($h['description'])
                        <div class="text-sm text-foreground mt-2">{{ $h['description'] }}</div>
                    @endif
                    @if($inTrip)
                        <div class="mt-2 inline-flex items-center gap-1 text-xs text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded px-2 py-0.5">In your trip dates</div>
                    @endif
                </div>
            </li>
        @empty
            <li class="ms-6 text-foreground-muted">No holidays found for this range.</li>
        @endforelse
    </ol>
</div>

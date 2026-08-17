<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-end gap-3">
        <div>
            <label class="block text-sm font-semibold text-foreground mb-0.5">Start date</label>
            <input type="date" wire:model.live.debounce.300ms="startDate" class="field mt-1 px-3 py-2 w-auto"/>
        </div>
        <div>
            <label class="block text-sm font-semibold text-foreground mb-0.5">End date</label>
            <input type="date" wire:model.live.debounce.300ms="endDate" class="field mt-1 px-3 py-2 w-auto"/>
        </div>
        <div class="md:ml-auto">
            <a href="{{ route('calendar.holidays.range', ['code' => $code, 'start' => $startDate, 'end' => $endDate]) }}" class="btn-ghost px-3.5 py-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8" /></svg>
                <span>Add to calendar (ICS)</span>
            </a>
        </div>
    </div>

    <div wire:loading.delay.block wire:target="startDate, endDate" class="space-y-3" aria-hidden="true">
        <x-ui.skeleton variant="row" :count="3" />
    </div>

    <ol wire:loading.remove.delay wire:target="startDate, endDate" class="relative border-s border-border">
        @forelse($holidays as $h)
            @php $inTrip = $startDate && $endDate && $h['date'] >= $startDate && $h['date'] <= $endDate; @endphp
            <li class="mb-6 ms-6">
                <span class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $inTrip ? 'bg-accent' : 'bg-primary' }} ring-8 ring-surface-card"></span>
                <div class="p-4 card">
                    <div class="flex justify-between items-center">
                        <div class="font-bold text-foreground">{{ $h['name'] }}</div>
                        <a class="text-sm text-primary hover:text-primary-dark hover:underline" href="{{ route('calendar.holidays.single', ['code' => $code, 'date' => $h['date'], 'name' => $h['name']]) }}">Add event</a>
                    </div>
                    <div class="text-sm text-foreground-muted mt-1">{{ $h['date'] }} • {{ $h['type'] }}</div>
                    @if($h['description'])
                        <div class="text-sm text-foreground mt-2">{{ $h['description'] }}</div>
                    @endif
                    @if($inTrip)
                        <div class="chip mt-2 bg-accent/10 text-accent">In your trip dates</div>
                    @endif
                </div>
            </li>
        @empty
            <li class="ms-6 text-foreground-muted">No holidays found for this range.</li>
        @endforelse
    </ol>
</div>

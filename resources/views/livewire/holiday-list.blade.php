<div class="space-y-4">
    <div class="flex flex-col md:flex-row md:items-end gap-3">
        <div>
            <label class="block text-sm text-gray-600">Start date</label>
            <input type="date" wire:model.debounce.300ms="startDate" class="mt-1 border rounded px-2 py-1"/>
        </div>
        <div>
            <label class="block text-sm text-gray-600">End date</label>
            <input type="date" wire:model.debounce.300ms="endDate" class="mt-1 border rounded px-2 py-1"/>
        </div>
        <div class="md:ml-auto">
            <a href="{{ route('calendar.holidays.range', ['code' => $code, 'start' => $startDate, 'end' => $endDate]) }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded bg-white hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v8m4-4H8" /></svg>
                <span>Add to calendar (ICS)</span>
            </a>
        </div>
    </div>

    @php
        $tripStart = $tripId ? \App\Models\Trip::find($tripId)?->start_date?->format('Y-m-d') : null;
        $tripEnd = $tripId ? \App\Models\Trip::find($tripId)?->end_date?->format('Y-m-d') : null;
    @endphp

    <ol class="relative border-s border-gray-200">
        @forelse($holidays as $h)
            @php $inTrip = $tripStart && $tripEnd && $h['date'] >= $tripStart && $h['date'] <= $tripEnd; @endphp
            <li class="mb-6 ms-6">
                <span class="absolute -start-3 flex h-6 w-6 items-center justify-center rounded-full {{ $inTrip ? 'bg-green-500' : 'bg-blue-500' }} ring-8 ring-white"></span>
                <div class="p-4 bg-white border rounded shadow-sm">
                    <div class="flex justify-between items-center">
                        <div class="font-semibold">{{ $h['name'] }}</div>
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('calendar.holidays.single', ['code' => $code, 'date' => $h['date'], 'name' => $h['name']]) }}">Add event</a>
                    </div>
                    <div class="text-sm text-gray-600 mt-1">{{ $h['date'] }} • {{ $h['type'] }}</div>
                    @if($h['description'])
                        <div class="text-sm mt-2">{{ $h['description'] }}</div>
                    @endif
                    @if($inTrip)
                        <div class="mt-2 inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 border border-green-200 rounded px-2 py-0.5">In your trip dates</div>
                    @endif
                </div>
            </li>
        @empty
            <li class="ms-6 text-gray-600">No holidays found for this range.</li>
        @endforelse
    </ol>
</div>



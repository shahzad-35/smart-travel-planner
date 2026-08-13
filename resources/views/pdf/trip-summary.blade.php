<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Trip Report - {{ $trip->destination }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 13px; }
        h1 { color: #1a202c; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 5px; }
        h2 { font-size: 16px; margin-top: 22px; color: #4a5568; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; }
        .subtitle { color: #718096; margin-top: 0; }
        .stats-grid { width: 100%; margin-bottom: 10px; }
        .stat-card { float: left; width: 31%; background: #f7fafc; padding: 12px; margin: 0 2% 10px 0; border-radius: 5px; box-sizing: border-box; }
        .clear { clear: both; }
        .stat-value { font-size: 18px; font-weight: bold; color: #2d3748; }
        .stat-label { font-size: 12px; color: #718096; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; font-size: 12px; }
        th { background-color: #edf2f7; }
        .total-row td { font-weight: bold; background: #f7fafc; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 11px; color: #a0aec0; }
        .notes { background: #f7fafc; padding: 12px; border-radius: 5px; }
        .packed { color: #2f855a; font-weight: bold; }
        .unpacked { color: #a0aec0; }
        .muted { color: #718096; }
    </style>
</head>
<body>
    <h1>Trip to {{ $trip->destination }}@if($trip->country_code) ({{ $trip->country_code }})@endif</h1>
    <p class="subtitle">
        {{ $trip->start_date->format('F j, Y') }} &ndash; {{ $trip->end_date->format('F j, Y') }}
        ({{ $trip->start_date->diffInDays($trip->end_date) + 1 }} days)
        &middot; Planned by {{ $trip->user->name }}
    </p>

    <h2>Overview</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Type</div>
            <div class="stat-value">{{ ucfirst($trip->type ?? '—') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Status</div>
            <div class="stat-value">{{ ucfirst($trip->status ?? '—') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Travelers</div>
            <div class="stat-value">{{ $trip->travelers ?? 1 }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Budget</div>
            <div class="stat-value">${{ number_format($trip->budget ?? 0, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Spent ({{ $budgetUsed }}% of budget)</div>
            <div class="stat-value">${{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Packing Progress</div>
            <div class="stat-value">{{ $packingProgress }}%</div>
        </div>
        <div class="clear"></div>
    </div>

    @if($trip->notes || $trip->tripNotes->isNotEmpty())
        <h2>Notes</h2>
        @if($trip->notes)
            <div class="notes">{{ $trip->notes }}</div>
        @endif
        @foreach($trip->tripNotes as $note)
            <div class="notes" style="margin-top: 6px;">{{ $note->note }}</div>
        @endforeach
    @endif

    <h2>Expenses</h2>
    @if($trip->expenses->isNotEmpty())
        <table>
            <thead>
                <tr><th>Date</th><th>Category</th><th>Description</th><th>Amount</th></tr>
            </thead>
            <tbody>
                @foreach($trip->expenses->sortBy('expense_date') as $expense)
                    <tr>
                        <td>{{ $expense->expense_date ? \Carbon\Carbon::parse($expense->expense_date)->format('M j, Y') : '—' }}</td>
                        <td>{{ ucfirst($expense->category) }}</td>
                        <td>{{ $expense->description ?? '—' }}</td>
                        <td>{{ $expense->currency ?? 'USD' }} {{ number_format($expense->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">Total</td>
                    <td>${{ number_format($totalExpenses, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="muted">No expenses recorded.</p>
    @endif

    <h2>Packing Checklist ({{ $packingProgress }}% packed)</h2>
    @if($trip->packingItems->isNotEmpty())
        <table>
            <thead>
                <tr><th>Category</th><th>Item</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($trip->packingItems->sortBy([['category', 'asc'], ['order', 'asc']]) as $item)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                        <td>{{ $item->item }}</td>
                        <td>
                            @if($item->is_packed)
                                <span class="packed">Packed</span>
                            @else
                                <span class="unpacked">Not packed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="muted">No packing items yet.</p>
    @endif

    @if(count($weatherForecast) > 0)
        <h2>Weather Forecast</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Condition</th><th>Temperature</th><th>High / Low</th></tr>
            </thead>
            <tbody>
                @foreach($weatherForecast as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('D, M j') }}</td>
                        <td>{{ ucfirst($day['condition'] ?? '—') }}</td>
                        <td>{{ round($day['temperature'] ?? 0) }}&deg;</td>
                        <td>{{ round($day['max_temp'] ?? $day['temperature'] ?? 0) }}&deg; / {{ round($day['min_temp'] ?? $day['temperature'] ?? 0) }}&deg;</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(count($holidays) > 0)
        <h2>Public Holidays During Trip</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Holiday</th><th>Type</th></tr>
            </thead>
            <tbody>
                @foreach($holidays as $holiday)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($holiday['date'])->format('D, M j') }}</td>
                        <td>{{ $holiday['name'] }}</td>
                        <td>{{ $holiday['type'] ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($trip->statusHistories->isNotEmpty())
        <h2>Status History</h2>
        <table>
            <thead>
                <tr><th>Date</th><th>Change</th><th>Reason</th></tr>
            </thead>
            <tbody>
                @foreach($trip->statusHistories->sortByDesc('created_at') as $history)
                    <tr>
                        <td>{{ $history->created_at->format('M j, Y H:i') }}</td>
                        <td>{{ $history->old_status ? ucfirst($history->old_status) . ' → ' : '' }}{{ ucfirst($history->new_status) }}</td>
                        <td>{{ $history->reason ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Generated by Smart Travel Planner on {{ now()->format('F j, Y') }}
    </div>
</body>
</html>

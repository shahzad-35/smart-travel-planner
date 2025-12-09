<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packing Checklist - {{ $trip->destination }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .trip-info {
            margin-bottom: 20px;
        }
        .progress {
            background-color: #f0f0f0;
            border-radius: 10px;
            padding: 10px;
            margin: 20px 0;
        }
        .progress-bar {
            background-color: #10b981;
            height: 20px;
            border-radius: 10px;
            text-align: center;
            color: white;
            font-weight: bold;
        }
        .category {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .category-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1f2937;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .item {
            margin-bottom: 8px;
            padding-left: 20px;
        }
        .checkbox {
            display: inline-block;
            width: 15px;
            height: 15px;
            border: 2px solid #6b7280;
            margin-right: 10px;
            vertical-align: middle;
        }
        .checkbox.checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        .checkbox.checked::after {
            content: '✓';
            color: white;
            font-weight: bold;
            margin-left: 2px;
        }
        .item-text {
            vertical-align: middle;
        }
        .item-text.checked {
            text-decoration: line-through;
            color: #9ca3af;
        }
        .custom-badge {
            background-color: #3b82f6;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Packing Checklist</h1>
        <h2>{{ $trip->destination }}</h2>
        <div class="trip-info">
            <p><strong>Trip Type:</strong> {{ ucfirst($trip->type) }}</p>
            <p><strong>Duration:</strong> {{ $trip->start_date->format('M j, Y') }} - {{ $trip->end_date->format('M j, Y') }}</p>
            <p><strong>Country:</strong> {{ $trip->country_code }}</p>
        </div>
    </div>

    @if($packingProgress > 0)
        <div class="progress">
            <strong>Progress: {{ $packingProgress }}% Complete</strong>
            <div class="progress-bar" style="width: {{ $packingProgress }}%;">
                {{ $packingProgress }}%
            </div>
        </div>
    @endif

    @foreach($packingItems as $category => $items)
        <div class="category">
            <div class="category-title">
                {{ $category == 'trip_type' ? ucfirst($trip->type) : ucfirst($category) }}
                <small style="font-weight: normal; color: #6b7280;">({{ count($items) }} items)</small>
            </div>
            
            @foreach($items as $item)
                <div class="item">
                    <span class="checkbox {{ $item['is_packed'] ? 'checked' : '' }}"></span>
                    <span class="item-text {{ $item['is_packed'] ? 'checked' : '' }}">
                        {{ $item['item'] }}
                        @if($item['is_custom'])
                            <span class="custom-badge">Custom</span>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    @endforeach

    <div class="footer">
        <p>Generated on {{ now()->format('M j, Y \a\t g:i A') }}</p>
        <p>Smart Travel Planner</p>
    </div>

    <script>
        // Auto-print functionality (can be called externally)
        function printChecklist() {
            window.print();
        }
    </script>
</body>
</html>

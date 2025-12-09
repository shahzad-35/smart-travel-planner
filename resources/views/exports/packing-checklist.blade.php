<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Packing Checklist - {{ $trip->destination }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #2563eb;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        .progress-section {
            margin: 20px 0;
            text-align: center;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background-color: #10b981;
            width: {{ $progress }}%;
        }
        .category {
            margin: 15px 0;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            overflow: hidden;
        }
        .category-header {
            background-color: #f3f4f6;
            padding: 8px 12px;
            border-bottom: 1px solid #d1d5db;
            font-weight: bold;
            font-size: 13px;
        }
        .item {
            display: flex;
            align-items: center;
            padding: 6px 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .item:last-child {
            border-bottom: none;
        }
        .checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid #d1d5db;
            border-radius: 3px;
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
        }
        .checkbox.checked {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .check-mark {
            color: white;
            font-size: 10px;
            font-weight: bold;
        }
        .item-text {
            flex: 1;
            font-size: 11px;
        }
        .item-text.packed {
            text-decoration: line-through;
            color: #9ca3af;
        }
        .custom-badge {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            font-size: 9px;
            padding: 1px 4px;
            border-radius: 3px;
            margin-left: 6px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Packing Checklist</h1>
        <p><strong>{{ $trip->destination }}</strong></p>
        <p>{{ $trip->start_date->format('M j, Y') }} - {{ $trip->end_date->format('M j, Y') }}</p>
        <p>{{ $trip->type }} trip to {{ $trip->country_code }}</p>
    </div>

    <div class="progress-section">
        <strong>{{ $progress }}% Complete</strong>
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
    </div>

    @foreach($packingItems as $category => $items)
        <div class="category">
            <div class="category-header">
                {{ $category == 'trip_type' ? ucfirst($trip->type) : ucfirst($category) }}
                ({{ count($items) }} items)
            </div>
            @foreach($items as $item)
                <div class="item">
                    <div class="checkbox {{ $item['is_packed'] ? 'checked' : '' }}">
                        @if($item['is_packed'])
                            <span class="check-mark">✓</span>
                        @endif
                    </div>
                    <span class="item-text {{ $item['is_packed'] ? 'packed' : '' }}">
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
        Generated on {{ now()->format('M j, Y \a\t g:i A') }} |
        Smart Travel Planner
    </div>
</body>
</html>

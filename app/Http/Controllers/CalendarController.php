<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\External\HolidayService;

class CalendarController extends Controller
{
    public function holidaySingle(Request $request)
    {
        $request->validate([
            'code' => ['required','string'],
            'date' => ['required','date'],
            'name' => ['required','string'],
        ]);

        $summary = $request->string('name');
        $date = $request->date('date');
        $uid = Str::uuid()->toString();

        $ics = $this->renderIcs([
            [
                'uid' => $uid,
                'summary' => $summary,
                'dtstart' => $date->format('Ymd'),
                'dtend' => $date->copy()->addDay()->format('Ymd'),
                'allday' => true,
            ],
        ]);

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="holiday.ics"',
        ]);
    }

    public function holidayRange(Request $request)
    {
        $request->validate([
            'code' => ['required','string'],
            'start' => ['required','date'],
            'end' => ['required','date','after_or_equal:start'],
        ]);

        $start = $request->date('start');
        $end = $request->date('end');

        $holidayService = app(HolidayService::class);
        $holidays = $holidayService->getHolidays($request->code, $start->format('Y-m-d'), $end->format('Y-m-d'));

        $events = [];
        foreach ($holidays as $holiday) {
            $date = \Carbon\Carbon::parse($holiday->date);
            $events[] = [
                'uid' => Str::uuid()->toString(),
                'summary' => $holiday->name,
                'dtstart' => $date->format('Ymd'),
                'dtend' => $date->copy()->addDay()->format('Ymd'),
                'allday' => true,
            ];
        }

        $ics = $this->renderIcs($events);
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="holidays.ics"',
        ]);
    }

    private function renderIcs(array $events): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Smart Travel Planner//EN',
            'CALSCALE:GREGORIAN',
        ];
        foreach ($events as $event) {
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:' . $event['uid'];
            $lines[] = 'DTSTAMP:' . now()->utc()->format('Ymd\THis\Z');
            $lines[] = 'DTSTART;VALUE=DATE:' . $event['dtstart'];
            $lines[] = 'DTEND;VALUE=DATE:' . $event['dtend'];
            $lines[] = 'SUMMARY:' . $this->escapeText($event['summary']);
            $lines[] = 'END:VEVENT';
        }
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function escapeText(string $text): string
    {
        return str_replace(['\\', ',', ';', "\n"], ['\\\\', '\\,', '\\;', '\\n'], $text);
    }
}



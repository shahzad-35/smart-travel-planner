<?php

namespace Tests\Unit;

use App\DTOs\HolidayDTO;
use App\Services\External\HolidayService;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HolidayServiceTest extends TestCase
{

    private HolidayService $holidayService;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the API key config
        config(['services.calendarific.api_key' => 'test_api_key']);

        $cacheService = app(CacheService::class);
        $this->holidayService = new HolidayService($cacheService);
    }

    public function testGetHolidaysFromCalendarificSuccess()
    {
        $calendarificResponse = [
            'response' => [
                'holidays' => [
                    [
                        'date' => ['iso' => '2024-03-23'],
                        'name' => 'Pakistan Day',
                        'type' => ['National holiday'],
                        'description' => 'Commemorates the Lahore Resolution of 1940',
                    ],
                    [
                        'date' => ['iso' => '2024-08-14'],
                        'name' => 'Independence Day',
                        'type' => ['National holiday'],
                        'description' => 'Celebrates Pakistan\'s independence from British rule',
                    ],
                    [
                        'date' => ['iso' => '2024-12-25'],
                        'name' => 'Christmas Day',
                        'type' => ['National holiday'],
                        'description' => 'Christmas Day celebration',
                    ],
                ],
            ],
        ];

        Http::fake([
            'calendarific.com/api/v2/holidays*' => Http::response($calendarificResponse, 200),
        ]);

        $holidays = $this->holidayService->getHolidays('PK', '2024-01-01', '2024-12-31');

        // Check that the API log entry was created
        $this->assertDatabaseHas('api_logs', [
            'api_name' => 'HolidayService',
        ]);

        $this->assertIsArray($holidays);
        $this->assertCount(3, $holidays);
        $this->assertInstanceOf(HolidayDTO::class, $holidays[0]);
        $this->assertEquals('2024-03-23', $holidays[0]->date);
        $this->assertEquals('Pakistan Day', $holidays[0]->name);
        $this->assertEquals('National holiday', $holidays[0]->type);
        $this->assertEquals('Commemorates the Lahore Resolution of 1940', $holidays[0]->description);
    }

    public function testGetHolidaysFallbackToNagerDate()
    {
        // Calendarific returns empty or error
        Http::fake([
            'calendarific.com/api/v2/holidays*' => Http::response(null, 500),
            'date.nager.at/api/v3/PublicHolidays/*' => Http::response([
                [
                    'date' => '2024-03-23',
                    'name' => 'Pakistan Day',
                    'localName' => 'یوم پاکستان',
                    'type' => 'Public',
                ],
                [
                    'date' => '2024-08-14',
                    'name' => 'Independence Day',
                    'localName' => 'یوم آزادی',
                    'type' => 'Public',
                ],
                [
                    'date' => '2024-12-25',
                    'name' => 'Christmas Day',
                    'localName' => 'کرسمس',
                    'type' => 'Public',
                ],
            ], 200),
        ]);

        $holidays = $this->holidayService->getHolidays('PK', '2024-01-01', '2024-12-31');

        // Check that the API log entry was created (fallback to Nager.Date)
        $this->assertDatabaseHas('api_logs', [
            'api_name' => 'HolidayService',
        ]);

        $this->assertIsArray($holidays);
        $this->assertCount(3, $holidays);
        $this->assertInstanceOf(HolidayDTO::class, $holidays[0]);
        $this->assertEquals('2024-03-23', $holidays[0]->date);
        $this->assertEquals('Pakistan Day', $holidays[0]->name);
        $this->assertEquals('Public', $holidays[0]->type);
        $this->assertEquals('یوم پاکستان', $holidays[0]->description);
    }

    public function testGetHolidaysReturnsEmptyOnFailure()
    {
        Http::fake([
            'calendarific.com/api/v2/holidays*' => Http::response(null, 500),
            'date.nager.at/api/v3/PublicHolidays/*' => Http::response(null, 500),
        ]);

        $holidays = $this->holidayService->getHolidays('PK', '2024-01-01', '2024-12-31');

        // Check that the API log entry was created
        $this->assertDatabaseHas('api_logs', [
            'api_name' => 'HolidayService',
        ]);

        $this->assertIsArray($holidays);
        $this->assertEmpty($holidays);
    }
}

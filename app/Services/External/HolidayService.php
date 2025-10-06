<?php

namespace App\Services\External;

use App\DTOs\HolidayDTO;
use App\Services\ApiResponseHandler;
use App\Services\BaseService;
use App\Services\CacheService;
use Illuminate\Support\Facades\Http;
use Exception;

class HolidayService extends BaseService
{
    private const CALENDARIFIC_BASE_URL = 'https://calendarific.com/api/v2';
    private const NAGER_DATE_BASE_URL = 'https://date.nager.at/api/v3';
    private const CACHE_TAG = 'holidays';

    private string $calendarificApiKey;

    public function __construct(CacheService $cacheService)
    {
        parent::__construct($cacheService);
        $this->calendarificApiKey = config('services.calendarific.api_key');

        if (!$this->calendarificApiKey) {
            throw new Exception('Calendarific API key not configured');
        }
    }

    /**
     * Get holidays for a country within a date range
     *
     * @param string $countryCode ISO 3166-1 alpha-2 country code
     * @param string $startDate Start date in Y-m-d format
     * @param string $endDate End date in Y-m-d format
     * @return HolidayDTO[]
     */
    public function getHolidays(string $countryCode, string $startDate, string $endDate): array
    {
        $cacheKey = "holidays_{$countryCode}_{$startDate}_{$endDate}";

        return $this->cacheService->remember($cacheKey, self::CACHE_TAG, function () use ($countryCode, $startDate, $endDate) {
            try {
                // Try Calendarific first
                $holidays = $this->fetchFromCalendarific($countryCode, $startDate, $endDate);
                if (!empty($holidays)) {
                    return $holidays;
                }

                // Fallback to Nager.Date
                $this->logInfo("Calendarific API failed or returned no data, falling back to Nager.Date for {$countryCode}");
                return $this->fetchFromNagerDate($countryCode, $startDate, $endDate);
            } catch (\App\Exceptions\ApiException $e) {
                $this->logError("API Exception: " . $e->getMessage());
                return [];
            } catch (Exception $e) {
                $this->logError("Failed to fetch holidays: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Fetch holidays from Calendarific API
     *
     * @param string $countryCode
     * @param string $startDate
     * @param string $endDate
     * @return HolidayDTO[]
     */
    private function fetchFromCalendarific(string $countryCode, string $startDate, string $endDate): array
    {
        $holidays = [];

        $handler = new ApiResponseHandler('HolidayService');

        try {
            // Calendarific API requires year, so we need to get holidays for each year in the range
            $startYear = (int) date('Y', strtotime($startDate));
            $endYear = (int) date('Y', strtotime($endDate));

            for ($year = $startYear; $year <= $endYear; $year++) {
                $response = $handler->execute(function () use ($countryCode, $year) {
                    return \Illuminate\Support\Facades\Http::timeout(10)->get(self::CALENDARIFIC_BASE_URL . '/holidays', [
                        'api_key' => $this->calendarificApiKey,
                        'country' => $countryCode,
                        'year' => $year,
                    ]);
                }, ['country' => $countryCode, 'year' => $year, 'api' => 'calendarific']);

                if ($response->successful()) {
                    $data = $response->json();
                    $yearHolidays = $this->mapCalendarificResponse($data, $startDate, $endDate);
                    $holidays = array_merge($holidays, $yearHolidays);
                } else {
                    $this->logError("Calendarific API error for {$countryCode} {$year}: " . $response->body());
                    return []; // Return empty to trigger fallback
                }
            }
        } catch (\App\Exceptions\ApiException $e) {
            $this->logError("API Exception: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            $this->logError("Calendarific API exception: " . $e->getMessage());
            return [];
        }

        return $holidays;
    }

    /**
     * Fetch holidays from Nager.Date API
     *
     * @param string $countryCode
     * @param string $startDate
     * @param string $endDate
     * @return HolidayDTO[]
     */
    private function fetchFromNagerDate(string $countryCode, string $startDate, string $endDate): array
    {
        $holidays = [];

        $handler = new ApiResponseHandler('HolidayService');

        try {
            // Nager.Date API requires year, so we need to get holidays for each year in the range
            $startYear = (int) date('Y', strtotime($startDate));
            $endYear = (int) date('Y', strtotime($endDate));

            for ($year = $startYear; $year <= $endYear; $year++) {
                $response = $handler->execute(function () use ($countryCode, $year) {
                    return \Illuminate\Support\Facades\Http::timeout(10)->get(self::NAGER_DATE_BASE_URL . "/PublicHolidays/{$year}/{$countryCode}");
                }, ['country' => $countryCode, 'year' => $year, 'api' => 'nager_date']);

                if ($response->successful()) {
                    $data = $response->json();
                    $yearHolidays = $this->mapNagerDateResponse($data, $startDate, $endDate);
                    $holidays = array_merge($holidays, $yearHolidays);
                } else {
                    $this->logError("Nager.Date API error for {$countryCode} {$year}: " . $response->body());
                    return [];
                }
            }
        } catch (\App\Exceptions\ApiException $e) {
            $this->logError("API Exception: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            $this->logError("Nager.Date API exception: " . $e->getMessage());
            return [];
        }

        return $holidays;
    }

    /**
     * Map Calendarific API response to HolidayDTO array, filtered by date range
     *
     * @param array $data
     * @param string $startDate
     * @param string $endDate
     * @return HolidayDTO[]
     */
    private function mapCalendarificResponse(array $data, string $startDate, string $endDate): array
    {
        $holidays = [];

        foreach ($data['response']['holidays'] ?? [] as $holiday) {
            $holidayDate = $holiday['date']['iso'] ?? '';
            if ($holidayDate >= $startDate && $holidayDate <= $endDate) {
                $holidays[] = new HolidayDTO(
                    date: $holidayDate,
                    name: $holiday['name'] ?? '',
                    type: $holiday['type'][0] ?? 'public_holiday',
                    description: $holiday['description'] ?? null
                );
            }
        }

        return $holidays;
    }

    /**
     * Map Nager.Date API response to HolidayDTO array, filtered by date range
     *
     * @param array $data
     * @param string $startDate
     * @param string $endDate
     * @return HolidayDTO[]
     */
    private function mapNagerDateResponse(array $data, string $startDate, string $endDate): array
    {
        $holidays = [];

        foreach ($data as $holiday) {
            $holidayDate = $holiday['date'] ?? '';
            if ($holidayDate >= $startDate && $holidayDate <= $endDate) {
                $holidays[] = new HolidayDTO(
                    date: $holidayDate,
                    name: $holiday['name'] ?? '',
                    type: $holiday['type'] ?? 'Public',
                    description: $holiday['localName'] ?? null
                );
            }
        }

        return $holidays;
    }
}

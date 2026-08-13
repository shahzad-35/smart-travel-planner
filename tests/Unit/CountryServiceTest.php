<?php

namespace Tests\Unit;

use App\DTOs\CountryDTO;
use App\Services\External\CountryService;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CountryServiceTest extends TestCase
{
    use RefreshDatabase;

    private CountryService $countryService;

    protected function setUp(): void
    {
        parent::setUp();

        $cacheService = app(CacheService::class);
        $this->countryService = new CountryService($cacheService);
    }

    public function testGetCountryInfoSuccess()
    {
        // Exercise the live v5 API path: requires a configured (non-placeholder) key
        config(['services.restcountries.api_key' => 'test_api_key']);
        $countryService = new CountryService(app(CacheService::class));

        // v5 response shape: data.objects envelope, names/codes/capitals fields
        $countryData = [
            'names' => ['common' => 'Pakistan'],
            'codes' => ['alpha_2' => 'PK', 'alpha_3' => 'PAK'],
            'capitals' => [['name' => 'Islamabad']],
            'region' => 'Asia',
            'population' => 240000000,
            'currencies' => ['PKR' => ['name' => 'Pakistani rupee']],
            'languages' => ['urd' => 'Urdu', 'eng' => 'English'],
            'timezones' => ['UTC+05:00'],
            'flag' => ['url_svg' => 'https://flagcdn.com/pk.svg'],
            'coordinates' => ['lat' => 30.0, 'lng' => 70.0],
        ];

        Http::fake([
            'api.restcountries.com/countries/v5/name*' => Http::response([
                'data' => ['objects' => [$countryData]],
            ], 200),
        ]);

        $country = $countryService->getCountryInfo('Pakistan');

        $this->assertInstanceOf(CountryDTO::class, $country);
        $this->assertEquals('Pakistan', $country->name);
        $this->assertEquals('PK', $country->code);
        $this->assertEquals('Islamabad', $country->capital);
        $this->assertEquals('Asia', $country->region);
        $this->assertEquals(240000000, $country->population);
        $this->assertEquals('Pakistani rupee', $country->currency);
        // languages are normalized to the [['name' => ...]] shape the views expect
        $this->assertEquals([['name' => 'Urdu'], ['name' => 'English']], $country->languages);
        $this->assertEquals('UTC+05:00', $country->timezone);
        $this->assertEquals('https://flagcdn.com/pk.svg', $country->flag);
        $this->assertEquals(30.0, $country->latitude);
        $this->assertEquals(70.0, $country->longitude);
    }

    public function testGetCountryInfoByCodeSuccess()
    {
        $countryData = [
            'name' => ['common' => 'Pakistan'],
            'cca2' => 'PK',
            'capital' => ['Islamabad'],
            'region' => 'Asia',
            'population' => 240000000,
            'currencies' => ['PKR' => ['name' => 'Pakistani rupee']],
            'languages' => ['urd' => 'Urdu', 'eng' => 'English'],
            'timezones' => ['UTC+05:00'],
            'flags' => ['svg' => 'https://flagcdn.com/pk.svg']
        ];

        Http::fake([
            'restcountries.com/v3.1/alpha/PK*' => Http::response($countryData, 200),
        ]);

        $country = $this->countryService->getCountryInfo('PK');

        $this->assertInstanceOf(CountryDTO::class, $country);
        $this->assertEquals('Pakistan', $country->name);
        $this->assertEquals('PK', $country->code);
    }

    public function testGetCountryInfoApiFailure()
    {
        Http::fake([
            'restcountries.com/v3.1/name/Nowhere*' => Http::response(null, 500),
        ]);

        $country = $this->countryService->getCountryInfo('Nowhere');

        $this->assertNull($country);
    }

    public function testSearchCountriesSuccess()
    {
        $countryData = [
            'name' => ['common' => 'Pakistan'],
            'cca2' => 'PK',
            'capital' => ['Islamabad'],
            'region' => 'Asia',
            'population' => 240000000,
            'currencies' => ['PKR' => ['name' => 'Pakistani rupee']],
            'languages' => ['urd' => 'Urdu', 'eng' => 'English'],
            'timezones' => ['UTC+05:00'],
            'flags' => ['svg' => 'https://flagcdn.com/pk.svg']
        ];

        Http::fake([
            'restcountries.com/v3.1/name/pak*' => Http::response([$countryData], 200),
        ]);

        $countries = $this->countryService->searchCountries('pak');

        $this->assertIsArray($countries);
        $this->assertCount(1, $countries);
        $this->assertInstanceOf(CountryDTO::class, $countries[0]);
        $this->assertEquals('Pakistan', $countries[0]->name);
    }

    public function testSearchCountriesApiFailure()
    {
        Http::fake([
            'restcountries.com/v3.1/name/invalid*' => Http::response(null, 500),
        ]);

        $countries = $this->countryService->searchCountries('invalid');

        $this->assertIsArray($countries);
        $this->assertEmpty($countries);
    }

    public function testIsCountryCode()
    {
        $this->assertTrue($this->countryService->isCountryCode('PK'));
        $this->assertTrue($this->countryService->isCountryCode('PAK'));
        $this->assertFalse($this->countryService->isCountryCode('Pakistan'));
        $this->assertFalse($this->countryService->isCountryCode('123'));
        $this->assertFalse($this->countryService->isCountryCode(''));
    }

    public function testGetOfflineCountryInfo()
    {
        // Mock offline data
        Storage::shouldReceive('exists')
            ->with('countries_offline.json')
            ->andReturn(true);

        Storage::shouldReceive('get')
            ->with('countries_offline.json')
            ->andReturn(json_encode([
                [
                    'name' => 'Pakistan',
                    'code' => 'PK',
                    'capital' => 'Islamabad',
                    'region' => 'Asia',
                    'population' => 240000000,
                    'currency' => 'Pakistani rupee',
                    'languages' => ['Urdu', 'English'],
                    'timezone' => 'UTC+05:00',
                    'flag' => 'https://flagcdn.com/pk.svg'
                ]
            ]));

        // Force API failure to trigger offline fallback
        Http::fake([
            'restcountries.com/v3.1/name/Pakistan*' => Http::response(null, 500),
        ]);

        $country = $this->countryService->getCountryInfo('Pakistan');

        $this->assertInstanceOf(CountryDTO::class, $country);
        $this->assertEquals('Pakistan', $country->name);
        $this->assertEquals('PK', $country->code);
    }
}

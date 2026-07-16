<?php

namespace Tests\Unit\Services;

use App\Services\CityLookupService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CityLookupServiceTest extends TestCase
{
    public function test_all_returns_city_names_from_the_overpass_response(): void
    {
        Http::fake([
            'overpass-api.de/*' => Http::response([
                'elements' => [
                    ['tags' => ['name' => 'Львів']],
                    ['tags' => ['name' => 'Київ']],
                    ['tags' => ['name' => 'Київ']],
                ],
            ], 200),
        ]);

        $cities = (new CityLookupService)->all();

        $this->assertSame(['Київ', 'Львів'], $cities->values()->all());
    }

    public function test_all_falls_back_to_the_builtin_list_when_overpass_is_unreachable(): void
    {
        Http::fake([
            'overpass-api.de/*' => Http::response([], 500),
        ]);

        $cities = (new CityLookupService)->all();

        $this->assertContains('Київ', $cities->all());
        $this->assertNotEmpty($cities);
    }

    public function test_all_is_cached_and_does_not_refetch_on_a_second_call(): void
    {
        Http::fake([
            'overpass-api.de/*' => Http::response([
                'elements' => [['tags' => ['name' => 'Одеса']]],
            ], 200),
        ]);

        $service = new CityLookupService;
        $service->all();
        $service->all();

        Http::assertSentCount(1);
    }

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }
}

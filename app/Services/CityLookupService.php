<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Ukrainian city names for the checkout's city search-select — sourced from
 * OpenStreetMap's public Overpass API instead of a hardcoded list, so the
 * set of cities isn't something we maintain by hand. Fetched once and cached
 * for a week (city lists don't change often, and Overpass is a shared public
 * service — no reason to hit it more than that); falls back to a small
 * built-in list if the API is ever unreachable, so checkout never breaks.
 */
class CityLookupService
{
    private const CACHE_KEY = 'checkout.ukraine_cities';

    private const CACHE_TTL_DAYS = 7;

    private const OVERPASS_URL = 'https://overpass-api.de/api/interpreter';

    private const FALLBACK_CITIES = [
        'Київ', 'Харків', 'Одеса', 'Дніпро', 'Львів', 'Запоріжжя',
        'Кривий Ріг', 'Миколаїв', 'Вінниця', 'Полтава',
    ];

    /**
     * @return Collection<int, string>
     */
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, now()->addDays(self::CACHE_TTL_DAYS), function () {
            $fetched = $this->fetchFromOverpass();

            return $fetched->isNotEmpty() ? $fetched : collect(self::FALLBACK_CITIES);
        });
    }

    /**
     * @return Collection<int, string>
     */
    private function fetchFromOverpass(): Collection
    {
        $query = <<<'OVERPASS'
            [out:json][timeout:20];
            area["ISO3166-1"="UA"][admin_level=2]->.ua;
            (
              node["place"="city"](area.ua);
            );
            out body;
            OVERPASS;

        try {
            $response = Http::timeout(15)
                ->asForm()
                ->post(self::OVERPASS_URL, ['data' => $query]);

            if (! $response->successful()) {
                return collect();
            }

            return collect($response->json('elements', []))
                ->map(fn (array $element) => $element['tags']['name'] ?? null)
                ->filter()
                ->unique()
                ->sort()
                ->values();
        } catch (\Throwable $e) {
            report($e);

            return collect();
        }
    }
}

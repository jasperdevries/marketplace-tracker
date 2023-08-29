<?php

namespace App\Services;

use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;
use function config;
use function throw_if;

class SteamApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('steam.base_url');
    }

    /**
     * @throws RequestException
     * @throws Throwable
     */
    public function getPrice(string $marketHashName): array
    {
        $response = Http::get($this->baseUrl . '/market/priceoverview', [
            'appid'            => 730,
            'currency'         => 3, // EUR
            'market_hash_name' => $marketHashName,
        ])->throw();

        throw_if(!$response->json('success'));

        return $response->json();
    }

    public function getInventory(User $user): Collection
    {
        $items = Cache::remember('steam-inventory-' . $user->steam_id, 60 * 60, function () use ($user) {
            $response = Http::get($this->baseUrl . '/inventory/' . $user->steam_id . '/730/2', [
                'appid'            => 730,
                'currency'         => 3, // EUR
            ])->throw();

            throw_if(!$response->json('success'));

            return $response->collect('descriptions');
        });

        $items = $items->map(function ($item) {
            $item['history'] = Item::hasExistingHistory($item['market_hash_name']);
            return $item;
        });

        return $items;
    }
}

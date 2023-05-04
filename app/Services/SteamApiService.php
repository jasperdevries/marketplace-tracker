<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Http\Client\RequestException;
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
    public function getPrice(Item $item): array
    {
        $response = Http::get($this->baseUrl . '/market/priceoverview', [
            'appid' => 730,
            'currency' => 3, // EUR
            'market_hash_name' => $item->market_hash_name,
        ])->throw();

        throw_if(!$response->json('success'));

        return $response->json();
    }
}

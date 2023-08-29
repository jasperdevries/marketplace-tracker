<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Services\SteamApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(SteamApiService $service)
    {
        return Inertia::render('Inventory/Index', [
            'items' => $service->getInventory(Auth::user())
                ->filter(fn($item) => $item['marketable'] === 1)
                ->values()
                ->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $item                   = new Item();
        $item->market_hash_name = $request->input('market_hash_name');
        $item->save();

        if (Cache::has('item-has-existing-history:hasExistingHistory:' . $item->market_hash_name)) {
            Cache::forget('item-has-existing-history:hasExistingHistory:' . $item->market_hash_name);
        }

        return [
            'success' => true,
            'item'    => $item,
        ];
    }
}

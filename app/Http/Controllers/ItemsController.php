<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index()
    {
        $items = Item::query()
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return Inertia::render('Items/Index')
            ->with('items', $items);
    }

    public function create(Request $request)
    {
        $item = new Item();

        return Inertia::render('Items/Form')
            ->with('create_another', $request->input('create_another', false))
            ->with('item', $item);
    }

    public function store(CreateItemRequest $request)
    {
        $item = new Item();
        $item->market_hash_name = $request->input('market_hash_name');
        $item->save();

        if ($request->input('create_another', false)) {
            return redirect()->route('items.create')->with('create_another', true);
        } else {
            return redirect()->route('items.index');
        }
    }
}

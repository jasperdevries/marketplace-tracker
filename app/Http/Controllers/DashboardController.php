<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $lastAddedItems = Item::query()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        return Inertia::render('Dashboard')
            ->with('lastAddedItems', $lastAddedItems);
    }
}

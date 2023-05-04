<?php

namespace App\Console\Commands;

use App\Jobs\GetItemPrice;
use App\Models\Item;
use Illuminate\Console\Command;
use function dispatch;

class GetItemPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'items:get-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the prices of all items from the Steam API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = Item::all();
        $items->each(fn(Item $item) => dispatch(new GetItemPrice($item)));
    }
}

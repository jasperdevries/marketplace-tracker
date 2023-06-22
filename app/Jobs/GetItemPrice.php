<?php

namespace App\Jobs;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Services\SteamApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use function array_key_exists;
use function filter_var;
use function str_replace;
use const FILTER_FLAG_ALLOW_FRACTION;
use const FILTER_SANITIZE_NUMBER_FLOAT;
use const FILTER_SANITIZE_NUMBER_INT;

class GetItemPrice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Item $item)
    {}

    /**
     * Execute the job.
     */
    public function handle(SteamApiService $service): void
    {
        $prices = $service->getPrice($this->item->market_hash_name);
        $lastItem = $this->item->prices()->latest()->first();

        $itemPrice = new ItemPrice();
        $itemPrice->item()->associate($this->item);
        $itemPrice->low = isset($prices['lowest_price']) ?
            filter_var(str_replace(',', '.', $prices['lowest_price']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) :
            ($lastItem?->low ?? 0);
        $itemPrice->median = isset($prices['median_price']) ?
            filter_var(str_replace(',', '.', $prices['median_price']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) :
            ($lastItem?->median ?? 0);
        $itemPrice->volume = array_key_exists('volume', $prices) ?
            filter_var(str_replace(',', '.', $prices['volume']), FILTER_SANITIZE_NUMBER_INT) : 0;
        $itemPrice->save();
    }
}

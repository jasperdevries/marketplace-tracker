<?php

namespace App\Models;

use App\Attributes\Cached;
use App\Traits\HasCached;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use function is_null;

/**
 * App\Models\Item
 *
 * @property string                                                                    $id
 * @property string                                                                    $market_hash_name
 * @property \Illuminate\Support\Carbon|null                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                           $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ItemPrice> $prices
 * @property-read int|null                                                             $prices_count
 * @method static \Illuminate\Database\Eloquent\Builder|Item newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Item query()
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereMarketHashName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
    use HasUlids;
    use HasCached;

    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class, 'item_id', 'id');
    }

    #[Cached(ttl: 60*60, prefix: 'item-has-existing-history')]
    public static function hasExistingHistory(string $marketHashName): string
    {
        $item = Item::query()
            ->with(['prices'])
            ->where('market_hash_name', $marketHashName)
            ->first();

        if (is_null($item)) {
            return 'no_history';
        }

        /** @var Carbon $firstPriceInHistory */
        $firstPriceInHistory = $item?->prices->sortBy('created_at')->first()->created_at ?? Carbon::now();
        return match (true) {
            $firstPriceInHistory->lte(Carbon::now()->subYear())    => 'very_old',
            $firstPriceInHistory->lte(Carbon::now()->subMonths(6)) => 'old',
            $firstPriceInHistory->lte(Carbon::now()->subMonths(3)) => 'medium',
            $firstPriceInHistory->lte(Carbon::now()->subMonth())   => 'new',
            default                                                => 'very_new',
        };
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ItemPrice
 *
 * @property string $id
 * @property string $item_id
 * @property float $low
 * @property float $median
 * @property int $volume
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereLow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereMedian($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ItemPrice whereVolume($value)
 * @mixin \Eloquent
 */
class ItemPrice extends Model
{
    use HasUlids;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }
}

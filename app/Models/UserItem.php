<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\Models\UserItem
 *
 * @property-read \App\Models\Item|null $item
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserItem query()
 * @mixin \Eloquent
 */
class UserItem extends Pivot
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}

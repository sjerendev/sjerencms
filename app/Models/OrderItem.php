<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * A release or a track on an order. `purchasable_type` holds the class name:
 * there is no morph map, because a map would change Release::getMorphClass()
 * and orphan the existing media.model_type rows.
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'purchasable_type', 'purchasable_id', 'title', 'amount', 'download_count'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'download_count' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }
}

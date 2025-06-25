<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\OrderItem;

class Download extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'download_token',
        'download_count',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function hasReachedLimit()
    {
        return $this->download_count >= 3;
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}

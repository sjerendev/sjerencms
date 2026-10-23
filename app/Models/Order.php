<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One purchase. Money is whole kronor. Only the Stripe webhook marks an order
 * paid (see the phase 4 plan).
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'email', 'total', 'currency', 'country', 'status', 'locale',
        'stripe_session_id', 'stripe_payment_intent', 'receipt_url', 'consented_at', 'paid_at',
    ];

    protected $attributes = ['currency' => 'SEK', 'status' => 'unpaid'];

    protected function casts(): array
    {
        return [
            'total' => 'integer',
            'status' => OrderStatus::class,
            'consented_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function number(): string
    {
        return sprintf('%05d', $this->id);
    }

    public function scopePaid(Builder $query): void
    {
        $query->where('status', OrderStatus::Paid);
    }
}

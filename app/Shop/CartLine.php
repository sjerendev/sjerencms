<?php

namespace App\Shop;

use App\Models\Release;
use App\Models\Track;

/** One line in the cart: what is bought and the amount the buyer chose. */
final readonly class CartLine
{
    public function __construct(
        public string $key,
        public Release|Track $item,
        public int $amount,
    ) {}

    public function minimum(): int
    {
        return $this->item->min_price;
    }

    /** `track` or `release`, the same as the key's prefix. */
    public function type(): string
    {
        return $this->item instanceof Track ? 'track' : 'release';
    }
}

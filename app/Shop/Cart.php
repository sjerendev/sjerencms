<?php

namespace App\Shop;

use App\Models\Release;
use App\Models\Track;
use Illuminate\Support\Collection;

/**
 * The cart lives in the session as `cart` => ['track:12' => ['type', 'id', 'amount']].
 * It holds no prices: every read resolves the items from the database and
 * drops what can no longer be bought, and an amount is never below the
 * item's minimum (decision 9).
 */
class Cart
{
    public const TYPES = ['release' => Release::class, 'track' => Track::class];

    public const MAX_AMOUNT = 100000;

    /** Stripe's smallest amount in SEK; the checkout refuses less. */
    public const MIN_TOTAL = 3;

    public function add(string $type, mixed $id, mixed $amount): bool
    {
        $class = self::TYPES[$type] ?? null;
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $item = $class && $id !== false ? $class::find($id) : null;

        if (! $item?->isPurchasable()) {
            return false;
        }

        $this->put("{$type}:{$id}", [
            'type' => $type,
            'id' => $id,
            'amount' => $this->clamp($amount, $item->min_price),
        ]);

        return true;
    }

    public function setAmount(string $key, mixed $amount): void
    {
        $line = $this->lines()->firstWhere('key', $key);

        if ($line) {
            $this->put($key, [...session('cart')[$key], 'amount' => $this->clamp($amount, $line->minimum())]);
        }
    }

    public function remove(string $key): void
    {
        // Top-level key only: forget("cart.{$key}") would read dots in the key as a path.
        $cart = session('cart', []);
        unset($cart[$key]);
        session()->put('cart', $cart);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }

    /**
     * @return Collection<int, CartLine>
     */
    public function lines(): Collection
    {
        $stored = collect(session('cart', []));

        if ($stored->isEmpty()) {
            return collect();
        }

        $entries = $stored->filter(fn (mixed $entry, mixed $key) => $this->isWellFormed($entry, $key));

        $items = collect(self::TYPES)->map(fn (string $class, string $type) => $class::query()
            ->with($type === 'track' ? ['media', 'release.media'] : ['media', 'tracks.media'])
            ->findMany($entries->where('type', $type)->pluck('id')));

        $lines = $entries
            ->map(function (array $entry, string $key) use ($items) {
                $item = $items[$entry['type']]->firstWhere('id', $entry['id']);

                // Clamped on read too: the minimum may have been raised since the add.
                return $item?->isPurchasable() ? new CartLine($key, $item, $this->clamp($entry['amount'], $item->min_price)) : null;
            })
            ->filter()
            ->values();

        // What is malformed or can no longer be bought leaves the session too.
        $kept = $lines->mapWithKeys(fn (CartLine $line) => [
            $line->key => [...$entries[$line->key], 'amount' => $line->amount],
        ])->all();

        if ($kept !== $stored->all()) {
            session()->put('cart', $kept);
        }

        return $lines;
    }

    public function total(): int
    {
        return $this->lines()->sum('amount');
    }

    public function count(): int
    {
        return $this->lines()->count();
    }

    /** What add() stores: `track:12` => ['type' => 'track', 'id' => 12, 'amount' => 20]. */
    private function isWellFormed(mixed $entry, mixed $key): bool
    {
        return is_array($entry)
            && is_string($entry['type'] ?? null) && isset(self::TYPES[$entry['type']])
            && is_int($entry['id'] ?? null)
            && is_int($entry['amount'] ?? null)
            && $key === "{$entry['type']}:{$entry['id']}";
    }

    private function put(string $key, array $entry): void
    {
        session()->put("cart.{$key}", $entry);
    }

    /** At least the minimum, at most MAX_AMOUNT, whole kronor; not a number gives the minimum. */
    private function clamp(mixed $amount, int $minimum): int
    {
        $number = is_numeric($amount) && is_finite((float) $amount) ? (float) $amount : 0.0;

        return (int) min(max($number, $minimum), self::MAX_AMOUNT);
    }
}

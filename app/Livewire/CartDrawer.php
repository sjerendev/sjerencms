<?php

namespace App\Livewire;

use App\Shop\Cart;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The cart as a drawer on a native <dialog>. cart.js forwards the player's
 * `cart:add` here, opens the dialog on `cart-open` and writes the count from
 * `cart-updated` into the header.
 */
class CartDrawer extends Component
{
    #[On('cart-add')]
    public function add(string $type, mixed $id, mixed $amount): void
    {
        if (app(Cart::class)->add($type, $id, $amount)) {
            $this->dispatch('cart-open');
        }

        $this->announce();
    }

    public function updateAmount(string $key, mixed $amount): void
    {
        app(Cart::class)->setAmount($key, $amount);

        $this->announce();
    }

    public function remove(string $key): void
    {
        app(Cart::class)->remove($key);

        $this->announce();
    }

    public function render(): View
    {
        $lines = app(Cart::class)->lines();

        return view('livewire.cart-drawer', [
            'lines' => $lines,
            'total' => $lines->sum('amount'),
            'tracks' => $lines->filter(fn ($line) => $line->type() === 'track')->count(),
            'albums' => $lines->filter(fn ($line) => $line->type() === 'release')->count(),
        ]);
    }

    private function announce(): void
    {
        $this->dispatch('cart-updated', count: app(Cart::class)->count());
    }
}

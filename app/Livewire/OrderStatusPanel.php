<?php

namespace App\Livewire;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Settings\GeneralSettings;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Tack.dc.html. Polls while the order is unpaid, because only the Stripe
 * webhook marks it paid; the root loses wire:poll when the paid markup morphs
 * in, which stops the polling. The order is locked: the id comes from the
 * controller, which has checked that this browser may see it.
 */
class OrderStatusPanel extends Component
{
    #[Locked]
    public Order $order;

    public function render(): View
    {
        $paid = $this->order->status === OrderStatus::Paid;

        return view('livewire.order-status-panel', [
            'paid' => $paid,
            'items' => $paid ? $this->order->items()->withPurchasable()->get() : collect(),
            'limit' => app(GeneralSettings::class)->download_limit,
        ]);
    }
}

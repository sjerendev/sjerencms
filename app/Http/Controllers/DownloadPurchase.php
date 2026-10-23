<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\OrderItem;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams a bought WAV or ZIP from the private `music` disk and counts it.
 * The counter moves only once the file is known to exist, and never past the limit.
 */
class DownloadPurchase
{
    public function __invoke(OrderItem $item, GeneralSettings $settings): StreamedResponse
    {
        $order = $item->order;

        abort_unless($order->status === OrderStatus::Paid, 403);
        abort_unless(
            (auth()->id() !== null && auth()->id() === $order->user_id)
            || in_array($order->id, session('orders', []), true),
            403,
        );

        $file = $item->file();
        abort_if($file === null, 404);

        // One atomic UPDATE: two parallel requests cannot both take the last download.
        $counted = OrderItem::whereKey($item->id)
            ->where('download_count', '<', $settings->download_limit)
            ->increment('download_count');
        abort_unless($counted === 1, 403);

        return Storage::disk($file->disk)->download($file->getPathRelativeToRoot(), $item->downloadName());
    }
}

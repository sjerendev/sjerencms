<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Download;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class ShopController extends Controller
{
    public function download($token)
    {
        $download = Download::where('download_token', $token)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($download->download_count >= 3) {
            abort(403, 'Maximum download attempts reached');
        }

        if ($download->expires_at && $download->expires_at < now()) {
            abort(403, 'Download link has expired');
        }

        $download->increment('download_count');

        return Storage::download($download->orderItem->product->download_url);
    }

    public function processCheckout(Request $request)
    {
        $request->validate([
            'payment_intent' => 'required|string',
            'email' => 'required|email',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.price' => 'required|numeric',
            'items.*.selectedAddons' => 'array'
        ]);

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'total' => collect($request->items)->sum('price'),
            'status' => 'completed',
            'payment_method' => 'stripe',
            'payment_id' => $request->payment_intent,
            'billing_details' => json_encode(['email' => $request->email])
        ]);

        // Create order items and download tokens
        foreach ($request->items as $item) {
            $orderItem = $order->items()->create([
                'product_id' => $item['id'],
                'quantity' => 1,
                'price' => $item['price'],
                'selected_addons' => isset($item['selectedAddons']) ? json_encode($item['selectedAddons']) : null
            ]);

            // Create download token
            Download::create([
                'order_item_id' => $orderItem->id,
                'user_id' => auth()->id(),
                'download_token' => Str::uuid(),
                'download_count' => 0,
                'expires_at' => now()->addDays(30)
            ]);
        }

        return response()->json([
            'message' => 'Order processed successfully',
            'order_number' => $order->order_number
        ]);
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50', // Minimum $0.50
            'email' => 'required|email'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'metadata' => [
                    'email' => $request->email
                ]
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function getDownloads()
    {
        $downloads = Download::where('user_id', auth()->id())
            ->with(['orderItem.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($downloads);
    }
}

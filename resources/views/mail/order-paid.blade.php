<x-mail::message>
{{ __('shop.receiptBody') }}

@foreach ($order->items as $item)
- {{ $item->title }}: {{ $item->amount }} kr
@endforeach

**{{ __('shop.sum') }}: {{ $order->total }} kr**

@if ($loginUrl)
<x-mail::button :url="$loginUrl">
{{ __('shop.receiptDownload') }}
</x-mail::button>
@endif

@if ($order->receipt_url)
[{{ __('shop.receiptLink') }}]({{ $order->receipt_url }})
@endif
</x-mail::message>

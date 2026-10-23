{{-- MinaKop.dc.html --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut flex flex-col items-start justify-between gap-[16px] pt-[40px] lg:flex-row lg:items-end lg:pt-[76px]">
        <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[96px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ __('nav.account') }}</h1>
        <form method="post" action="{{ route('shop.logout') }}" class="lbl m-0 flex items-center gap-[6px] text-ink-dim">
            @csrf
            <span>{{ $user->email }} ·</span>
            <button type="submit" class="lbl cursor-pointer border-0 bg-transparent p-0 text-ink hover:text-accent">{{ __('shop.logout') }}</button>
        </form>
    </div>

    @if ($items->isEmpty())
        <p class="gut m-0 pt-[48px] pb-[64px] text-[16px] text-ink-dim lg:pt-[72px] lg:pb-[120px]">{{ __('shop.nothing') }}</p>
    @else
        <div class="gut pt-[48px] lg:pt-[72px]">
            <x-section-head number="01" :label="__('shop.downloads')" :aside="__('shop.perPurchase', ['limit' => $limit])" />
            @foreach ($items as $item)
                <x-shop.download-row :item="$item" account />
            @endforeach
        </div>

        <div class="gut pt-[64px] pb-[64px] lg:pt-[96px] lg:pb-[120px]">
            <x-section-head number="02" :label="__('shop.receipts')" />
            <div class="hidden grid-cols-[200px_200px_minmax(0,1fr)_120px_140px] items-center gap-[32px] py-[18px] lg:grid">
                <span class="lbl text-ink-dimmer">{{ __('shop.order') }}</span>
                <span class="lbl text-ink-dimmer">{{ __('shop.date') }}</span>
                <span class="lbl text-ink-dimmer">{{ __('shop.contents') }}</span>
                <span class="lbl text-ink-dimmer">{{ __('shop.amount') }}</span>
                <span></span>
            </div>
            @foreach ($orders as $order)
                <div class="grid grid-cols-2 items-baseline gap-x-[16px] gap-y-[8px] border-t border-hair py-[18px] text-[16px] last:border-b lg:grid-cols-[200px_200px_minmax(0,1fr)_120px_140px] lg:items-center lg:gap-[32px]">
                    <span><span class="lbl text-ink-dimmer lg:hidden">{{ __('shop.order') }} </span>{{ $order->number() }}</span>
                    <span>{{ ($order->paid_at ?? $order->created_at)->translatedFormat('j M Y') }}</span>
                    <span class="col-span-2 lg:col-span-1">{{ $order->items->pluck('title')->join(', ') }}</span>
                    <span>{{ $order->total }} kr</span>
                    @if ($order->receipt_url)
                        <a href="{{ $order->receipt_url }}" target="_blank" rel="noopener" class="lbl text-right">{{ __('shop.receipt') }} ↗</a>
                    @else
                        <span></span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.site>

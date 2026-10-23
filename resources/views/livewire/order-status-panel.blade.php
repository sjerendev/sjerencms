{{-- Tack.dc.html. One root, so the poll goes away with the unpaid markup. --}}
<div @unless ($paid) wire:poll.2s @endunless>
    <div class="gut grid grid-cols-1 items-end gap-[20px] pt-[40px] lg:grid-cols-[minmax(0,1fr)_440px] lg:gap-[60px] lg:pt-[76px]">
        <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[108px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ __('shop.thanks') }}<span class="text-accent">.</span></h1>
        <p class="m-0 text-[16px] leading-[1.6] text-ink-dim" @unless ($paid) role="status" @endunless>
            {{ $paid ? __('shop.thanksText', ['email' => $order->email, 'limit' => $limit]) : __('shop.processing') }}
        </p>
    </div>

    @if ($paid)
        <div class="gut pt-[48px] lg:pt-[72px]">
            <x-section-head number="01" :label="__('shop.download')" :aside="__('shop.orderLine', ['number' => $order->number(), 'total' => $order->total])" />
            <div class="[&>:first-child]:border-t-0">
                @foreach ($items as $item)
                    <x-shop.download-row :item="$item" />
                @endforeach
            </div>
            <div class="border-t border-hair"></div>

            <div class="flex flex-col gap-[24px] pt-[32px] pb-[64px] lg:flex-row lg:items-center lg:justify-between lg:pt-[40px] lg:pb-[100px]">
                <span class="text-[15px] text-ink-dim">{{ __('shop.help') }}</span>
                <div class="flex flex-wrap gap-x-[32px] gap-y-[14px]">
                    <a href="{{ route('shop.account') }}" wire:navigate class="lbl">{{ __('nav.account') }} →</a>
                    <a href="{{ route('music.index') }}" wire:navigate class="lbl">{{ __('shop.backToMusic') }} →</a>
                </div>
            </div>
        </div>
    @endif
</div>

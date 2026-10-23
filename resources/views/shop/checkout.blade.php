{{-- Kassa.dc.html. --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut flex items-end justify-between gap-[24px] pt-[40px] lg:pt-[64px]">
        <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[76px]">{{ __('shop.checkout') }}</h1>
        <button type="button" data-cart-open class="lbl cursor-pointer bg-transparent py-[12px] text-ink-dim hover:text-accent">← {{ __('shop.backToCart') }}</button>
    </div>
    <livewire:checkout-form />
</x-layouts.site>

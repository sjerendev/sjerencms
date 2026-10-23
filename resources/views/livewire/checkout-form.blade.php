{{-- Kassa.dc.html. The drawing's Land and Kort/PayPal steps are gone: Stripe asks for the country
     and offers the payment methods on its own page (decision 3). novalidate so the server's
     messages are the ones shown. --}}
<form wire:submit="pay" novalidate class="gut grid grid-cols-1 items-start gap-[40px] pt-[32px] pb-[64px] lg:grid-cols-[minmax(0,1fr)_440px] lg:gap-[96px] lg:pt-[48px] lg:pb-[100px]">
    <div class="border-b border-hair">
        <div class="grid grid-cols-[36px_minmax(0,1fr)] gap-[12px] border-t border-hair-strong py-[30px] lg:grid-cols-[70px_minmax(0,1fr)] lg:gap-[24px]">
            <span class="lbl">01</span>
            <div class="flex flex-col gap-[14px]">
                <label class="flex flex-col gap-[8px]">
                    <span class="lbl">{{ __('shop.email') }}</span>
                    <input
                        type="email"
                        wire:model="email"
                        autocomplete="email"
                        placeholder="{{ __('shop.emailPlaceholder') }}"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="h-[54px] rounded-none border border-hair-strong bg-transparent px-[16px] text-[17px] text-ink outline-accent focus-visible:outline-2 focus-visible:outline-offset-2"
                    >
                </label>
                @error('email')
                    <p id="email-error" role="alert" class="m-0 border-l-2 border-accent pl-[12px] text-[14px] leading-[1.5]">{{ $message }}</p>
                @enderror
                <span class="text-[14px] leading-[1.5] text-ink-dim">{{ __('shop.emailHelp') }}</span>
                <a href="{{ route('shop.login') }}" wire:navigate class="lbl text-ink-dim hover:text-accent">{{ __('shop.boughtBefore') }} {{ __('nav.login') }}</a>
            </div>
        </div>

        <div class="grid grid-cols-[36px_minmax(0,1fr)] gap-[12px] border-t border-hair py-[30px] lg:grid-cols-[70px_minmax(0,1fr)] lg:gap-[24px]">
            <span class="lbl">02</span>
            <fieldset class="m-0 flex min-w-0 flex-col gap-[10px] border-0 p-0">
                <legend class="lbl p-0 pb-[14px]">{{ __('shop.payment') }}</legend>
                <label class="flex min-h-[64px] cursor-pointer items-center gap-[16px] border border-hair-strong px-[20px] py-[14px]">
                    <input type="checkbox" wire:model="consent" class="size-[18px] shrink-0 accent-accent" @error('consent') aria-invalid="true" aria-describedby="consent-error" @enderror>
                    <span class="text-[15px] leading-[1.4]">{{ __('shop.consent') }}</span>
                </label>
                @error('consent')
                    <p id="consent-error" role="alert" class="m-0 border-l-2 border-accent pl-[12px] text-[14px] leading-[1.5]">{{ $message }}</p>
                @enderror
                <span class="pt-[6px] text-[14px] leading-[1.5] text-ink-dim">{{ __('shop.paymentHelp') }}</span>
            </fieldset>
        </div>
    </div>

    <aside aria-label="{{ __('shop.yourOrder') }}" class="flex flex-col gap-[18px] border border-panel-line bg-panel p-[30px] text-[#edecea]">
        <span class="lbl">{{ __('shop.yourOrder') }}</span>
        @foreach ($lines as $line)
            @php
                $isTrack = $line->type() === 'track';
                $release = $isTrack ? $line->item->release : $line->item;
                $cover = $release->coverUrl();
            @endphp
            <div wire:key="{{ $line->key }}" class="flex items-center gap-[14px] first:pt-[6px]">
                @if ($cover)
                    <img src="{{ $cover }}" alt="" class="block size-[56px] shrink-0 object-cover">
                @else
                    <span class="block size-[56px] shrink-0 bg-[rgba(237,236,234,0.15)]"></span>
                @endif
                <div class="flex min-w-0 grow flex-col gap-[3px]">
                    <span class="font-semibold">{{ $line->item->title }}</span>
                    <span class="lbl text-[rgba(237,236,234,0.62)]">{{ __($isTrack ? 'shop.trackBy' : 'shop.albumBy', ['artist' => $release->artist]) }}</span>
                </div>
                <span class="whitespace-nowrap">{{ $line->amount }} kr</span>
            </div>
        @endforeach
        {{-- "Varav moms" waits for the VAT answer (decision 1). --}}
        <div class="mt-[6px] flex items-center justify-between border-t border-[rgba(237,236,234,0.55)] pt-[16px]">
            <span class="lbl">{{ __('shop.toPay') }}</span>
            <span class="text-[36px] font-semibold tracking-[-0.03em]">{{ $total }} kr</span>
        </div>
        @error('pay')
            <p role="alert" class="m-0 border-l-2 border-accent pl-[12px] text-[14px] leading-[1.5]">{{ $message }}</p>
        @enderror
        <button type="submit" wire:loading.attr="disabled" class="lbl flex h-[56px] cursor-pointer items-center justify-center rounded-pill border-0 bg-accent text-plate disabled:opacity-60">{{ __('shop.pay', ['amount' => $total]) }}</button>
        <span class="lbl text-center text-[rgba(237,236,234,0.62)]">{{ __('shop.wav') }}</span>
    </aside>
</form>

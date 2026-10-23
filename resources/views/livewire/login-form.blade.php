{{-- LoggaIn.dc.html. novalidate so the server's message is the one shown. --}}
<div class="flex flex-col gap-[28px]">
    <form wire:submit="send" novalidate class="flex flex-col gap-[16px] border-t border-hair-strong pt-[22px]">
        <label class="flex flex-col gap-[8px]">
            <span class="lbl">{{ __('shop.email') }}</span>
            <input
                type="email"
                wire:model="email"
                autocomplete="email"
                placeholder="{{ __('shop.emailPlaceholder') }}"
                @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                class="h-[54px] rounded-none border border-hair-strong bg-transparent px-[16px] text-[17px] text-ink outline-accent focus-visible:outline-2 focus-visible:outline-offset-2 lg:h-[58px] lg:text-[18px]"
            >
        </label>
        @error('email')
            <p id="email-error" role="alert" class="m-0 border-l-2 border-accent pl-[12px] text-[14px] leading-[1.5]">{{ $message }}</p>
        @enderror
        <button type="submit" wire:loading.attr="disabled" class="lbl flex h-[56px] cursor-pointer items-center justify-center rounded-pill border-0 bg-accent text-plate disabled:opacity-60">{{ __('shop.sendLink') }}</button>
    </form>

    @if ($sentTo)
        <div role="status" class="flex items-start gap-[14px] border border-hair bg-ink/[0.045] p-[20px]">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="mt-[2px] shrink-0"><path d="M4 6h16v12H4z"></path><path d="m4 7 8 6 8-6"></path></svg>
            <div class="flex flex-col gap-[6px]">
                <span class="text-[16px] font-semibold">{{ __('shop.sent', ['email' => $sentTo]) }}</span>
                <span class="text-[14px] leading-[1.5] text-ink-dim">{{ __('shop.sentHelp', ['minutes' => $minutes]) }}</span>
            </div>
        </div>
    @endif
</div>

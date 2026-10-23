{{-- LoggaIn.dc.html. --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut grid grid-cols-1 items-start gap-[32px] pt-[40px] pb-[64px] lg:grid-cols-[minmax(0,1fr)_520px] lg:gap-[96px] lg:pt-[96px] lg:pb-[120px]">
        <div class="flex flex-col gap-[24px]">
            <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[96px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ __('nav.account') }}</h1>
            <p class="m-0 max-w-[44ch] text-[17px] leading-[1.6] text-ink-dim">{{ __('shop.loginIntro') }}</p>
        </div>
        <div class="flex flex-col gap-[28px] lg:pt-[14px]">
            @if (session('expired'))
                <p role="alert" class="m-0 border-l-2 border-accent pl-[12px] text-[14px] leading-[1.5]">{{ session('expired') }}</p>
            @endif
            <livewire:login-form />
        </div>
    </div>
</x-layouts.site>

{{-- The page a mail link opens. Its button posts to the same URL, see ConfirmLogin. --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut flex flex-col gap-[32px] pt-[40px] pb-[64px] lg:max-w-[760px] lg:pt-[96px] lg:pb-[120px]">
        <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[96px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ __('nav.account') }}</h1>
        <form method="post" action="{{ request()->fullUrl() }}" class="flex max-w-[520px] flex-col gap-[16px] border-t border-hair-strong pt-[22px]">
            @csrf
            <button type="submit" class="lbl flex h-[56px] cursor-pointer items-center justify-center rounded-pill border-0 bg-accent text-plate">{{ __('nav.login') }}</button>
        </form>
    </div>
</x-layouts.site>

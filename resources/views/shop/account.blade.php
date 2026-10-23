{{-- MinaKop.dc.html, header only. Task 8 adds the downloads and receipts. --}}
<x-layouts.site :title="$title" :description="$description" :alternates="$alternates">
    <div class="gut flex flex-col items-start justify-between gap-[16px] pt-[40px] lg:flex-row lg:items-end lg:pt-[76px]">
        <h1 class="m-0 text-[44px] leading-none font-semibold tracking-[-0.032em] lg:text-[96px] lg:leading-[0.95] lg:tracking-[-0.038em]">{{ __('nav.account') }}</h1>
        <form method="post" action="{{ route('shop.logout') }}" class="lbl m-0 flex items-center gap-[6px] text-ink-dim">
            @csrf
            <span>{{ $user->email }} ·</span>
            <button type="submit" class="lbl cursor-pointer border-0 bg-transparent p-0 text-ink hover:text-accent">{{ __('shop.logout') }}</button>
        </form>
    </div>
</x-layouts.site>

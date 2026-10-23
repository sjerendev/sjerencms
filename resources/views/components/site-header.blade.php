{{-- Port of Header.tsx. --}}
@props(['alternates' => []])
@php
    $email = app(\App\Settings\GeneralSettings::class)->email;
    $nav = [
        ['href' => route('page.show', ['slug' => 'projekt']), 'key' => 'work', 'active' => request()->route('slug') === 'projekt'],
        ['href' => route('page.show', ['slug' => 'om-mig']), 'key' => 'about', 'active' => request()->route('slug') === 'om-mig'],
        ['href' => route('music.index'), 'key' => 'music', 'active' => request()->routeIs('music.*')],
        // Playground is not in the new design yet, see sjeren-2026 TODO-INNEHALL.md.
    ];
@endphp
<header class="gut">
    <div class="flex items-center justify-between gap-6 border-b border-hair-strong pt-[18px] pb-[13px] md:pt-[26px] md:pb-[16px]">
        <a href="{{ route('page.home') }}" wire:navigate aria-label="Patrik Sjerén">
            <x-wordmark class="h-[19px] w-auto md:h-[22px]" />
        </a>

        <nav class="hidden items-center gap-[36px] lg:flex" aria-label="{{ __('nav.menu') }}">
            @foreach ($nav as $item)
                <a
                    href="{{ $item['href'] }}"
                    wire:navigate
                    @if ($item['active']) aria-current="page" @endif
                    @class(['lbl hover:text-accent', 'border-b border-ink pb-[2px]' => $item['active']])
                >
                    {{ __('nav.'.$item['key']) }}
                </a>
            @endforeach
            <button type="button" data-cart-open class="lbl cursor-pointer bg-transparent hover:text-accent">
                {{ __('shop.cart') }} · <span data-cart-count>{{ app(\App\Shop\Cart::class)->count() }}</span>
            </button>
        </nav>

        <div class="hidden items-center gap-[20px] lg:flex">
            <x-locale-switch :alternates="$alternates" />
            <x-theme-toggle />
            @if ($email)
                <a
                    href="mailto:{{ $email }}"
                    class="lbl flex items-center gap-[7px] rounded-pill bg-ink px-[20px] py-[11px] text-paper transition-colors duration-300 hover:bg-accent hover:text-plate"
                >
                    {{ __('nav.cta') }} <span aria-hidden="true" class="text-[12px]">↗</span>
                </a>
            @endif
        </div>

        {{-- Mobile: the cart stays outside the menu, one tap away. --}}
        <button type="button" data-cart-open class="lbl ml-auto cursor-pointer bg-transparent hover:text-accent lg:hidden">
            {{ __('shop.cart') }} · <span data-cart-count>{{ app(\App\Shop\Cart::class)->count() }}</span>
        </button>

        {{-- Native <details> instead of a menu with state. --}}
        <details class="group relative lg:hidden">
            <summary
                class="grid size-[44px] cursor-pointer list-none place-items-center rounded-pill bg-ink [&::-webkit-details-marker]:hidden"
                aria-label="{{ __('nav.menu') }}"
            >
                <span aria-hidden="true" class="flex flex-col gap-[4px]">
                    <span class="block h-[1.5px] w-[16px] bg-paper"></span>
                    <span class="block h-[1.5px] w-[16px] bg-paper"></span>
                </span>
            </summary>
            <div class="absolute right-0 z-10 mt-[12px] flex w-[240px] flex-col gap-[18px] border border-hair-mid bg-paper p-[20px]">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}" wire:navigate @if ($item['active']) aria-current="page" @endif class="lbl">
                        {{ __('nav.'.$item['key']) }}
                    </a>
                @endforeach
                <div class="flex items-center justify-between border-t border-hair pt-[16px]">
                    <x-locale-switch :alternates="$alternates" />
                    <x-theme-toggle />
                </div>
                @if ($email)
                    <a
                        href="mailto:{{ $email }}"
                        class="lbl flex items-center justify-center gap-[7px] rounded-pill bg-ink px-[20px] py-[11px] text-paper transition-colors duration-300 hover:bg-accent hover:text-plate"
                    >
                        {{ __('nav.cta') }} <span aria-hidden="true" class="text-[12px]">↗</span>
                    </a>
                @endif
            </div>
        </details>
    </div>

    <div class="flex justify-between gap-3 py-[11px] md:py-[13px]">
        <span class="lbl text-ink-dim">{{ __('bar.place') }}</span>
        <span class="lbl flex items-center gap-[7px] text-ink-dim md:gap-[8px]">
            <span aria-hidden="true" class="inline-block size-[6px] rounded-pill bg-open md:size-[7px]"></span>
            {{ __('bar.status') }}
        </span>
    </div>
</header>

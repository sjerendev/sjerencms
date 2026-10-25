{{-- The preview player: the bar on Omslag.dc.html from lg, the mini player on SlappMobil.dc.html below.
     Hidden until something plays; player.js fills every data-player-* hook. --}}
<div
    id="player"
    data-player-root
    data-label-play="{{ __('music.playPlayer') }}"
    data-label-pause="{{ __('music.pausePlayer') }}"
    hidden
    class="fixed inset-x-0 bottom-0 z-40 border-t border-hair bg-bar text-[#edecea]"
>
    <audio preload="none"></audio>

    <div class="hidden h-[80px] grid-cols-[56px_300px_minmax(0,1fr)_90px_180px_44px] items-center gap-[24px] px-[var(--gutter)] lg:grid">
        <button type="button" data-player-toggle aria-label="{{ __('music.playPlayer') }}" class="flex size-[44px] cursor-pointer items-center justify-center rounded-pill bg-bar-sound text-plate">
            <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/></svg>
        </button>
        <div class="flex min-w-0 items-center gap-[14px]">
            <img data-player-cover alt="" class="block size-[44px] object-cover">
            <div class="flex min-w-0 flex-col gap-[4px]">
                <span data-player-title class="truncate text-[15px] font-semibold"></span>
                <span data-player-meta class="lbl truncate text-[rgba(237,236,234,0.62)]"></span>
            </div>
        </div>
        <div class="flex h-[28px] items-center">
            <svg data-player-progress style="width: 0%" height="28" viewBox="0 0 300 28" preserveAspectRatio="none" aria-hidden="true"><path d="M0 14 Q 7.5 3 15 14 T 30 14 T 45 14 T 60 14 T 75 14 T 90 14 T 105 14 T 120 14 T 135 14 T 150 14 T 165 14 T 180 14 T 195 14 T 210 14 T 225 14 T 240 14 T 255 14 T 270 14 T 285 14 T 300 14" fill="none" stroke="var(--bar-sound)" stroke-width="1.6"/></svg>
            <span class="block h-px grow bg-[rgba(237,236,234,0.3)]"></span>
        </div>
        <span data-player-time class="lbl text-right text-[rgba(237,236,234,0.75)]"></span>
        <button type="button" data-cart-add="track" class="lbl cursor-pointer rounded-pill border border-[rgba(237,236,234,0.55)] px-[18px] py-[12px] text-center hover:border-accent hover:bg-accent hover:text-plate">
            {{ __('music.buyTrack') }} · <span data-player-price></span> kr
        </button>
        <x-player-close />
    </div>

    <div class="lg:hidden">
        <div class="h-[2px] bg-[rgba(237,236,234,0.25)]"><div data-player-progress style="width: 0%" class="h-[2px] bg-bar-sound"></div></div>
        <div class="flex items-center gap-[12px] px-[20px] py-[12px]">
            <img data-player-cover alt="" class="block size-[44px] object-cover">
            <div class="flex min-w-0 grow flex-col gap-[3px]">
                <span data-player-title class="truncate text-[15px] font-semibold"></span>
                <span class="lbl truncate text-[rgba(237,236,234,0.62)]"><span data-player-alias></span> · <span data-player-time></span></span>
            </div>
            <button type="button" data-player-toggle aria-label="{{ __('music.playPlayer') }}" class="flex size-[48px] shrink-0 cursor-pointer items-center justify-center rounded-pill bg-bar-sound text-plate">
                <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/></svg>
            </button>
            <x-player-close />
        </div>
    </div>
</div>

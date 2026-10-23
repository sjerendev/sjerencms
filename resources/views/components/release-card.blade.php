{{-- One release in the grid on Omslag.dc.html. --}}
@props(['release'])
@php
    $href = url('/'.request()->route('locale').'/musik/'.$release->slug);
    $tracks = $release->tracks_count ?? $release->tracks()->count();
@endphp
<div class="flex flex-col gap-[12px]">
    <a href="{{ $href }}" wire:navigate>
        @if ($cover = $release->coverUrl())
            <img src="{{ $cover }}" alt="{{ __('music.coverAlt', ['title' => $release->title]) }}" loading="lazy" class="block aspect-square w-full object-cover">
        @else
            <span role="img" aria-label="{{ __('music.coverAlt', ['title' => $release->title]) }}" class="block aspect-square w-full bg-hair"></span>
        @endif
    </a>
    <span class="lbl pt-[4px] text-ink-dimmer">{{ $release->artist }}</span>
    <a href="{{ $href }}" wire:navigate class="text-[18px] leading-[1.15] font-medium tracking-[-0.02em] hover:text-accent lg:text-[22px]">{{ $release->title }}</a>
    <span class="text-[14px] text-ink-dim">{{ $release->released_on->year }} · {{ trans_choice('music.tracks', $tracks) }}</span>
    <div class="flex items-center gap-[10px] pt-[4px]">
        <button
            type="button"
            data-play-release="{{ $release->slug }}"
            aria-label="{{ __('music.play', ['title' => $release->title]) }}"
            class="flex size-[44px] shrink-0 cursor-pointer items-center justify-center rounded-pill border border-sound text-sound"
        ><svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true"><path d="M3 1.5 12 7 3 12.5Z" fill="currentColor"/></svg></button>
        <button
            type="button"
            data-cart-add="release"
            data-id="{{ $release->id }}"
            data-amount="{{ $release->min_price }}"
            class="lbl inline-flex h-[44px] cursor-pointer items-center rounded-pill border border-buy px-[18px] text-buy hover:border-accent hover:bg-accent hover:text-plate"
        >{{ __('music.buy') }} · {{ $release->min_price }} kr</button>
    </div>
</div>

{{-- One order item on Tack.dc.html, or on MinaKop.dc.html with :account. Three states: ready, preparing
     (no file yet, e.g. the album's zip) and exhausted. Renders from the snapshot title, so a purchasable
     that is gone still gets a row. --}}
@props(['item', 'account' => false])
@php
    $purchasable = $item->purchasable;
    $isTrack = $item->purchasable_type === \App\Models\Track::class;
    $release = $purchasable instanceof \App\Models\Track ? $purchasable->release : $purchasable;
    $cover = $release?->coverUrl();
    $limit = app(\App\Settings\GeneralSettings::class)->download_limit;
    $left = $item->downloadsLeft();
    $state = match (true) {
        $item->file() === null => 'preparing',
        $left === 0 => 'exhausted',
        default => 'ready',
    };
    $mail = app(\App\Settings\GeneralSettings::class)->email;
    $coverSize = $account ? 'size-[72px] lg:size-[96px]' : 'size-[72px] lg:size-[120px]';
    $columns = $account
        ? 'lg:grid-cols-[96px_minmax(0,1fr)_170px_150px_200px]'
        : 'lg:grid-cols-[120px_minmax(0,1fr)_160px_200px]';
@endphp
<div class="grid grid-cols-[72px_minmax(0,1fr)] items-center gap-x-[16px] gap-y-[18px] border-t border-hair py-[22px] lg:gap-[32px] lg:py-[26px] {{ $columns }}">
    @if ($cover)
        <img src="{{ $cover }}" alt="" loading="lazy" class="block object-cover {{ $coverSize }}">
    @else
        <span class="block bg-hair {{ $coverSize }}"></span>
    @endif

    <div class="flex min-w-0 flex-col gap-[6px]">
        @if ($release)
            <span class="lbl text-ink-dim">{{ $release->artist }}</span>
        @endif
        @if ($account && $release)
            <a href="{{ route('music.show', ['release' => $release]) }}" wire:navigate class="text-[22px] leading-[1.12] font-medium tracking-[-0.025em] hover:text-accent lg:text-[26px]">{{ $item->title }}</a>
        @else
            <span class="text-[26px] leading-[1.1] font-medium tracking-[-0.028em] lg:text-[34px]">{{ $item->title }}</span>
        @endif
        @unless ($account)
            <span class="text-[14px] text-ink-dim">{{ $isTrack ? __('shop.kindTrack') : __('shop.kindAlbum', ['count' => $release?->tracks->count() ?? 0]) }}</span>
        @endunless
    </div>

    @if ($account)
        <span class="col-span-2 text-[14px] text-ink-dim lg:col-span-1">{{ __($isTrack ? 'shop.track' : 'shop.album') }} · {{ __('shop.bought', ['date' => ($item->order->paid_at ?? $item->order->created_at)->translatedFormat('j M Y')]) }}</span>
    @endif

    <div class="col-span-2 flex flex-col gap-[8px] lg:col-span-1">
        <span class="lbl {{ $account ? '' : 'text-ink-dim' }}">{{ __('shop.left', ['left' => $left, 'limit' => $limit]) }}</span>
        @if ($account && $limit <= 10)
            <div aria-hidden="true" class="flex gap-[4px]">
                @for ($i = 0; $i < $limit; $i++)
                    <span class="block h-[4px] w-[18px] {{ $i < $left ? 'bg-accent' : 'bg-ink/15' }}"></span>
                @endfor
            </div>
        @endif
    </div>

    <div class="col-span-2 flex flex-col items-stretch gap-[8px] lg:col-span-1">
        @if ($state === 'ready')
            <a href="{{ route('download', $item) }}" class="lbl inline-flex h-[48px] items-center justify-center rounded-pill bg-ink px-[22px] text-paper hover:text-accent">{{ __('shop.downloadWav') }} ↓</a>
        @else
            <span aria-disabled="true" class="lbl inline-flex h-[48px] items-center justify-center rounded-pill border border-dashed border-hair-strong px-[22px] text-center text-ink-dimmer">{{ $state === 'preparing' ? __('shop.preparing') : __('shop.exhausted') }}</span>
            @if ($state === 'exhausted' && filled($mail))
                <a href="mailto:{{ $mail }}" class="lbl text-center text-ink-dim hover:text-accent">{{ __('shop.writeMe') }}</a>
            @endif
        @endif
    </div>
</div>

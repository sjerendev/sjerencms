{{-- Korg.dc.html. A native <dialog> gives focus, Escape and the backdrop; wire:ignore.self keeps
     its `open` attribute when Livewire morphs it. --}}
@php
    // Computed here: Livewire wraps @if in comment markers, which would split the text.
    $heading = collect([__('shop.cart'), collect([
        $tracks ? trans_choice('shop.tracks', $tracks) : null,
        $albums ? trans_choice('shop.albums', $albums) : null,
    ])->filter()->implode(', ')])->filter()->implode(' · ');
@endphp
<div>
    <dialog
        data-cart-dialog
        wire:ignore.self
        aria-label="{{ __('shop.cart') }}"
        class="fixed inset-y-0 right-0 left-auto m-0 h-dvh max-h-none w-full max-w-none flex-col border-l border-hair bg-paper px-[20px] text-ink open:flex backdrop:bg-ink/50 in-data-[theme=dark]:backdrop:bg-black/60 lg:w-[540px] lg:px-[44px]"
    >
        <div class="flex shrink-0 items-center justify-between border-b border-hair-strong pt-[22px] pb-[18px] lg:pt-[30px]">
            <span class="lbl">{{ $heading }}</span>
            <form method="dialog">
                <button aria-label="{{ __('shop.close') }}" class="grid size-[44px] cursor-pointer place-items-center rounded-pill border border-hair-strong bg-transparent p-0 text-ink hover:border-accent hover:text-accent">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M2 2 12 12M12 2 2 12"/></svg>
                </button>
            </form>
        </div>

        <div class="min-h-0 grow overflow-y-auto pt-[8px]">
            @forelse ($lines as $line)
                @php
                    $isTrack = $line->type() === 'track';
                    $release = $isTrack ? $line->item->release : $line->item;
                    $cover = $release->coverUrl();
                    $caption = $isTrack ? "{$release->artist} · {$release->title}" : $release->artist;
                @endphp
                <div wire:key="{{ $line->key }}" class="grid grid-cols-[96px_minmax(0,1fr)_120px] gap-[20px] border-t border-hair py-[22px] first:border-t-0">
                    @if ($cover)
                        <img src="{{ $cover }}" alt="" class="block size-[96px] object-cover">
                    @else
                        <span class="block size-[96px] bg-hair"></span>
                    @endif
                    <div class="flex min-w-0 flex-col gap-[6px]">
                        <span class="lbl text-ink-dim">{{ $caption }}</span>
                        <span class="text-[22px] leading-[1.15] font-semibold tracking-[-0.015em]">{{ $line->item->title }}</span>
                        <span class="text-[14px] text-ink-dim">{{ $isTrack
                            ? __('shop.trackMeta', ['min' => $line->minimum()])
                            : __('shop.albumMeta', ['count' => $line->item->tracks->count(), 'min' => $line->minimum()]) }}</span>
                        <button type="button" wire:click="remove('{{ $line->key }}')" class="lbl cursor-pointer self-start bg-transparent py-[8px] text-ink-dimmer hover:text-accent">{{ __('shop.remove') }}</button>
                    </div>
                    <label class="flex h-[44px] items-center gap-[6px] border border-hair-strong px-[12px] focus-within:border-accent">
                        <input
                            type="text"
                            inputmode="numeric"
                            value="{{ $line->amount }}"
                            aria-label="{{ __('shop.amountFor', ['title' => $line->item->title]) }}"
                            wire:change="updateAmount('{{ $line->key }}', $event.target.value)"
                            {{-- A morph keeps what was typed when the clamped value is the one already shown. --}}
                            wire:replace.self
                            class="w-full min-w-0 border-0 bg-transparent text-right text-[18px] font-semibold text-ink outline-none"
                        >
                        <span>kr</span>
                    </label>
                </div>
            @empty
                <p class="lbl m-0 py-[40px] text-ink-dim">{{ __('shop.empty') }}</p>
            @endforelse
        </div>

        <div class="flex shrink-0 flex-col gap-[16px] border-t border-hair-strong pt-[24px] pb-[28px] lg:pb-[36px]">
            <div class="flex items-baseline justify-between">
                <span class="lbl">{{ __('shop.sum') }}</span>
                <span class="text-[40px] font-semibold tracking-[-0.03em]">{{ $total }} kr</span>
            </div>
            @if ($lines->isNotEmpty())
                <a href="{{ route('shop.checkout') }}" wire:navigate class="lbl flex h-[56px] items-center justify-center rounded-pill bg-accent text-plate">{{ __('shop.toCheckout') }}</a>
            @endif
            <form method="dialog">
                <button class="lbl w-full cursor-pointer bg-transparent py-[10px] text-center text-ink-dim hover:text-accent">{{ __('shop.keepListening') }}</button>
            </form>
        </div>
    </dialog>
</div>
